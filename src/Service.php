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
class Service implements ServiceInterface
{
    private const LINK_REGEX_PATTERN = '/^([A-Za-z0-9\_\-]*):*([A-Za-z0-9\_\-]*)$/';

    /**
     * {@inheritdoc}
     */
    public function linkList(array $list, string $link): SplDoublyLinkedList
    {
        if (!\preg_match(self::LINK_REGEX_PATTERN, $link, $linkMatches)) {
            throw new InvalidArgumentException(
                2,
                \sprintf('%s does not match link pattern %s', $link, self::LINK_REGEX_PATTERN)
            );
        }

        $link = $this->findLink($list, $linkMatches);

        return $this->buildLinkedList($list, $link);
    }

    /**
     * @todo Walk list seeking in both directions...
     *
     * @param array  $list
     * @param string $link
     * @return SplDoublyLinkedList
     */
    private function buildLinkedList(array $list, string $link): SplDoublyLinkedList
    {
        $direction = true;
        $linkedList = new SplDoublyLinkedList();
        $currentNodeListKey = \array_key_first($list);

        while (!empty($list)) {
            if (!isset($currentNodeListKey)) {
                $currentNode = $linkedList->{$this->getUnlinkOperation($direction)}();
            } else {
                $currentNode = $list[$currentNodeListKey];
                unset($list[$currentNodeListKey]);
            }

            $linkedList->{$this->getLinkOperation($direction)}($currentNode);

            $currentNodeListKey = $this->findLinkedNodeKey($list, $link, $currentNode, $direction);

            if (!isset($currentNodeListKey)) {
                $direction = !$direction;
            }
        }

        $linkedList->rewind();

        return $linkedList;
    }

    /**
     * @param array  $list
     * @param string $link
     * @param array  $currentNode
     * @param bool   $direction
     * @return null|int
     */
    private function findLinkedNodeKey(array $list, string $link, array $currentNode, bool $direction): ?int
    {
        $links = \explode(':', $link);

        foreach ($list as $key => $node) {
            if ($currentNode[$links[$direction]] === $node[$links[!$direction]]) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @param bool $direction
     * @return string
     */
    private function getLinkOperation(bool $direction): string
    {
        return $direction ? 'unshift' : 'push';
    }

    /**
     * @param bool $direction
     * @return string
     */
    private function getUnlinkOperation(bool $direction): string
    {
        return $direction ? 'shift' : 'pop';
    }

    /**
     * @todo That's ugly! Optimize with a max heap, improve the mechanism, and make elegant code...
     *
     * @param array $list
     * @param array $linkMatches
     * @return string
     */
    private function findLink(array $list, array $linkMatches): string
    {
        if ($linkMatches[1] && $linkMatches[2]) {
            return $linkMatches[0];
        }

        $nodeAttributes = [];

        foreach ($list as $node) {
            foreach ($node as $nodeAttributeName => $nodeAttributeValue) {
                $nodeAttributes[(string)$nodeAttributeValue] ??= [];
                $nodeAttributes[(string)$nodeAttributeValue] = \array_merge(
                    $nodeAttributes[(string)$nodeAttributeValue],
                    [$nodeAttributeName]
                );
            }
        }

        foreach ($nodeAttributes as $names) {
            if (\count($names) === 2) {
                if (isset($linkMatches[1]) && false !== $nameKey = \array_search($linkMatches[1], $names)) {
                    return \sprintf('%s:%s', $linkMatches[1], $names[!$nameKey]);
                }

                if (isset($linkMatches[2]) && false !== $nameKey = \array_search($linkMatches[2], $names)) {
                    return \sprintf('%s:%s', $names[!$nameKey], $linkMatches[2]);
                }

                return \implode(':', $names);
            }
        }

        return '';
    }
}
