<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Model\Config;

class Schema extends \Magento\Framework\Config\Data
{
    public function __construct(
        Reader $reader,
        \Magento\Framework\Config\CacheInterface $cache,
        $cacheId = 'js_bundler_schema_config',
        \Magento\Framework\Serialize\SerializerInterface $serializer = null
    ) {
        parent::__construct($reader, $cache, $cacheId, $serializer);
    }
}
