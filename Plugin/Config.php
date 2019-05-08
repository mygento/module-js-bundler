<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Plugin;

use Magento\Deploy\Package\BundleInterface;

class Config
{
    /**
     * @var \Mygento\JsBundler\Helper\Data
     */
    private $helper;

    /**
     * @param \Mygento\JsBundler\Helper\Data $helper
     */
    public function __construct(
        \Mygento\JsBundler\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\RequireJs\Config $subject
     * @param string $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return type
     */
    public function afterGetConfig($subject, $result)
    {
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

        $result .= PHP_EOL . 'requirejs.config({bundles: {' . PHP_EOL;
        $map = [];
        foreach ($this->helper->transposeConfig($config) as $bundle => $files) {
            if (count($files) < 2) {
                continue;
            }

            $map[] = "'" . BundleInterface::BUNDLE_JS_DIR
                . '/' . $bundle . "-bundle': "
                . '[' . implode(',', $files) . ']';
        }
        $result .= implode(',' . PHP_EOL, $map);
        $result .= PHP_EOL . '}});';

        return $result;
    }
}
