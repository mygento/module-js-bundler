<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
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
     * @param Filesystem $filesystem
     * @param Repository $assetRepository
     */
    public function __construct(
        Filesystem $filesystem,
        Repository $assetRepository
    ) {
        $this->filesystem = $filesystem;
        $this->assetRepository = $assetRepository;
    }

    /**
     * @param string $configFileName
     * @param string $configData
     *
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function create(string $configFileName, string $configData)
    {
        $staticContext = $this->assetRepository->getStaticViewFileContext();
        $configFileRelativePath = $staticContext->getConfigPath() . '/' . $configFileName;

        $dir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        $dir->writeFile($configFileRelativePath, $configData);
    }
}
