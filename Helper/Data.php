<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\View\Design\Theme\ThemeProviderInterface
     */
    private $themeProvider;

    /**
     * @var \Magento\Framework\View\ConfigInterface
     */
    private $viewConfig;

    /**
     * @var array
     */
    private $config;

    /**
     * @param \Magento\Framework\View\ConfigInterface $viewConfig
     * @param \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->viewConfig = $viewConfig;
        $this->themeProvider = $themeProvider;

        $this->config = [];
    }

    /**
     * Get View Configuration object related to the given area and theme
     *
     * @param string $area
     * @param string $theme
     * @return \Magento\Framework\Config\View
     */
    public function getViewConfig($area, $theme)
    {
        $themePath = $area . '/' . $theme;
        if (!isset($this->config[$themePath])) {
            $this->config[$themePath] = $this->viewConfig->getViewConfig([
                'area' => $area,
                'themeModel' => $this->themeProvider->getThemeByFullPath($themePath),
            ]);
        }

        return $this->config[$themePath];
    }

    /**
     * @param array $config
     * @return array
     */
    public function transformConfig(array $config): array
    {
        $result = [];
        foreach ($config as $bundle => $items) {
            foreach ($items as $item) {
                $result[$item] = $bundle;
            }
        }

        return $result;
    }

    /**
     * @param array $config
     * @return array
     */
    public function transposeConfig(array $config): array
    {
        $result = [];
        foreach ($config as $item => $bundle) {
            $file = pathinfo($item);
            $result[$bundle][] = '\'' . $file['dirname'] . '/' . $file['filename'] . '\'';
        }

        return $result;
    }

    /**
     * Check if js minify mode is enabled
     *
     * @param null $store
     * @return bool
     */
    public function jsMinifyEnabled($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            \Magento\Config\Model\Config\Backend\Admin\Custom::XML_PATH_DEV_JS_MINIFY_FILES,
            ScopeInterface::SCOPE_STORE, $store
        );
    }
}
