<?php

/**
 * @author Mygento Team
 * @copyright 2019-2026 Mygento (https://www.mygento.com)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Model\Config;

use Magento\Framework\View\Design\ThemeInterface;

class Bundles
{
    private array $bundlesByTheme = [];

    public function __construct(private Reader $reader) {}

    public function getByTheme(ThemeInterface $theme): array
    {
        $themePath = $theme->getFullPath();
        if (!isset($this->bundlesByTheme[$themePath])) {
            $this->bundlesByTheme[$themePath] = $this->reader->readByTheme($theme);
        }

        return $this->bundlesByTheme[$themePath];
    }
}
