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
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Opportus\LinkerService
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/linker-service/blob/master/LICENSE MIT
 */
class LinkerService
{
    /**
     * @var string $environment
     */
    private string $environment;

    /**
     * @var Container $container
     */
    private Container $container;

    /**
     * @param string $environment
     */
    public function __construct(string $environment)
    {
        $this->environment = $environment;

        $this->initialize();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handleHttpRequest(Request $request): Response
    {
        try {
            $controller = $this->container->get('linker_service.controller');

            return $controller->handleHttpRequest($request);
        } catch (Exception $exception) {
            $responseBody = 'Internal Server Error';

            if ($this->environment === 'dev') {
                $responseBody = \sprintf('%s: %s', $responseBody, $exception->getMessage());
            }

            return new Response($responseBody, 500);
        }
    }

    private function initialize()
    {
        $container = new ContainerBuilder();

        $container
            ->register('linker_service.service', Service::class)
        ;

        $container
            ->register('linker_service.controller', Controller::class)
            ->addArgument(new Reference('linker_service.service'))
            ->setPublic(true)
        ;

        $container->compile();

        $this->container = $container;
    }
}
