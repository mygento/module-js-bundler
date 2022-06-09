<?php

/**
 * @author Mygento Team
 * @copyright 2019-2022 Mygento (https://www.mygento.ru)
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

    /**
     * @param \Mygento\JsBundler\Model\Config\Reader $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param string $cacheId
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
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

    /**
     * @param \Magento\Framework\View\Design\ThemeInterface $theme
     * @return array
     */
    private function readConfig(\Magento\Framework\View\Design\ThemeInterface $theme): array
    {
        return $this->reader->readByTheme($theme);
    }
}
