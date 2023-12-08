<?php

defined('TYPO3') or die();

(function () {
    if ((\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class))->getMajorVersion() < 12) {
        $GLOBALS['TBE_STYLES']['skins']['backendpreviews']['name'] = 'backendpreviews';
        $GLOBALS['TBE_STYLES']['skins']['backendpreviews']['stylesheetDirectories'] = [
            'EXT:backendpreviews/Resources/Public/Backend/Css/Skin/',
        ];
    }
})();
