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

use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Package\PackageManager;

class BootCompleted
{
    public function __construct(protected IconRegistry $iconRegistry, protected PackageManager $packageManager)
    {
    }

    public function __invoke(BootCompletedEvent $event): void
    {
        if (!$this->packageManager->isPackageActive('fontawesome_provider')) {
            return;
        }
        $this->iconRegistry->registerIcon(
            'exclamation-triangle',
            \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
            [
                'name' => 'exclamation-triangle',
            ]
        );
        $this->iconRegistry->registerIcon(
            'exclamation-circle',
            \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
            [
                'name' => 'exclamation-circle',
            ]
        );
    }
}
