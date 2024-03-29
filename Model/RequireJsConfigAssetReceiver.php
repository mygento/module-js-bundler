<?php

/**
 * @author Mygento Team
 * @copyright 2019-2022 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Model;

use Magento\Framework\View\Asset\Repository;
use Mygento\JsBundler\Api\RequireJsConfigAssetReceiverInterface;

class RequireJsConfigAssetReceiver implements RequireJsConfigAssetReceiverInterface
{
    /**
     * @var Repository
     */
    private $assetRepository;

    /**
     * @param Repository $assetRepository
     */
    public function __construct(Repository $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    /**
     * @param string $configFileName
     *
     * @return \Magento\Framework\View\Asset\File;
     */
    public function receive(string $configFileName)
    {
        $staticContext = $this->assetRepository->getStaticViewFileContext();
        $configFileRelativePath = $staticContext->getConfigPath() . '/' . $configFileName;

        return $this->assetRepository->createArbitrary($configFileRelativePath, '');
    }
}
