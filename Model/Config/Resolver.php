<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Model\Config;

use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\View\Design\Fallback\RulePool;
use Magento\Framework\View\Design\FileResolution\Fallback\ResolverInterface;
use Magento\Framework\View\DesignInterface;


class Resolver implements \Magento\Framework\Config\FileResolverInterface
{
    /**
     * @var Reader
     */
    private $moduleReader;

    public function __construct(
        Reader $moduleReader,
        DesignInterface $designInterface,
        ResolverInterface $resolver
    ) {
        $this->resolver = $resolver;
        $this->currentTheme = $designInterface->getDesignTheme();
        $this->area = $designInterface->getArea();
        $this->moduleReader = $moduleReader;
    }

    public function get($filename, $scope): array
    {
        $iterator = $this->moduleReader->getConfigurationFiles($filename)->toArray();
        $bundlePath = $this->resolver->resolve(
            RulePool::TYPE_FILE,
            'etc/view.xml',
            $this->area,
            $this->currentTheme
        );
        if (file_exists($bundlePath)) {
            try {
                $designDom = new \DOMDocument();
                $designDom->load($bundlePath);
                $iterator[$bundlePath] = $designDom->saveXML();
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    new \Magento\Framework\Phrase('Could not read config file')
                );
            }
        }

        return $iterator;
    }
}