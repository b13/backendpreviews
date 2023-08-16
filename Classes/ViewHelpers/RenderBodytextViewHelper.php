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
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Class RenderBodytextViewHelper
 *
 * View helper to process bodytext field values for backend preview, similar to PageLayoutView:renderText().
 */
class RenderBodytextViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * Default crop value, used as fallback.
     */
    const DEFAULT_CROP_VALUE = 1500;

    const DEFAULT_KEEP_TAGS_LIST = 'ol, ul, li';

    /**
     * Initialize arguments.
     *
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument(
            'value',
            'string',
            'The input value. If not given, the evaluated child nodes will be used.',
            false,
            null
        );
        $this->registerArgument(
            'crop',
            'int',
            'Change the default crop value to this number of characters. Set to 0 to disable. Default: 1500.'
        );
        $this->registerArgument(
            'keepTags',
            'string',
            'List of tags to keep (example: "ol, li"). Set to "none" to remove all tags. Default: "ol, ul, li".',
        );
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        if ($arguments['crop'] === 0) {
            $crop = 0;
        } else {
            $crop = $arguments['crop'] ? $arguments['crop'] : self::DEFAULT_CROP_VALUE;
        }
        $value = $arguments['value'];
        $keepTags = $arguments['keepTags'] ?: self::DEFAULT_KEEP_TAGS_LIST;

        if ($value === null) {
            $value = $renderChildrenClosure();
        }

        if ($value) {
            $value = strip_tags($value, $keepTags);
            $value = GeneralUtility::fixed_lgd_cs($value, $crop);
            if ($keepTags !== null) {
                return nl2br(trim($value));
            }
            return nl2br(htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8', false));
        }

        return '';
    }
}
