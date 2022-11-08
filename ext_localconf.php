<?php

defined('TYPO3') or die();

if ((\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class))->getMajorVersion() < 12) {
    // extends database row and render Backend-Preview if fluidBasedPageModule is not used
    // (if fluidBasedPageModule is used "$GLOBALS['TCA']['tt_content']['ctrl']['previewRenderer']" takes effect for Backend-Preview-Rendering)
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['tx_backendpreviews'] =
        \B13\Backendpreviews\Hooks\BackendPreviewRenderer::class;
}
// if TYPO3 > 11 "$GLOBALS['TCA']['tt_content']['ctrl']['previewRenderer']" is always used for Backend-Preview-Rendering
// extending the database row is handled by B13\Backendpreviews\Listener\PageContentPreviewRendering

if (
    (\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class))->getMajorVersion() < 12 ||
    (\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Package\PackageManager::class)->isPackageActive('fontawesome_provider'))
) {
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
}
