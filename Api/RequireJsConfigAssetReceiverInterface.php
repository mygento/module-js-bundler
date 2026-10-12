<?php

/**
 * @author Mygento Team
 * @copyright 2019-2026 Mygento (https://www.mygento.com)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Api;

interface RequireJsConfigAssetReceiverInterface
{
    /**
     * @param string $configFileName
     *
     * @return \Magento\Framework\View\Asset\File;
     */
    public function receive(string $configFileName);
}
