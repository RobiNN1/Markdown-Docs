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

use Exception;
use RobiNN\Docs\Documentation;
use RobiNN\Docs\ParseMarkdown;

class SearchController extends Documentation {
    public function show(): void {
        $results = [];
        $search_page = filter_input(INPUT_GET, 'page');

        if (!empty($search_page)) {
            foreach ($this->allPages() as $page) {
                foreach (explode(' ', $search_page) as $word) {
                    if (stripos($page['title'], $word) !== false) {
                        $results[] = [
                            'page'  => $page['page'],
                            'title' => $page['title'],
                            'link'  => $page['link'],
                        ];
                    }
                }
            }
        }

        // Remove duplicates
        $temp_arr = array_unique(array_column($results, 'link'));
        $results = array_values(array_intersect_key($results, $temp_arr));

        if (empty($results)) {
            $results['status'] = 'We didn\'t find any results!';
        }

        header('Content-Type: application/json');
        try {
            echo json_encode($results, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Get all pages.
     *
     * @return array<int, array<string, string>>
     */
    private function allPages(): array {
        $pages = [];

        if (is_dir($this->config('docs_path'))) {
            $dirs = $this->scanDir($this->config('docs_path'));

            foreach ($dirs as $file) {
                $md = new ParseMarkdown($file);
                $md->parse();
                $page_title = $md->getTitle();
                $headings = $md->getHeadings();

                $pages[] = [
                    'page'  => $page_title,
                    'title' => $page_title,
                    'link'  => $this->config('site_url').$file,
                ];

                if (!empty($headings)) {
                    foreach ($headings as $heading) {
                        $pages[] = [
                            'page'  => $page_title,
                            'title' => $heading['title'],
                            'link'  => $this->config('site_url').$file.'#'.$heading['id'],
                        ];
                    }
                }
            }
        }

        return $this->cacheData('search_all_pages', $pages);
    }
}
