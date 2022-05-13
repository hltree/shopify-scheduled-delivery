<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Route;
use PHPShopify\ShopifySDK;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected ShopifySDK $ShopifySDK;

    protected function initSDK(): void
    {
        $Option = new Option();
        $accessToken = $Option->getAccessToken();

        if (!$accessToken) {
            die('アクセストークンがありません');
        }
        $this->ShopifySDK = new ShopifySDK([
            'ShopUrl' => config('app.shopUrl'),
            'AccessToken' => $accessToken
        ]);
    }
}
