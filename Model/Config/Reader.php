<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Model\Config;

class Reader extends \Magento\Framework\Config\Reader\Filesystem
{
    public function __construct(
        \Mygento\JsBundler\Model\Config\Resolver $fileResolver,
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
     * Load configuration scope
     *
     * @param string|null $scope
     * @return array
     */
    public function read($scope = null)
    {
        $scope = $scope ?: $this->_defaultScope;
        $fileList = $this->_fileResolver->get($this->_fileName, $scope);
        if (!count($fileList)) {
            return [];
        }
        $output = $this->_readFiles($fileList);

        return $output;
    }

    private function resolve()
    {

    }
}
