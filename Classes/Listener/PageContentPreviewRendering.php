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

class PageContentPreviewRendering
{
    protected DatabaseRowService $databaseRowService;

    public function __construct(DatabaseRowService $databaseRowService)
    {
        $this->databaseRowService = $databaseRowService;
    }

    public function __invoke(PageContentPreviewRenderingEvent $event): void
    {
        $record = $event->getRecord();
        $record = $this->databaseRowService->extendRow($record);
        $event->setRecord($record);
    }
}
