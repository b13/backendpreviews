<?php

declare(strict_types=1);

namespace B13\Backendpreviews\ViewHelpers;

/*
 * This file is part of TYPO3 CMS-extension backendpreviews by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\WorkspaceRestriction;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Class GetDatabaseRecordViewHelper
 *
 * ViewHelper to get database records by uid.
 */
class GetDatabaseRecordViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * Defaults
     */
    const DEFAULT_TABLE = 'tt_content';
    const DEFAULT_SPLIT_CHAR = ',';

    /**
     * Initialize arguments.
     *
     * @api
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'table',
            'string',
            'The database table to fetch a single record from. Default: tt_content',
            false,
            'tt_content'
        );
        $this->registerArgument(
            'uidList',
            'mixed',
            'The record uid(s).',
            true
        );
        $this->registerArgument(
            'splitChar',
            'string',
            'Character to split up the string. Default: ,',
        );
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return array
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): array
    {
        $table = $arguments['table'] ?? self::DEFAULT_TABLE;
        $queryBuilder = self::getQueryBuilder($table);
        $splitChar = $arguments['splitChar'] ?? self::DEFAULT_SPLIT_CHAR;
        $uids = GeneralUtility::intExplode($splitChar, (string)$arguments['uidList'], true);
        if ($uids === []) {
            return [];
        }

        $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($uids, Connection::PARAM_INT_ARRAY))
            );
        if ((new Typo3Version())->getMajorVersion() > 12) {
            $queryBuilder->getConcreteQueryBuilder()->addOrderBy('FIELD(uid,' . implode(',', $uids ) . ')');
        } else {
            $queryBuilder->add('orderBy', 'FIELD(uid,' . implode(',', $uids ) . ')');
        }
        return $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();
    }

    protected static function getQueryBuilder(string $table): QueryBuilder
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
            ->add(GeneralUtility::makeInstance(WorkspaceRestriction::class, (int)$GLOBALS['BE_USER']->workspace));
        return $queryBuilder;
    }
}
