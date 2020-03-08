<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Model\Config;

class Schema extends \Magento\Framework\Config\Data
{
    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @var array
     */
    private $config;

    public function __construct(
        Reader $reader,
        \Magento\Framework\Config\CacheInterface $cache,
        $cacheId = 'js_bundler_schema_config',
        \Magento\Framework\Serialize\SerializerInterface $serializer = null
    ) {
        parent::__construct($reader, $cache, $cacheId, $serializer);
        $this->reader = $reader;

        $this->config = [];
    }

    /**
     * @param \Magento\Framework\View\Design\ThemeInterface $theme
     * @return type
     */
    public function getConfig(\Magento\Framework\View\Design\ThemeInterface $theme)
    {
        $themePath = $theme->getFullPath();
        if (!isset($this->config[$themePath])) {
            $this->config[$themePath] = $this->readConfig($theme);
        }

        return $this->config[$themePath];
    }

    private function readConfig(\Magento\Framework\View\Design\ThemeInterface $theme): array
    {
        return $this->reader->readByTheme($theme);
    }
}
