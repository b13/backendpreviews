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

use B13\Backendpreviews\Service\DatabaseRowService;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\PageLayoutContext;
use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException;

class ContentPreview
{
    public function render(RecordInterface $record, PageLayoutContext $context): ?string
    {
        $previewConfiguration = $this->getPreviewConfiguration((int)$record->getPid());
        if ($previewConfiguration === null) {
            return null;
        }

        $cType = $record->getRecordType();
        $templateConfiguration = $previewConfiguration['template.'] ?? [];
        $fluidTemplateName = !empty($templateConfiguration[$cType]) ? $templateConfiguration[$cType] : $cType;

        $view = $this->createView($previewConfiguration['view.'], $context);
        $data = GeneralUtility::makeInstance(DatabaseRowService::class)->getAdditionalDataForView($record, $context);
        $view->assignMultiple($data);
        $view->assign('record', $record);
        try {
            return $view->render($fluidTemplateName);
        } catch (InvalidTemplateResourceException) {
        }
        return null;
    }

    /**
     * Render path for TYPO3 v13, where the page module still passes the content element as an array
     * instead of a RecordInterface (see Breaking-92434).
     *
     * @param array<string, mixed> $row
     */
    public function renderLegacy(array $row, PageLayoutContext $context): ?string
    {
        $previewConfiguration = $this->getPreviewConfiguration((int)$row['pid']);
        if ($previewConfiguration === null) {
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

        $view = $this->createView($previewConfiguration['view.'], $context);
        $view->assignMultiple($row);
        try {
            return $view->render($fluidTemplateName);
        } catch (InvalidTemplateResourceException) {
        }
        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getPreviewConfiguration(int $pid): ?array
    {
        $previewConfiguration = BackendUtility::getPagesTSconfig($pid)['mod.']['web_layout.']['tt_content.']['preview.'] ?? [];
        // Early return in case no preview (or fluid template) configuration can be found
        if (!$previewConfiguration || empty($previewConfiguration['view.'])) {
            return null;
        }
        return $previewConfiguration;
    }

    /**
     * @param array<string, mixed> $fluidConfiguration
     */
    protected function createView(array $fluidConfiguration, PageLayoutContext $context): ViewInterface
    {
        $viewFactory = GeneralUtility::makeInstance(ViewFactoryInterface::class);
        return $viewFactory->create(
            new ViewFactoryData(
                $fluidConfiguration['templateRootPaths.'] ?? null,
                $fluidConfiguration['partialRootPaths.'] ?? null,
                $fluidConfiguration['layoutRootPaths.'] ?? null,
                null,
                $context->getCurrentRequest()
            )
        );
    }
}
