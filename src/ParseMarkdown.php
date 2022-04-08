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

namespace RobiNN\Docs;

class ParseMarkdown {
    /**
     * @var ParsedownExt
     */
    private ParsedownExt $parsedown;

    /**
     * @var ?string
     */
    private ?string $text;

    public function __construct(?string $text = null) {
        $this->parsedown = new ParsedownExt();

        $this->text = is_file($this->getFile($text)) ? file_get_contents($this->getFile($text)) : $text;
    }

    /**
     * Parse content
     *
     * @return string
     */
    public function parse(): string {
        return $this->parsedown->text($this->text);
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string {
        if (empty($this->parsedown->title)) {
            $data = explode("\n", (string)$this->text);
            return array_reverse(explode('# ', $data[0], 2))[0];
        }

        return $this->parsedown->title;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string {
        $description = '';
        $data = explode("\n", (string)$this->text);

        if (!empty($data[2])) {
            $description = strip_tags($data[2]);
            $max_length = 158; // Recommended maximum for description size

            if (strlen($description) > $max_length) {
                $offset = ($max_length - 3) - strlen($description);
                $description = substr($description, 0, strrpos($description, ' ', $offset)).'...';
            }
        }

        return $description;
    }

    /**
     * Get headings
     *
     * @return array
     */
    public function getHeadings(): array {
        return $this->parsedown->headings;
    }

    /**
     * Get file
     *
     * @param string $path
     *
     * @return string
     */
    private function getFile(string $path): string {
        $path = Functions::config('docs_path').trim($path, '/');
        return is_file($path.'.md') ? $path.'.md' : $path.'/README.md';
    }
}
