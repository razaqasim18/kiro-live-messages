<?php // Code within app\Helpers\SettingHelper.php
namespace App\Helpers;

use App\Models\Setting;


class SettingHelper
{

    public static function getSettingValueByName($slug)
    {
        $response = Setting::where('name', $slug)->first();
        return ($response) ? $response?->value : "0";
    }

    public static function getCoinsdeduction($coins)
    {
        $commissionPercent = SettingHelper::getSettingValueByName('gift_coins_commission') ?? 0;

        $netCoins = $coins - (($coins * $commissionPercent) / 100);
        return floor($netCoins);
    }
}
