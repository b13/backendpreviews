<?php

declare(strict_types=1);

namespace B13\Backendpreviews\Listener;

/*
 * This file is part of TYPO3 CMS-extension backendpreviews by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use B13\Backendpreviews\Service\DatabaseRowService;
use TYPO3\CMS\Backend\View\Event\PageContentPreviewRenderingEvent;
use TYPO3\CMS\Core\Information\Typo3Version;

class PageContentPreviewRendering
{
    protected DatabaseRowService $databaseRowService;

    public function __construct(DatabaseRowService $databaseRowService)
    {
        $this->databaseRowService = $databaseRowService;
    }

    public function __invoke(PageContentPreviewRenderingEvent $event): void
    {
        if ((new Typo3Version())->getMajorVersion() > 13) {
            return;
        }
        $record = $event->getRecord();
        $record = $this->databaseRowService->extendRow($record);
        $event->setRecord($record);
    }
}
