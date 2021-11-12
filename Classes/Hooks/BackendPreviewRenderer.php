<?php

/*
 * This file is part of TYPO3 CMS-extension backendpreviews by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace B13\Backendpreviews\Hooks;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;


/**
 * This hook adds a full Fluid Preview Rendering to the Page Layout View for content elements, thus allowing the use
 * of consistent markup across multiple content types by using the same partials for recurring parts (like lists,
 * images, etc.).
 */
class BackendPreviewRenderer implements PageLayoutViewDrawItemHookInterface
{
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
        $this->setDefaults($row);

        $tsConfig = BackendUtility::getPagesTSconfig($row['pid'])['mod.']['web_layout.']['tt_content.']['preview.']['template.'] ?? [];
        $fluidTemplateName = $row['CType'];

        if ($row['CType'] === 'list' && !empty($row['list_type'])
            && !empty($tsConfig['list.'][$row['list_type']])
        ) {
            $fluidTemplateName = $tsConfig['list.'][$row['list_type']];
        } elseif (!empty($tsConfig[$row['CType']])) {
            $fluidTemplateName = $tsConfig[$row['CType']];
        }

        $fluidConfiguration = BackendUtility::getPagesTSconfig($row['pid'])['mod.']['web_layout.']['tt_content.']['preview.']['view.'] ?? [];

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setLayoutRootPaths($fluidConfiguration['layoutRootPaths.']);
        $view->setPartialRootPaths($fluidConfiguration['partialRootPaths.']);
        $view->setTemplateRootPaths($fluidConfiguration['templateRootPaths.']);
        $view->setTemplate($fluidTemplateName);
        $view->assignMultiple($row);

        if ($view->hasTemplate()) {
            $itemContent .= $view->render();
            $drawItem = false;
        }

    }

    protected function setDefaults(&$row) {
        if ($this->getBackendUser()->recordEditAccessInternals('tt_content', $row)) {
            $urlParameters = [
                'edit' => [
                    'tt_content' => [
                        $row['uid'] => 'edit'
                    ]
                ],
                'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI').'#element-tt_content-'.$row['uid']
            ];
            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $url = (string)$uriBuilder->buildUriFromRoute('record_edit', $urlParameters);
            $return = [
                'url' => $url,
                'title' => htmlspecialchars($this->getLanguageService()->getLL('edit'))
            ];
            $row['editLink'] = $return;
        }

        $row['CType-label'] = $this->getLanguageService()->sL(
            BackendUtility::getLabelFromItemListMerged($row['pid'], 'tt_content', 'CType', $row['CType'])
        );

        // return all sys_file_reference rows
        if ($row['assets'] ?? false) {
            $row['allAssets'] = \B13\Backendpreviews\Service\FilereferenceService::resolveFilereferences('assets', 'tt_content', $row['uid']);
            $row['allAssets-visible'] = \B13\Backendpreviews\Service\FilereferenceService::countNumberOfVisibleFilereferences('assets', 'tt_content', $row['uid']);
        }
        if ($row['assets2'] ?? false) {
            $row['allAssets2'] = \B13\Backendpreviews\Service\FilereferenceService::resolveFilereferences('assets2', 'tt_content', $row['uid']);
            $row['allAssets-visible'] = \B13\Backendpreviews\Service\FilereferenceService::countNumberOfVisibleFilereferences('assets2', 'tt_content', $row['uid']);
        }
        if ($row['media'] ?? false) {
            $row['allMedia'] = \B13\Backendpreviews\Service\FilereferenceService::resolveFilereferences('media', 'tt_content', $row['uid']);
            $row['allMedia-visible'] = \B13\Backendpreviews\Service\FilereferenceService::countNumberOfVisibleFilereferences('media', 'tt_content', $row['uid']);
        }
        if ($row['image'] ?? false) {
            $row['allImages'] = \B13\Backendpreviews\Service\FilereferenceService::resolveFilereferences('image', 'tt_content', $row['uid']);
            $row['allImages-visible'] = \B13\Backendpreviews\Service\FilereferenceService::countNumberOfVisibleFilereferences('image', 'tt_content', $row['uid']);
        }

    }

    /**
     * Returns the language service
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * Create thumbnail code for record/field but not linked
     *
     * @param mixed[] $row Record array
     * @param string $table Table (record is from)
     * @param string $field Field name for which thumbnail are to be rendered.
     * @return string HTML for thumbnails, if any.
    public function getThumbCodeUnlinked($row, $table, $field)
    {
        return BackendUtility::thumbCode($row, $table, $field, '', '', null, 0, 'style="margin-right: 10px;"', '', false);
    }
     */


}
