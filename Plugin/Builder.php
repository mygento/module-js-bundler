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
     *  @var array
     */
    private $content;

    /**
     * @param \Mygento\JsBundler\Helper\Data $helper
     * @param \Magento\Framework\Filesystem $fs
     * @param \Magento\Framework\App\Utility\Files $files
     * @param \Magento\Framework\View\Asset\Minification $minification
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Mygento\JsBundler\Helper\Data $helper,
        \Magento\Framework\Filesystem $fs,
        \Magento\Framework\App\Utility\Files $files,
        \Magento\Framework\View\Asset\Minification $minification
    ) {
        $this->helper = $helper;
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
}
