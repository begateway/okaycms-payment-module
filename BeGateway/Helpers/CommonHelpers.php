<?php
namespace Okay\Modules\OkayCMS\BeGateway\Helpers;

use BeGateway\Settings;

class CommonHelpers
{
    public static function initBeGatewaySettings ($settings){
        Settings::$gatewayBase = $settings['domain-gateway'] ? 'https://' . $settings['domain-gateway'] : Settings::$gatewayBase;
        Settings::$checkoutBase = $settings['domain-checkout'] ? 'https://' . $settings['domain-checkout'] : Settings::$checkoutBase;
        Settings::$shopId = $settings['shop-id'];
        Settings::$shopKey = $settings['secret-key'];
    }

    public static function initDictionary ($currentLangLabel) {
        $lang = [];
        $currVocabularyFile = $_SERVER['DOCUMENT_ROOT']. '/Okay/Modules/OkayCMS/BeGateway/Backend/lang/'
            . $currentLangLabel . '.php';

        if(file_exists($currVocabularyFile)) {
            $vocabularyFile = $currVocabularyFile;
        } else {
            $vocabularyFile = $_SERVER['DOCUMENT_ROOT']. '/Okay/Modules/OkayCMS/BeGateway/Backend/lang/en.php';
        }
        if(file_exists($vocabularyFile))
            require_once ($vocabularyFile);
        return $lang;
    }
}
