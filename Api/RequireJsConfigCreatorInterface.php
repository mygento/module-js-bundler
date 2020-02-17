<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Api;

interface RequireJsConfigCreatorInterface
{
    /**
     * @param string $configFileName
     * @param string $configData
     *
     * @return void
     */
    public function create(string $configFileName, string $configData);
}
