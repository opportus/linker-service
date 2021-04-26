<?php

/**
 * This file is part of the opportus/linker-service project.
 *
 * Copyright (c) 2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\LinkerService\Exception;

use Throwable;

/**
 * @package Opportus\LinkerService\Exception
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/linker-service/blob/master/LICENSE MIT
 */
class InvalidOperationException extends Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param null|Throwable $previous
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        $message = \sprintf(
            'Operation is invalid. %s',
            $message
        );

        parent::__construct($message, $code, $previous);
    }
}
