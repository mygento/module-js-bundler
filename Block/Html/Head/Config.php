<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Block\Html\Head;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Page\Config as PageConfig;
use Mygento\JsBundler\Api\RequireJsConfigAssetReceiverInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\RequireJs\Config as RequireJsConfig;

class Config extends AbstractBlock
{
    const REQUIREJS_CONFIG_BUNDLER_FILE = 'requirejs-config-bundler.js';
    const REQUIREJS_CONFIG_ORIGINAL_FILE = 'requirejs-config.js';

    /**
     * @var PageConfig
     */
    private $pageConfig;

    /**
     * @var RequireJsConfigAssetReceiverInterface
     */
    private $requireJsConfigAssetReceiver;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var RequireJsConfig
     */
    private $requireJsConfig;

    /**
     * @param Context $context
     * @param PageConfig $pageConfig
     * @param RequireJsConfigAssetReceiverInterface $requireJsConfigAssetReceiver
     * @param DirectoryList $directoryList
     * @param RequireJsConfig $requireJsConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        PageConfig $pageConfig,
        RequireJsConfigAssetReceiverInterface $requireJsConfigAssetReceiver,
        DirectoryList $directoryList,
        RequireJsConfig $requireJsConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->pageConfig = $pageConfig;
        $this->requireJsConfigAssetReceiver = $requireJsConfigAssetReceiver;
        $this->directoryList = $directoryList;
        $this->requireJsConfig = $requireJsConfig;
    }

    /**
     * @return AbstractBlock
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function _prepareLayout()
    {
        $assetCollection = $this->pageConfig->getAssetCollection();
        $requireJsConfigBundler = $this->requireJsConfigAssetReceiver->receive(self::REQUIREJS_CONFIG_BUNDLER_FILE);
        $requireJsConfigBundlerFullPath =  $this->directoryList->getPath(DirectoryList::STATIC_VIEW) . '/' . $requireJsConfigBundler->getPath();

        if (file_exists($requireJsConfigBundlerFullPath)) {
            $assetCollection->insert(
                $requireJsConfigBundler->getFilePath(),
                $requireJsConfigBundler,
                $this->requireJsConfig->getMixinsFileRelativePath()
            );

            //remove original requirejs-config from asset collection
            $requireJsConfigBundler = $this->requireJsConfigAssetReceiver->receive(self::REQUIREJS_CONFIG_ORIGINAL_FILE);
            $assetCollection->remove($requireJsConfigBundler->getFilePath());
        }

        return parent::_prepareLayout();
    }
}
