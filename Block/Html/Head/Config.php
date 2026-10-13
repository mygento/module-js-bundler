<?php

/**
 * @author Mygento Team
 * @copyright 2019-2026 Mygento (https://www.mygento.com)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Block\Html\Head;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\RequireJs\Config as RequireJsConfig;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Page\Config as PageConfig;
use Mygento\JsBundler\Api\RequireJsConfigAssetReceiverInterface;
use Mygento\JsBundler\Plugin\Builder;

class Config extends AbstractBlock
{
    private ReadInterface $pubStaticDir;

    public function __construct(
        Context $context,
        private PageConfig $pageConfig,
        private RequireJsConfigAssetReceiverInterface $requireJsConfigAssetReceiver,
        Filesystem $filesystem,
        array $data = [],
    ) {
        parent::__construct($context, $data);

        $this->pubStaticDir = $filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW);
    }

    protected function _prepareLayout()
    {
        $bundlesConfig = $this->requireJsConfigAssetReceiver->receive(Builder::BUNDLES_CONFIG_FILE);
        if ($this->pubStaticDir->isFile($bundlesConfig->getPath())) {
            $this->pageConfig->getAssetCollection()->insert(
                $bundlesConfig->getFilePath(),
                $bundlesConfig,
                RequireJsConfig::REQUIRE_JS_FILE_NAME,
            );
        }

        return parent::_prepareLayout();
    }
}
