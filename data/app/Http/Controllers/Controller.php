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

    protected bool $auth = true;
    protected ShopifySDK $ShopifySDK;
    protected string $cookieShop = '';
    protected string $cookieHmac = '';

    public function __construct()
    {
        $this->setCookie();
        $this->verifyHmac();
        $this->initSDK();
    }

    protected function View(string $view = '', array $data = [])
    {
        $data = $data + [
                'installed' => $this->installed()
            ];
        return view($view, $data);
    }

    protected function initSDK(): void
    {
        $Option = new Option();

        try {
            $accessToken = $Option->getAccessToken($this->cookieShop);

            $this->ShopifySDK = new ShopifySDK([
                'ShopUrl' => $this->cookieShop,
                'AccessToken' => $accessToken
            ]);

            /**
             * 接続確認のためにテーマを取得してみる
             */
            $this->ShopifySDK->Theme->get();
        } catch (\Exception $e) {
            if ($accessToken) {
                Option::reset($this->cookieShop);
            }
            if (true === $this->auth) abort(401, __('まだインストールされていないようです。トップページからインストールしてください'));
        }
    }

    protected function setCookie()
    {
        session_start();

        $hmac = '';
        if (isset($_GET['hmac'])) {
            $hmac = $_GET['hmac'];
        }
        $shop = '';
        if (isset($_GET['shop'])) {
            $shop = $_GET['shop'];
        }

        $hour = time() + 60 * 60;

        if ($hmac) {
            setcookie('shopify-scheduled-delivery-val-hmac', $hmac, $hour);
        }
        if ($shop) {
            setcookie('shopify-scheduled-delivery-val-shop', $shop, $hour);
        }

        try {
            if ($hmac || $shop) {
                $this->cookieShop = $shop;
                $this->cookieHmac = $hmac;
            } else {
                $this->cookieShop = $_COOKIE['shopify-scheduled-delivery-val-shop'];
                $this->cookieHmac = $_COOKIE['shopify-scheduled-delivery-val-hmac'];
            }
        } catch (\Exception $e) {
            abort(401, __('Shopifyからアクセスしてください'));
        }
    }

    private function verifyHmac()
    {
        try {
            $ar = [];
            $hmac = $this->cookieHmac;

            foreach ($_GET as $key => $value) {
                $key = str_replace("%", "%25", $key);
                $key = str_replace("&", "%26", $key);
                $key = str_replace("=", "%3D", $key);
                $value = str_replace("%", "%25", $value);
                $value = str_replace("&", "%26", $value);

                $ar[] = $key . "=" . $value;
            }

            $str = implode('&', $ar);
            $ver_hmac = hash_hmac('sha256', $str, config('app.secretKey'), false);

            if ($ver_hmac === $hmac) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            abort(401);
        }
    }

    private function installed(): bool
    {
        $Option = new Option();
        if ($Option->getAccessToken($this->cookieShop)) {
            return true;
        }

        return false;
    }
}
