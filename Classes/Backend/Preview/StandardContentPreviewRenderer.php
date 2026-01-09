<?php

declare(strict_types=1);

namespace B13\Backendpreviews\Backend\Preview;

/*
 * This file is part of TYPO3 CMS-extension backendpreviews by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StandardContentPreviewRenderer extends \TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer
{
    public function renderPageModulePreviewHeader(GridColumnItem $item): string
    {
        // we do not add any output by default
        // this removes the default output of header, subheader, date, header_layout
        // If this would be the only content of a preview, we will still render the
        // preview Header in renderPageModulePreviewContent()
        return '';
    }

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $record = $item->getRecord();
        $context = $item->getContext();
        $contentPreview = GeneralUtility::makeInstance(ContentPreview::class);
        if ($record instanceof RecordInterface) {
            $content = $contentPreview->render($record, $context);
        } else {
            $content = $contentPreview->renderLegacy($record);
        }
        if ($content !== null) {
            return $content;
        }
        // Fallback to renderPageModulePreviewHeader() if no content would be rendered otherwise
        // Some core CEs (e.g. CType "header") do only render header and subheader in their preview.
        return parent::renderPageModulePreviewContent($item) ?: parent::renderPageModulePreviewHeader($item);
    }
}
