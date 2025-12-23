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

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Class ExplodeListViewHelper
 *
 * View helper to explode a comma-separated list (like a uid list you would find as value in tt_content field "pages"
 * into an array.
 */
class ExplodeListViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * Default split char, used as fallback.
     */
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
            'value',
            'string',
            'The input value. If non is given, the evaluated child nodes will be used.',
            false,
            null
        );
        $this->registerArgument(
            'splitChar',
            'string',
            'Character to split up the string.',
        );
        $this->registerArgument(
            'splitNL',
            'boolean',
            'Split newlines. If this is true, splitChar is ignored.',
            '',
            false
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
        if ($arguments['splitNL'] !== false) {
            $splitChar = PHP_EOL;
        } else {
            $splitChar = $arguments['splitChar'] ?? self::DEFAULT_SPLIT_CHAR;
        }
        $value = $arguments['value'] ?? $renderChildrenClosure();
        return explode($splitChar, (string)$value);
    }
}
