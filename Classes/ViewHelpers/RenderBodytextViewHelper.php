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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class RenderBodytextViewHelper
 *
 * View helper to process bodytext field values for backend preview, similar to PageLayoutView:renderText().
 */
class RenderBodytextViewHelper extends AbstractViewHelper
{/**
     * Default crop value, used as fallback.
     */
    const DEFAULT_CROP_VALUE = 1500;

    const DEFAULT_KEEP_TAGS_LIST = ['ol', 'ul', 'li'];

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'value',
            'string',
            'The input value. If not given, the evaluated child nodes will be used.',
        );
        $this->registerArgument(
            'crop',
            'int',
            'Change the default crop value to this number of characters. Set to 0 to disable. Default: 1500.'
        );
        $this->registerArgument(
            'keepTags',
            'string',
            'List of tags to keep (example: "ol, li"). Set to "none" to remove all tags. Default: "[ol, ul, li]".',
        );
    }

    public function render()
    {
        if (($this->arguments['crop'] ?? 0) === 0) {
            $crop = 0;
        } else {
            $crop = $this->arguments['crop'] ?: self::DEFAULT_CROP_VALUE;
        }
        $value = (string)($this->arguments['value'] ?? $this->renderChildren());
        if ($value === '') {
            return '';
        }
        $keepTags = $this->arguments['keepTags'] ? explode(',', str_replace(' ', '', $this->arguments['keepTags'])): self::DEFAULT_KEEP_TAGS_LIST;
        $value = strip_tags($value, $keepTags);
        $value = GeneralUtility::fixed_lgd_cs($value, $crop);
        return nl2br(trim($value));
    }
}
