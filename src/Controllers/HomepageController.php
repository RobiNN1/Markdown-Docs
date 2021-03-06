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

namespace RobiNN\Docs\Controllers;

use RobiNN\Docs\Documentation;

class HomepageController extends Documentation {
    public function show(): void {
        echo $this->tpl('home', [
            'categories' => $this->cacheData('homepage_categories', $this->getPages('', true)),
        ]);
    }
}
