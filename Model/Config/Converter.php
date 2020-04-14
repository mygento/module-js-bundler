<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Model\Config;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Convert config
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $result = [];
        $bundles = $source->getElementsByTagName('bundle');
        foreach ($bundles as $bundle) {
            $name = $bundle->getAttribute('name');
            if (!isset($result[$name])) {
                $result[$name] = [];
            }
            foreach ($bundle->getElementsByTagName('item') as $item) {
                $result[$name][] = $item->nodeValue;
            }
        }

        return $result;
    }
}
