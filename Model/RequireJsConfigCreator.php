<?php

/**
 * @author Mygento Team
 * @copyright 2019-2022 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Asset\Minification;
use Magento\Framework\View\Asset\Repository;
use Mygento\JsBundler\Api\RequireJsConfigCreatorInterface;

class RequireJsConfigCreator implements RequireJsConfigCreatorInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Repository
     */
    private $assetRepository;

    /**
     * @var Minification
     */
    private $minification;

    /**
     * @param Filesystem $filesystem
     * @param Repository $assetRepository
     * @param Minification $minification
     */
    public function __construct(
        Filesystem $filesystem,
        Repository $assetRepository,
        Minification $minification
    ) {
        $this->filesystem = $filesystem;
        $this->assetRepository = $assetRepository;
        $this->minification = $minification;
    }

    /**
     * @param string $configFileName
     * @param string $configData
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     * @return void
     */
    public function create(string $configFileName, string $configData)
    {
        $staticContext = $this->assetRepository->getStaticViewFileContext();
        $configFileRelativePath = $this->minification->addMinifiedSign(
            $staticContext->getConfigPath() . '/' . $configFileName
        );

        $dir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        $dir->writeFile($configFileRelativePath, $configData);
    }
}
