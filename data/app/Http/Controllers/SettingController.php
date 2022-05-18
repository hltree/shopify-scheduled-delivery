<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function index()
    {
        $this->initSDK();
        /**
         * @returns array
         */
        $shopifyThemes = $this->ShopifySDK->Theme->get();

        $viewParams = [];
        if (!isset($shopifyThemes) || !is_array($shopifyThemes)) {
            $viewParams['cEroors'][] = __('有効なテーマがありません');
        } else {
            foreach ($shopifyThemes as $shopifyTheme) {
                $viewParams['themes'][$shopifyTheme['id']] = $this->ShopifySDK->Theme($shopifyTheme['id'])->get()['name'];
            }
        }

        return view('setting', $viewParams);
    }

    public function edit(string $themeId)
    {
        $this->initSDK();
        /**
         * @returns array
         */
        $shopifyTheme = $this->ShopifySDK->Theme($themeId);

        $viewParams = [
            'themeId' => $themeId
        ];

        try {
            $jsFile = $shopifyTheme->Asset->get([
                'asset[key]' => 'assets/form-scheduled-delivery.js'
            ]);
            $splitJsFile = preg_split("/(\r\n|\r|\n)/", $jsFile['asset']['value']);
            $values = [];
            $areaFlatPickr = false;
            foreach ($splitJsFile as $line) {
                if (1 === preg_match('/area_flatpickr_e/', $line)) $areaFlatPickr = false;
                if (true === $areaFlatPickr) $values[] = $line;
                if (1 === preg_match('/area_flatpickr_s/', $line)) $areaFlatPickr = true;
            }

            $flatPickrJs = implode("\n", $values);
            $flatPickrJs = str_replace('disable', 'defaultDate', $flatPickrJs);
            $viewParams['flatPickr'] = $flatPickrJs;
        } catch (\Exception $e) {
            $viewParams['cEroors'][] = __('テーマは存在しますか？再インストールが必要な場合は');
        }

        return view('setting-edit', $viewParams);
    }

    public function update(string $themeId, Request $request)
    {
        $viewParams = [];

        $this->initSDK();

        try {
            /**
             * @returns array
             */
            $shopifyTheme = $this->ShopifySDK->Theme($themeId);

            $closeDays = $request->get('close_days');
            if ($closeDays) {
                $closeDaysArray = explode(',', $closeDays);
            }
            $jsFile = File::get(resource_path('shopify/assets/form-scheduled-delivery.js'));

            $splitJsFile = preg_split("/(\r\n|\r|\n)/", $jsFile);
            $newFileLines = $splitJsFile;

            $areaFlatPickr = false;
            foreach ($splitJsFile as $number => $line) {
                if (true === $areaFlatPickr) {
                    if (isset($closeDaysArray)) {
                        $newFileLines[$number] .= "\ndisable: [";
                        foreach ($closeDaysArray as $day) {
                            $newFileLines[$number] .= '"' . $day . '",';
                        }
                        $newFileLines[$number] .= '],';
                    }
                    $areaFlatPickr = false;
                }
                if (1 === preg_match('/area_flatpickr_s/', $line)) $areaFlatPickr = true;
            }

            $newFile = implode("\n", $newFileLines);

            $shopifyTheme->Asset->put([
                'key' => 'assets/form-scheduled-delivery.js',
                'value' => $newFile
            ]);
        } catch (\Exception $e) {}

        return redirect(route('setting.edit', ['themeId' => $themeId]))->with('success', __('正常に処理されました'));
    }
}
