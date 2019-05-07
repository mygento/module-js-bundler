<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Plugin;

use Magento\Deploy\Package\BundleInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Builder
{
    const VIEW_CONFIG_MODULE = 'Mygento_JsBundler';

    /**
     * @var \Magento\Framework\View\Asset\Minification
     */
    private $minification;

    /**
     * @var \Magento\Framework\App\Utility\Files
     */
    private $utilityFiles;

    /**
     * @var \Magento\Framework\View\Design\Theme\ThemeProviderInterface
     */
    private $themeProvider;

    /**
     * @var \Magento\Framework\View\ConfigInterface
     */
    private $viewConfig;

    /**
     * @var string
     */
    private $pubStaticDir;

    /**
     *  @var array
     */
    private $content;

    /**
     * @param \Magento\Framework\View\ConfigInterface $viewConfig
     * @param \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider
     * @param \Magento\Framework\Filesystem $fs
     * @param \Magento\Framework\App\Utility\Files $files
     * @param \Magento\Framework\View\Asset\Minification $minification
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider,
        \Magento\Framework\Filesystem $fs,
        \Magento\Framework\App\Utility\Files $files,
        \Magento\Framework\View\Asset\Minification $minification
    ) {
        $this->viewConfig = $viewConfig;
        $this->themeProvider = $themeProvider;
        $this->utilityFiles = $files;
        $this->minification = $minification;

        $this->pubStaticDir = $fs->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        $this->content = [];
    }

    /**
     * @param \Magento\Deploy\Service\Bundle $subject
     * @param mixed $result
     * @param string $area
     * @param string $theme
     * @param string $locale
     */
    public function afterDeploy($subject, $result, $area, $theme, $locale)
    {
        $files = $this->getConfig($area, $theme)->getMediaEntities(
            self::VIEW_CONFIG_MODULE,
            \Mygento\JsBundler\Model\Extractor::BUNDLE_PATH
        );
        if (empty($files)) {
            return $result;
        }

        $config = $this->transformConfig($files);
        $bundleFiles = array_keys($config);
        if (count($bundleFiles) < 2) {
            return $result;
        }

        $folder = implode(DIRECTORY_SEPARATOR, [$area, $theme, $locale]);
        $pathToBundleDir = implode(DIRECTORY_SEPARATOR, [$folder, BundleInterface::BUNDLE_JS_DIR]);
        $packageDir = $this->pubStaticDir->getAbsolutePath($folder);
        $filesList = $this->utilityFiles->getFiles([$packageDir], '*.*');

        foreach ($filesList as $filePath => $sourcePath) {
            $sourcePath = str_replace('\\', '/', $sourcePath);
            $sourcePath = $this->pubStaticDir->getRelativePath($sourcePath);
            $filePath = substr($sourcePath, strlen($folder) + 1);

            $contentType = pathinfo($filePath, PATHINFO_EXTENSION);
            if (!in_array($contentType, \Magento\Deploy\Service\Bundle::$availableTypes)) {
                continue;
            }

            if (in_array($this->minification->removeMinifiedSign($filePath), $bundleFiles)) {
                $bundle = $config[$this->minification->removeMinifiedSign($filePath)];
                $this->content[$bundle][] = $this->getFileContent($this->minification->addMinifiedSign($sourcePath));
            }
        }
        foreach ($this->content as $bundleName => $content) {
            $bundleFile = $this->pubStaticDir->openFile(
                $this->minification->addMinifiedSign($pathToBundleDir . DIRECTORY_SEPARATOR . $bundleName . '-bundle.js')
            );
            $bundleFile->write(implode(PHP_EOL, $content));
        }

        return $result;
    }

    /**
     * Get content of static file
     *
     * @param string $sourcePath
     * @return string
     */
    private function getFileContent($sourcePath)
    {
        $content = $this->pubStaticDir->readFile($this->minification->addMinifiedSign($sourcePath));
        if (mb_detect_encoding($content) !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8');
        }

        return $content;
    }

    /**
     * Get View Configuration object related to the given area and theme
     *
     * @param string $area
     * @param string $theme
     * @return \Magento\Framework\Config\View
     */
    private function getConfig($area, $theme)
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
    private function transformConfig(array $config): array
    {
        $result = [];
        foreach ($config as $bundle => $items) {
            foreach ($items as $item) {
                $result[$item] = $bundle;
            }
        }

        return $result;
    }
}
