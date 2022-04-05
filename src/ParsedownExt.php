<?php
/**
 * This file is part of Docs.
 *
 * Copyright (c) Róbert Kelčák (https://kelcak.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RobiNN\Docs;

use Parsedown;

class ParsedownExt extends Parsedown {
    /**
     * @var array
     */
    public array $headings = [];

    /**
     * @var string
     */
    public string $title = '';

    /**
     * Get headings
     *
     * @param mixed $Line
     *
     * @return ?array
     */
    protected function blockHeader(mixed $Line): ?array {
        $block = parent::blockHeader($Line);

        // Set headings
        if (isset($block['element']['text'])) {
            $title = $block['element']['text'];
            $level = $block['element']['name'];
            $id = $this->createIdFromTitle($title);

            if ($level === 'h1') {
                $this->title = $title;
            }

            if (in_array($level, ['h2', 'h3', 'h4', 'h5', 'h6'])) {
                $this->headings[] = [
                    'title' => $title,
                    'id'    => $id,
                ];

                $block['element']['text'] = '<'.$level.' id="'.$id.'"><a href="#'.$id.'">'.$title.'</a></'.$level.'>';
            }
        }

        return $block;
    }

    /**
     * Create id from title
     *
     * @param string $title
     *
     * @return string
     */
    public function createIdFromTitle(string $title): string {
        return strtolower(str_replace([' ', '(', ')', '/'], ['-', '', '', ''], $title));
    }

    /**
     * Fix images paths and add css class
     *
     * @param mixed $Excerpt
     *
     * @return ?array
     */
    protected function inlineImage(mixed $Excerpt): ?array {
        $inline = parent::inlineImage($Excerpt);

        if (isset($inline)) {
            $path = Functions::config('docs_path').str_replace('../', '', $inline['element']['attributes']['src']);
            $image_type = pathinfo($path, PATHINFO_EXTENSION);
            $img_data = file_get_contents($path);

            $inline['element']['attributes']['src'] = 'data:image/'.$image_type.';base64,'.base64_encode($img_data);
            $inline['element']['attributes']['class'] = 'img-fluid';
        }

        return $inline;
    }

    /**
     * Add class to table
     *
     * @param mixed $Line
     * @param mixed $Block
     *
     * @return ?array
     */
    protected function blockTable(mixed $Line, mixed $Block = null): ?array {
        $block = parent::blockTable($Line, $Block);

        if (isset($block)) {
            $block['element']['attributes']['class'] = 'table';
        }

        return $block;
    }

    /**
     * Remove .md from links
     *
     * @param mixed $Excerpt
     *
     * @return ?array
     */
    protected function inlineLink(mixed $Excerpt): ?array {
        $block = parent::inlineLink($Excerpt);

        if (isset($block)) {
            $href = $block['element']['attributes']['href'];
            $block['element']['attributes']['href'] = str_ends_with($href, '.md') ? str_replace('.md', '', $href) : $href;
        }

        return $block;
    }
}