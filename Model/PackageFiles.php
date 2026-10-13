<?php

/**
 * @author Mygento Team
 * @copyright 2019-2026 Mygento (https://www.mygento.com)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Asset\RepositoryMap;

class PackageFiles
{
    private ReadInterface $pubStaticDir;
    private array $resultMapByPackage = [];

    public function __construct(Filesystem $filesystem)
    {
        $this->pubStaticDir = $filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW);
    }

    public function getDeployedFilePath(string $packageDir, string $file): string
    {
        if (!isset($this->resultMapByPackage[$packageDir])) {
            $this->resultMapByPackage[$packageDir] = $this->readResultMap($packageDir);
        }

        return ($this->resultMapByPackage[$packageDir][$file] ?? $packageDir) . '/' . $file;
    }

    private function readResultMap(string $packageDir): array
    {
        $resultMapFile = $packageDir . '/' . RepositoryMap::RESULT_MAP_NAME;
        if (!$this->pubStaticDir->isFile($resultMapFile)) {
            return [];
        }

        $resultMap = [];
        $files = json_decode($this->pubStaticDir->readFile($resultMapFile), true);
        foreach ($files as $fileId => $package) {
            $file = str_replace(Repository::FILE_ID_SEPARATOR, '/', $fileId);
            $resultMap[$file] = $package['area'] . '/' . $package['theme'] . '/' . $package['locale'];
        }

        return $resultMap;
    }
}
