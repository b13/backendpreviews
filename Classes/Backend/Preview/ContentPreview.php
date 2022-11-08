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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ContentPreview
{
    public function render(array $row): ?string
    {
        $previewConfiguration = BackendUtility::getPagesTSconfig($row['pid'])['mod.']['web_layout.']['tt_content.']['preview.'] ?? [];
        if (!$previewConfiguration) {
            // Early return in case no preview configuration can be found
            return null;
        }

        $fluidConfiguration = $previewConfiguration['view.'] ?? [];
        if (!$fluidConfiguration) {
            // Early return in case no fluid template configuration can be found
            return null;
        }

        $templateConfiguration = $previewConfiguration['template.'] ?? [];
        if ($row['CType'] === 'list' && !empty($row['list_type']) && !empty($templateConfiguration['list.'][$row['list_type']])) {
            $fluidTemplateName = $templateConfiguration['list.'][$row['list_type']];
        } elseif (!empty($templateConfiguration[$row['CType']])) {
            $fluidTemplateName = $templateConfiguration[$row['CType']];
        } else {
            $fluidTemplateName = $row['CType'];
        }

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setLayoutRootPaths($fluidConfiguration['layoutRootPaths.'] ?? []);
        $view->setPartialRootPaths($fluidConfiguration['partialRootPaths.'] ?? []);
        $view->setTemplateRootPaths($fluidConfiguration['templateRootPaths.'] ?? []);
        $view->setTemplate($fluidTemplateName);
        $view->assignMultiple($row);

        if ($view->hasTemplate()) {
            return $view->render();
        }
        return null;
    }
}
