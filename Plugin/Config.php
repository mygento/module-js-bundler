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
     * @var \Mygento\JsBundler\Helper\Data
     */
    private $helper;

    /**
     * @var \Mygento\JsBundler\Model\Config
     */
    private $config;

    /**
     * @var RequireJsConfigCreatorInterface
     */
    private $requireJsConfigCreator;

    /**
     * @param \Mygento\JsBundler\Helper\Data $helper
     * @param \Mygento\JsBundler\Model\Config $config
     * @param RequireJsConfigCreatorInterface $requireJsConfigCreator
     */
    public function __construct(
        \Mygento\JsBundler\Helper\Data $helper,
        \Mygento\JsBundler\Model\Config $config,
        RequireJsConfigCreatorInterface $requireJsConfigCreator
    ) {
        $this->helper = $helper;
        $this->config = $config;
        $this->requireJsConfigCreator = $requireJsConfigCreator;
    }

    /**
     * @param \Magento\Framework\RequireJs\Config $subject
     * @param string $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return type
     */
    public function afterGetConfig($subject, $result)
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $path = explode('/', str_replace('/' . $subject::MIXINS_FILE_NAME, '', $subject->getMixinsFileRelativePath()));
        $area = array_shift($path);
        array_pop($path);
        $theme = implode('/', $path);

        $files = $this->helper->getViewConfig($area, $theme)->getMediaEntities(
            \Mygento\JsBundler\Model\Extractor::VIEW_CONFIG_MODULE,
            \Mygento\JsBundler\Model\Extractor::BUNDLE_PATH
        );

        if (empty($files)) {
            return $result;
        }

        $config = $this->helper->transformConfig($files);
        $bundleFiles = array_keys($config);
        if (count($bundleFiles) < 2) {
            return $result;
        }

        $bundleAssetData = $result . PHP_EOL . 'requirejs.config({bundles: {' . PHP_EOL;
        $map = [];

        foreach ($this->helper->transposeConfig($config) as $bundle => $files) {
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
