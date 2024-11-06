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
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StandardContentPreviewRenderer extends \TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer
{
    protected string $previewContent = '';

    public function renderPageModulePreviewHeader(GridColumnItem $item): string
    {
        // We do not add any header output by default:
        // This removes the default output of header, subheader, date, header_layout
        // Only exception:  If this would be the only content of a preview,
        //                  we will still render it.
        $this->setPreviewContent($item);
        if (!empty($this->previewContent)) {
            return '';
        }
        return parent::renderPageModulePreviewHeader($item);
    }

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $this->setPreviewContent($item);
        return $this->previewContent;
    }

    private function setPreviewContent(GridColumnItem $item): void
    {
        if (!empty($this->previewContent)) {
            return;
        }
        $this->previewContent = $this->createPageModulePreviewContent($item);
    }

    private function createPageModulePreviewContent(GridColumnItem $item): string
    {
        $record = $item->getRecord();
        $contentPreview = GeneralUtility::makeInstance(ContentPreview::class);
        $content = $contentPreview->render($record);
        if ($content !== null) {
            return $content;
        }
        return parent::renderPageModulePreviewContent($item);
    }
}
