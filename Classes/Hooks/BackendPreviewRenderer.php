<?php

declare(strict_types=1);

namespace B13\Backendpreviews\Hooks;

/*
 * This file is part of TYPO3 CMS-extension backendpreviews by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use B13\Backendpreviews\Backend\Preview\ContentPreview;
use B13\Backendpreviews\Service\DatabaseRowService;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Configuration\Features;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This hook adds a full Fluid Preview Rendering to the Page Layout View for content elements, thus allowing the use
 * of consistent markup across multiple content types by using the same partials for recurring parts (like lists,
 * images, etc.).
 */
class BackendPreviewRenderer implements PageLayoutViewDrawItemHookInterface
{
    protected DatabaseRowService $databaseRowService;

    public function __construct(DatabaseRowService $databaseRowService)
    {
        $this->databaseRowService = $databaseRowService;
    }

    /**
     * Preprocesses the preview rendering of a content element of any type
     *
     * @param \TYPO3\CMS\Backend\View\PageLayoutView $parentObject Calling parent object
     * @param bool $drawItem Whether to draw the item using the default functionality
     * @param string $headerContent Header content
     * @param string $itemContent Item content
     * @param array $row Record row of tt_content
     */
    public function preProcess(
        PageLayoutView &$parentObject,
        &$drawItem,
        &$headerContent,
        &$itemContent,
        array &$row
    ) {
        $row = $this->databaseRowService->extendRow($row);
        if ((GeneralUtility::makeInstance(Features::class))->isFeatureEnabled('fluidBasedPageModule') === false) {
            $contentPreview = GeneralUtility::makeInstance(ContentPreview::class);
            $content = $contentPreview->render($row);
            if ($content !== null) {
                $itemContent .= $content;
                $drawItem = false;
            }
        }
    }
}
