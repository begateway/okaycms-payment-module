<?php
namespace Okay\Modules\OkayCMS\BeGateway\Helpers;

use BeGateway\Settings;
use Okay\Core\SmartyPlugins\Func;

class CommonHelpers
{
    public static function initBeGatewaySettings($settings)
    {
        Settings::$checkoutBase = $settings['domain-checkout'] ? 'https://' . $settings['domain-checkout'] : Settings::$checkoutBase;
        Settings::$shopId = $settings['shop-id'];
        Settings::$shopKey = $settings['secret-key'];
        Settings::$shopPubKey = $settings['public-key'] ? : '';
    }

    public static function initDictionary($currentLangLabel, $logger = null)
    {
        $lang = [];
        if ($logger){
            $logger->info('begateway:initDictionary: Current lang is: ' . $currentLangLabel . '.');
        }
        $pathToDictionaryFiles = dirname(__DIR__). '/Backend/lang/';
        $currDictionaryFile = $pathToDictionaryFiles . $currentLangLabel . '.php';
        if ($logger){
            $logger->info('begateway:initDictionary: Dictionary path is: ' . $currDictionaryFile . '.');
        }

        if(is_readable($currDictionaryFile)) {
            $dictionaryFile = $currDictionaryFile;
            if ($logger) {
                $logger->info('begateway:initDictionary: Dictionary file has been found!');
            }
        } else {
            $dictionaryFile = $_SERVER['DOCUMENT_ROOT']. '/Okay/Modules/OkayCMS/BeGateway/Backend/lang/en.php';
            if ($logger){
                $logger->info('begateway:initDictionary: Dictionary not found! default will be used: ' . $currDictionaryFile . '.');
            }
        }
        if(file_exists($dictionaryFile))
            require ($dictionaryFile);

        if ($logger) {
            $logger->info('begateway:initDictionary: $lang=\'' . print_r($lang['repeat_request'], true) . '\'');
        }
        return $lang;
    }

    public static function mapLangLabelForBeGateway($langLabel)
    {
        $mapTable = array(
            'BY' => 'BE',
            'by' => 'be',
            'UA' => 'UK',
            'ua' => 'uk'
        );
        return $mapTable[$langLabel] ? : $langLabel;
    }

    public static function mapLangLabelForOkayCMS($langLabel)
    {
        $mapTable = array(
            'BE' => 'BY',
            'be' => 'by',
            'UK' => 'UA',
            'uk' => 'ua'
        );
        return $mapTable[$langLabel] ? : $langLabel;
    }
}
