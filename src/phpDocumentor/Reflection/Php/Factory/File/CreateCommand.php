<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory\File;

use phpDocumentor\Reflection\File;
use phpDocumentor\Reflection\Middleware\Command;
use phpDocumentor\Reflection\Php\Factory\ContextStack;
use phpDocumentor\Reflection\Php\StrategyContainer;

/**
 * File Create command is used by the File Factory Strategy.
 * The command is passed to the registered middle ware classes.
 */
final class CreateCommand implements Command
{
    /**
     * Initializes this command.
     */
    public function __construct(private readonly ContextStack $context, private readonly File $file, private readonly StrategyContainer $strategies)
    {
    }

    /**
     * Returns the strategyContainer in this command context.
     */
    public function getStrategies(): StrategyContainer
    {
        return $this->strategies;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function getContext(): ContextStack
    {
        return $this->context;
    }
}
