<?php

/**
 * This file is part of the opportus/linker-service project.
 *
 * Copyright (c) 2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\LinkerService\Tests;

use Generator;
use Opportus\LinkerService\LinkerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package Opportus\LinkerService\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/linker-service/blob/master/LICENSE MIT
 */
class LinkerServiceTest extends TestCase
{
    /**
     * @dataProvider provideLists
     * @param string $list
     * @param string $linkedList
     */
    public function testHandleHttpRequest(string $list, string $linkedList)
    {
        $linker = $this->buildLinkerService('dev');
        $request = $this->buildRequest($list, 'arrival:departure');

        $response = $linker->handleHttpRequest($request);

        $linkedList = '"'.\str_replace('"', '\u0022', $linkedList).'"';

        static::assertSame($linkedList, $response->getContent());
    }

    /**
     * @return Generator
     */
    public function provideLists(): Generator
    {
        $linkedList = \file_get_contents(\dirname(__FILE__).'/list.json');
        $linkedList = $list = \json_decode($linkedList, true, 3);

        for ($i =0; $i < 10; $i++) {
            \shuffle($list);

            yield [
                \json_encode($list),
                \json_encode($linkedList),
            ];
        }
    }

    /**
     * @param string $environment
     * @return LinkerService
     */
    private function buildLinkerService(string $environment): LinkerService
    {
        return new LinkerService($environment);
    }

    /**
     * @param string $list
     * @param string $link
     * @return Request
     */
    private function buildRequest(string $list, string $link): Request
    {
        return new Request(
            [
                'list' => $list,
                'link' => $link,
            ]
        );
    }
}
