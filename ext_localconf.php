<?php

defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['1615885039708'] =
    \B13\Backendpreviews\Hooks\BackendPreviewRenderer::class;

$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
    'exclamation-triangle',
    \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
    [
        'name' => 'exclamation-triangle',
    ]
);
$iconRegistry->registerIcon(
    'exclamation-circle',
    \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
    [
        'name' => 'exclamation-circle',
    ]
);
