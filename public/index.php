<?php

/**
 * This file is part of the opportus/linker-service project.
 *
 * Copyright (c) 2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

use Opportus\LinkerService\LinkerService;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';

$request = Request::createFromGlobals();

$linker = new LinkerService($request->server->get('SERVICE_ENVIRONMENT', 'prod'));

$response = $linker->handleHttpRequest($request);

$response->send();
