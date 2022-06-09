<?php

/**
 * @author Mygento Team
 * @copyright 2019-2022 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Model\Config;

class Reader extends \Magento\Framework\Config\Reader\Filesystem
{
    /**
     * @param \Mygento\JsBundler\Model\Config\Resolver $fileResolver
     * @param \Mygento\JsBundler\Model\Config\Converter $converter
     * @param \Mygento\JsBundler\Model\Config\SchemaLocator $schemaLocator
     * @param \Magento\Framework\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        Resolver $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        $fileName = 'js_bundler.xml',
        $idAttributes = [],
        $domDocumentClass = \Magento\Framework\Config\Dom::class,
        $defaultScope = 'global'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }

    /**
     * @param \Magento\Framework\View\Design\ThemeInterface $theme
     * @return array
     */
    public function readByTheme(\Magento\Framework\View\Design\ThemeInterface $theme): array
    {
        $fileList = $this->_fileResolver->getByTheme(
            $this->_fileName,
            $this->_defaultScope,
            $theme->getArea(),
            $theme
        );

        if (!count($fileList)) {
            return [];
        }

        return $this->_readFiles($fileList);
    }
}
