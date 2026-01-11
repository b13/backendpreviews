<?php

declare(strict_types=1);

namespace B13\Backendpreviews\Service;

/*
 * This file is part of TYPO3 CMS-extension backendpreviews by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DatabaseRowService implements SingletonInterface
{
    protected FileRepository $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function getAdditionalDataForView(RecordInterface $record): array
    {
        $data = [];
        if ($this->getBackendUser()->recordEditAccessInternals($record->getMainType(), $record)) {
            $urlParameters = [
                'edit' => [
                    'tt_content' => [
                        $record->getUid() => 'edit',
                    ],
                ],
                'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI') . '#element-tt_content-' . $record->getUid(),
            ];
            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $url = (string)$uriBuilder->buildUriFromRoute('record_edit', $urlParameters);
            $return = [
                'url' => $url,
                'title' => htmlspecialchars($this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:edit')),
            ];
            $data['editLink'] = $return;
        }

        $data['CType-label'] = $this->getLanguageService()->sL(
            BackendUtility::getLabelFromItemListMerged($record->getPid(), 'tt_content', 'CType', $record->get('CType'))
        );
        return $data;
    }

    public function extendRow(array $row): array
    {
        if ($this->getBackendUser()->recordEditAccessInternals('tt_content', $row)) {
            $urlParameters = [
                'edit' => [
                    'tt_content' => [
                        $row['uid'] => 'edit',
                    ],
                ],
                'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI') . '#element-tt_content-' . $row['uid'],
            ];
            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $url = (string)$uriBuilder->buildUriFromRoute('record_edit', $urlParameters);
            $return = [
                'url' => $url,
                'title' => htmlspecialchars($this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:edit')),
            ];
            $row['editLink'] = $return;
        }

        $row['CType-label'] = $this->getLanguageService()->sL(
            BackendUtility::getLabelFromItemListMerged($row['pid'], 'tt_content', 'CType', $row['CType'])
        );

        if ($row['CType'] === 'list') {
            $row['list_type-label'] = $this->getLanguageService()->sL(
                BackendUtility::getLabelFromItemListMerged($row['pid'], 'tt_content', 'list_type', $row['list_type'])
            );
        }
        if (!empty($row['pi_flexform'])) {
            $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
            $row['pi_flexform_transformed'] = $flexFormService->convertFlexFormContentToArray($row['pi_flexform']);
        }

        // return all sys_file_reference rows
        // automatic resolving of all type=file, only works with TYPO3 v12+
        $foundOne = false;
        foreach ($GLOBALS['TCA']['tt_content']['columns'] as $fieldName => $fieldConfig) {
            if ($fieldConfig['config']['type'] === 'file') {
                $variableName = GeneralUtility::underscoredToUpperCamelCase($fieldName);
                if ($fieldName === 'image') {
                    $variableName = 'Images';
                }
                if ($row[$fieldName] ?? false) {
                    $row['all' . $variableName] = $this->fileRepository->findByRelation('tt_content', $fieldName, $row['uid']);
                    $row['all' . $variableName . '-visible'] = $this->countVisibleFileReferences($row['all' . $variableName]);
                }
                $foundOne = true;
            }
        }
        if (!$foundOne) {
            if ($row['assets'] ?? false) {
                $row['allAssets'] = $this->fileRepository->findByRelation('tt_content', 'assets', $row['uid']);
                $row['allAssets-visible'] = $this->countVisibleFileReferences($row['allAssets']);
            }
            if ($row['assets2'] ?? false) {
                $row['allAssets2'] = $this->fileRepository->findByRelation('tt_content', 'assets2', $row['uid']);
                $row['allAssets2-visible'] = $this->countVisibleFileReferences($row['allAssets2']);
            }
            if ($row['media'] ?? false) {
                $row['allMedia'] = $this->fileRepository->findByRelation('tt_content', 'media', $row['uid']);
                $row['allMedia-visible'] = $this->countVisibleFileReferences($row['allMedia']);
            }
            if ($row['image'] ?? false) {
                $row['allImages'] = $this->fileRepository->findByRelation('tt_content', 'image', $row['uid']);
                $row['allImages-visible'] = $this->countVisibleFileReferences($row['allImages']);
            }
        }
        return $row;
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function countVisibleFileReferences(array $references): int
    {
        $cnt = 0;
        /** @var FileReference $reference */
        foreach ($references as $reference) {
            if ((int)$reference->getProperty('hidden') === 0) {
                $cnt++;
            }
        }
        return $cnt;
    }
}
