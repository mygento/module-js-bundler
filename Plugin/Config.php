<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Plugin;

use Magento\Deploy\Package\BundleInterface;
use Mygento\JsBundler\Api\RequireJsConfigCreatorInterface;

class Config
{
    const BUNDLE_ASSET_FILE_NAME = 'requirejs-config-bundler.js';

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    private $design;

    /**
     * @var \Mygento\JsBundler\Model\Config\Schema
     */
    private $sconfig;

    /**
     * @var RequireJsConfigCreatorInterface
     */
    private $requireJsConfigCreator;

    public function __construct(
        \Mygento\JsBundler\Model\Config\Schema $sconfig,
        RequireJsConfigCreatorInterface $requireJsConfigCreator,
        \Magento\Framework\View\DesignInterface $design
    ) {
        $this->requireJsConfigCreator = $requireJsConfigCreator;
        $this->sconfig = $sconfig;
        $this->design = $design;
    }

    /**
     * @param \Magento\Framework\RequireJs\Config $subject
     * @param string $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return type
     */
    public function afterGetConfig($subject, $result)
    {
        $themeClass = $this->design->getDesignTheme();
        $config = $this->sconfig->getConfig($themeClass);
        if (empty($config)) {
            return $result;
        }

        $bundleAssetData = $result . PHP_EOL . 'requirejs.config({bundles: {' . PHP_EOL;
        $map = [];

        foreach ($config as $bundle => $files) {
            if (count($files) < 2) {
                continue;
            }

            $map[] = "'" . BundleInterface::BUNDLE_JS_DIR
                . '/' . $bundle . "-bundle': "
                . '[' . implode(',', $files) . ']';
        }

        $bundleAssetData .= implode(',' . PHP_EOL, $map);
        $bundleAssetData .= PHP_EOL . '}});';

        $this->requireJsConfigCreator->create(self::BUNDLE_ASSET_FILE_NAME, $bundleAssetData);

        return $result;
    }
}
