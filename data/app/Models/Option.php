<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getAccessToken(string $shopUrl) :string
    {
        $accessToken = '';

        $where = $this::whereNotNull('access_token')->where('access_token', '!=', '')->where('shop_url', $shopUrl);
        if ($where->exists()) {
            $takeOne = $where->get()->take(1)[0];
            $accessToken = $takeOne->getAttribute('access_token');
        }

        return $accessToken;
    }

    public static function reset(string $shopUrl)
    {
        $records = self::whereNotNull('access_token')->where('access_token', '!=', '')->where('shop_url', $shopUrl);
        if ($records->exists()) {
            $records->delete();
        }
    }
}
