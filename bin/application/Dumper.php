<?php

/**
 * This file implements the dumper.
 */

namespace dbeurive\Slim\bin\application;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use dbeurive\Slim\controller\Indexer as CtrlIndexer;

/**
 * Class Dumper
 *
 * This class implements the dumper.
 * The dumper produces a human readable representation of the index.
 *
 * @package dbeurive\Slim\bin\application
 */

class Dumper extends Command
{
    /**
     * Command line argument that represents the path to the created index.
     */
    const CLA_INDEX_PATH = 'index-path';
    /**
     * Command line option that represents the host part of the URI.
     */
    const CLO_HOST = 'host';

    /**
     * Parser constructor.
     */
    final public function __construct()
    {
        parent::__construct();
        $this->addArgument(self::CLA_INDEX_PATH, InputArgument::OPTIONAL, 'Path to the file used to store the index.', 'index.json')
            ->addOption(self::CLO_HOST, null, InputOption::VALUE_REQUIRED, 'Host part of the URI (ex: http://domain.com)', '');
    }

    /**
     * {@inheritdoc}
     * @see Command
     */
    protected function configure()
    {
        $this->setName('dump')
             ->setDescription('Dump the index');
    }

    /**
     * {@inheritdoc}
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $error = null;

        // Get the CLI configuration.
        $indexPath = $input->getArgument(self::CLA_INDEX_PATH);
        $host = $input->getOption(self::CLO_HOST);

        if (false === $index = file_get_contents($indexPath)) {
            throw new \Exception("Can not load file \"$indexPath\".");
        }

        $data = json_decode($index, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Can not JSON decode the index \"$indexPath\": " . json_last_error_msg());
        }

        $output = array();
        /**
         * @var string $_uriPrefix
         * @var array $_data
         */
        $decal = sprintf("%-7s", '');
        foreach ($data as $_uriPrefix => $_data) {

            $controllerPath = $_data[CtrlIndexer::KEY_CONTROLLER][CtrlIndexer::KEY_CONTROLLER_PATH];
            $controllerClass = $_data[CtrlIndexer::KEY_CONTROLLER][CtrlIndexer::KEY_CONTROLLER_CLASS];

            foreach ($_data[CtrlIndexer::KEY_ACTIONS] as $__data) {
                $output[] = sprintf("%-7s", strtoupper($__data[CtrlIndexer::KEY_HTTP_METHOD])) . $host . "/$_uriPrefix/" . $__data[CtrlIndexer::KEY_ACTION_URI];
                $output[] = $decal . $controllerPath;
                $output[] = $decal . $controllerClass . '::' . $__data[CtrlIndexer::KEY_METHOD];
            }
        }
        print implode(PHP_EOL, $output) . PHP_EOL;
    }
}