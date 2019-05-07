<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Model;

class Extractor implements \Magento\Framework\View\Xsd\Media\TypeDataExtractorInterface
{
    const VIEW_CONFIG_MODULE = 'Mygento_JsBundler';
    const ITEM_PATH = 'bundle';
    const BUNDLE_PATH = 'bundles';

    /**
     * Extract media configuration data from the DOM structure
     *
     * @param \DOMElement $mediaNode
     * @param string $mediaParentTag
     * @return array
     */
    public function process(\DOMElement $mediaNode, $mediaParentTag)
    {
        $result = [];
        $moduleNameVideo = $mediaNode->getAttribute('module');
        foreach ($mediaNode->getElementsByTagName(self::ITEM_PATH) as $node) {
            $bundle = $node->getAttribute('name');
            foreach ($node->childNodes as $attribute) {
                if ($attribute->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $nodeValue = $attribute->nodeValue;
                $result[$mediaParentTag][$moduleNameVideo][self::BUNDLE_PATH][$bundle][] = $nodeValue;
            }
        }

        return $result;
    }
}
