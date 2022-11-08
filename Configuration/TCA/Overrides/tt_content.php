<?php

defined('TYPO3') or die();

// for fluidBasedPageModule enabled (or TYPO3 > 12)
$GLOBALS['TCA']['tt_content']['ctrl']['previewRenderer'] = \B13\Backendpreviews\Backend\Preview\StandardContentPreviewRenderer::class;
