<?php

/**
 * @author Mygento Team
 * @copyright 2019 Mygento (https://www.mygento.ru)
 * @package Mygento_JsBundler
 */

namespace Mygento\JsBundler\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Bundle extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Mygento\JsBundler\Model\Config\Schema
     */
    private $config;

    public function __construct(
        \Mygento\JsBundler\Model\Config\Schema $config,
        string $name
    ) {
        parent::__construct($name);
        $this->config = $config;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->config->get();
    }
}
