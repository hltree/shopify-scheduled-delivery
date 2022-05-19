<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use PHPShopify\AuthHelper;
use PHPShopify\ShopifySDK;

class AuthRedirectController extends Controller
{
    protected bool $auth = false;

    public function index()
    {
        new ShopifySDK([
            'ShopUrl' => $this->cookieShop,
            'ApiKey' => config('app.apiKey'),
            'SharedSecret' => config('app.secretKey')
        ]);
        try {
            $accessToken = AuthHelper::getAccessToken();
        } catch (\Exception $Exception) {
            if ($Exception instanceof \PHPShopify\Exception\CurlException) {
                abort('400', 'セッションの有効期限切れです。はじめからやり直してください');
            }
            abort('400', $Exception->getMessage());
        }

        /**
         * アプリの再インストール等に対応するため、アクセストークンは再登録させるようにする
         */
        Option::reset($this->cookieShop);
        DB::beginTransaction();
        try {
            if (!isset($accessToken)) throw new \Exception();
            Option::create([
                'access_token' => $accessToken,
                'shop_url' => $this->cookieShop
            ]);
            DB::commit();
        } catch (\Exception $Exception) {
            DB::rollBack();
            abort('400', 'トークンを保存できませんでした。はじめからやり直してください');
        }

        $config = ShopifySDK::$config;
        // ShopifySDKオブジェクト 取得
        $shopify = new ShopifySDK([
            'ShopUrl' => $this->cookieShop,
            'AccessToken' => $accessToken
        ]);

        /**
         * @returns array
         */
        $shopifyThemes = $shopify->Theme->get();

        try {
            $liquidFile = File::get(resource_path('shopify/snippets/form-scheduled-delivery.liquid'));
            $jsFile = File::get(resource_path('shopify/assets/form-scheduled-delivery.js'));
        } catch (\Exception $e) {
            abort(400);
        }

        if (!isset($shopifyThemes) || !is_array($shopifyThemes)) die('テーマがないようです。作成してから再度実行してください');
        foreach ($shopifyThemes as $theme) {
            $shopify->Theme($theme['id'])->Asset->put([
                'key' => 'assets/form-scheduled-delivery.js',
                'value' => $jsFile
            ]);
            $shopify->Theme($theme['id'])->Asset->put([
                'key' => 'snippets/form-scheduled-delivery.liquid',
                'value' => $liquidFile
            ]);
        }

        return redirect(route('home'))->with('success', __('インストールを正常に完了しました'));
    }
}
