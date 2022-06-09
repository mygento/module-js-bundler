<?php

/**
 * @author Mygento Team
 * @copyright 2019-2022 Mygento (https://www.mygento.ru)
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
