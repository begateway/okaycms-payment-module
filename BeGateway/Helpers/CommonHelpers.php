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

    public static function initDictionary($currentLangLabel)
    {
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
