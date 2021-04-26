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

use Exception;
use Opportus\LinkerService\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Opportus\LinkerService
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/linker-service/blob/master/LICENSE MIT
 */
class Controller
{
    /**
     * @var ServiceInterface $service
     */
    private ServiceInterface $service;

    /**
     * @param ServiceInterface $service
     */
    public function __construct(ServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handleHttpRequest(Request $request): Response
    {
        $link = $request->get('link', '');
        $list = $request->get('list', '[]');

        try {
            $list = \json_decode($list, true, 3, \JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            $responseBody = \sprintf('%s: %s', 'Bad Request', $exception->getMessage());

            return new Response($responseBody, 400);
        }

        try {
            $linkedList = $this->service->linkList($list, $link);
        } catch (InvalidArgumentException $exception) {
            $responseBody = \sprintf('%s: %s', 'Bad Request', $exception->getMessage());

            return new Response($responseBody, 400);
        } catch (Exception $exception) {
            $responseBody = 'Internal Server Error';

            if ($request->server->get('SERVICE_ENVIRONMENT') === 'dev') {
                $responseBody = \sprintf('%s: %s', $responseBody, $exception->getMessage());
            }

            return new Response($responseBody, 500);
        }

        $responseBody = \json_encode(\iterator_to_array($linkedList, true));

        return new JsonResponse($responseBody, 200);
    }
}
