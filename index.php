<?php
/**
 * This file is part of Docs.
 *
 * Copyright (c) Róbert Kelčák (https://kelcak.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

$router = new RobiNN\Docs\Router();

$docs = new RobiNN\Docs\Documentation();

$router->set404([$docs, 'show404']);
$router->setBasePath($docs->config('site_path'));

$router->get('/', RobiNN\Docs\Controllers\HomepageController::class);
$router->get('search', RobiNN\Docs\Controllers\SearchController::class);
$router->get('(.*)', RobiNN\Docs\Controllers\DocsController::class); // It must be at the end

$router->run();
