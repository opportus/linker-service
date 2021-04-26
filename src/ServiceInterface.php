<?php

/**
 * This file is part of the opportus/linker-service project.
 *
 * Copyright (c) 2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\LinkerService;

use Opportus\LinkerService\Exception\InvalidArgumentException;
use SplDoublyLinkedList;

/**
 * @package Opportus\LinkerService
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/linker-service/blob/master/LICENSE MIT
 */
interface ServiceInterface
{
    /**
     * @param array  $list
     * @param string $link
     * @return SplDoublyLinkedList
     * @throws InvalidArgumentException
     */
    public function linkList(array $list, string $link): SplDoublyLinkedList;
}
