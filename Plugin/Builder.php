<?php

/**
 * @author Mygento Team
 * @copyright 2019-2022 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Plugin;

use Magento\Deploy\Package\BundleInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Builder
{
    public const JSEXT = '.js';

    /**
     * @var \Magento\Framework\View\Design\Theme\ThemeProvider
     */
    private $design;

    /**
     * @var \Mygento\JsBundler\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\View\Asset\Minification
     */
    private $minification;

    /**
     * @var \Magento\Framework\App\Utility\Files
     */
    private $utilityFiles;

    /**
     * @var string
     */
    private $pubStaticDir;

    /**
     * @var array
     */
    private $content;

    /**
     * @var \Mygento\JsBundler\Model\Config\Schema
     */
    private $config;

    /**
     * @param \Mygento\JsBundler\Helper\Data $helper
     * @param \Magento\Framework\Filesystem $fs
     * @param \Magento\Framework\App\Utility\Files $files
     * @param \Magento\Framework\View\Asset\Minification $minification
     * @param \Mygento\JsBundler\Model\Config\Schema $config
     * @param \Magento\Framework\View\DesignInterface $design
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Mygento\JsBundler\Helper\Data $helper,
        \Magento\Framework\Filesystem $fs,
        \Magento\Framework\App\Utility\Files $files,
        \Magento\Framework\View\Asset\Minification $minification,
        \Mygento\JsBundler\Model\Config\Schema $config,
        \Magento\Framework\View\Design\Theme\ThemeProvider $design
    ) {
        $this->helper = $helper;
        $this->utilityFiles = $files;
        $this->minification = $minification;
        $this->pubStaticDir = $fs->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        $this->content = [];
        $this->config = $config;

        $this->design = $design;
    }

    /**
     * @param \Magento\Deploy\Service\Bundle $subject
     * @param mixed $result
     * @param string $area
     * @param string $theme
     * @param string $locale
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDeploy($subject, $result, $area, $theme, $locale)
    {
        $themeClass = $this->design->getThemeByFullPath($area . '/' . $theme);
        $config = $this->config->getConfig($themeClass);
        if (!($config)) {
            return $result;
        }

        $this->content = [];

        $folder = implode(DIRECTORY_SEPARATOR, [$area, $theme, $locale]);
        $pathToBundleDir = implode(DIRECTORY_SEPARATOR, [$folder, BundleInterface::BUNDLE_JS_DIR]);
        $packageDir = $this->pubStaticDir->getAbsolutePath($folder);
        $filesList = $this->utilityFiles->getFiles([$packageDir], '*.*');

        $files = $this->helper->transformConfig($config);
        $bundleFiles = array_keys($files);
        if (count($bundleFiles) < 2) {
            return $result;
        }

        foreach ($bundleFiles as $filePath) {
            $relativePath = $packageDir . '/' . $this->pubStaticDir->getRelativePath($filePath . self::JSEXT);
            $relativePath = $this->minification->addMinifiedSign($relativePath);

            if (in_array($relativePath, $filesList)) {
                $bundle = $files[$this->minification->removeMinifiedSign($filePath)];
                $this->content[$bundle][] = $this->updateFile(
                    $this->minification->removeMinifiedSign($filePath),
                    $this->minification->addMinifiedSign($relativePath)
                );
            }
        }
        foreach ($this->content as $bundleName => $content) {
            $bundleFile = $this->pubStaticDir->openFile(
                $this->minification->addMinifiedSign(
                    $pathToBundleDir . DIRECTORY_SEPARATOR . $bundleName . '-bundle.js'
                )
            );
            $bundleFile->write(implode(PHP_EOL, $content));
        }

        return $result;
    }

    /**
     * @param string $filename
     * @param string $source
     * @return string
     */
    private function updateFile($filename, $source): string
    {
        $content = $this->getFileContent($source);
        $file = pathinfo($filename);

        if ($file['dirname'] == 'jquery/ui-modules') {
            $file['dirname'] = 'jquery-ui-modules';
        }

        $fileId = $file['dirname'] . '/' . $file['filename'];

        $content = str_replace("define(\n ", "define('{$fileId}',", $content);
        $content = str_replace('define([', "define('{$fileId}',[", $content);
        $content = str_replace('define(function ()', "define('{$fileId}', [], function()", $content);

        return str_replace('define(function()', "define('{$fileId}', [], function()", $content);
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
}
