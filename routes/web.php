<?php

use App\Http\Controllers\PageController;

$controller = new PageController();

$router->get('/', [$controller, 'home'], 'home');
$router->get('reference', [$controller, 'reference'], 'reference');
$router->get('download', [$controller, 'download'], 'download');
$router->get('file/{id}', [$controller, 'fileProxy'], 'file');
