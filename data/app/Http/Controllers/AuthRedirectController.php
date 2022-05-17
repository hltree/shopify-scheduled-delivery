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
    public function index()
    {
        new ShopifySDK([
            'ShopUrl' => config('app.shopUrl'),
            'ApiKey' => config('app.apiKey'),
            'SharedSecret' => config('app.secretKey')
        ]);
        try {
            $accessToken = AuthHelper::getAccessToken();
        } catch (\Exception $Exception) {
            if ($Exception instanceof \PHPShopify\Exception\CurlException) {
                echo 'おそらくセッションの有効期限切れです。sendAuthorizeからやり直してください';
                die();
            }
        }

        /**
         * アプリの再インストール等に対応するため、アクセストークンは再登録させるようにする
         */
        Option::reset();
        DB::beginTransaction();
        try {
            if (!isset($accessToken)) throw new \Exception('トークンを取得できませんでした');
            Option::create([
                'access_token' => $accessToken
            ]);
            DB::commit();
        } catch (\Exception $Exception) {
            DB::rollBack();
            echo $Exception->getMessage();
            die();
        }

        $config = ShopifySDK::$config;
        // ShopifySDKオブジェクト 取得
        $shopify = new ShopifySDK([
            'ShopUrl' => $config['ShopUrl'],
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
            dd($e->getMessage());
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

        // app handle 取得
        $graphQL =
            <<<Query
query {
  app {
    handle
  }
}
Query;
        $appResponse = $shopify->GraphQL->post($graphQL);
        header('Location: ' . $config['AdminUrl'] . 'apps/' . $appResponse['data']['app']['handle']);

        exit;
    }
}
