<?php

/**
 * This file implements the controller0 indexer.
 */

namespace dbeurive\Slim\bin\application;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use dbeurive\Slim\controller\Explorer;
use dbeurive\Slim\controller\Indexer as CtrlIndexer;

/**
 * Class Indexer
 *
 * This class implements the controller0 indexer.
 *
 * @package dbeurive\Slim\bin\application
 */
class Indexer extends Command
{
    /**
     * Command line argument that represents the path to the base directory to scan for controllers.
     */
    const CLA_CONTROLLER_BASE_DIRECTORY = 'controllers-base-directory';
    /**
     * Command line option that represents the path to the created index.
     */
    const CLO_INDEX_PATH = 'index-path';
    /**
     * Command line option that represents the suffix of the (PHP) files that implement controllers.
     */
    const CLO_SOURCE_SUFFIX = 'suffix';
    /**
     * Command line option that represents the number of sub directories, below the base directory, under which controllers must be stored.
     */
    const CLO_PATH_DEPTH = 'depth';

    /**
     * This key is used within the generated index.
     * It identifies the HTTP method ("GET", "POST"...) assigned to the action.
     */
    const KEY_HTTP_METHOD = 'http-method';
    /**
     * This key is used within the generated index.
     * It identifies the URI associated to the action.
     */
    const KEY_ACTION_URI = 'action-uri';
    /**
     * This key is used within the generated index.
     * It identifies the controller0 class.
     */
    const KEY_CONTROLLER_CLASS = 'class';
    /**
     * This key is used within the generated index.
     * It identifies the method that implements the action.
     */
    const KEY_METHOD = 'method';
    /**
     * This key is used within the generated index.
     * It identifies the path to the file that implements the controller0.
     */
    const KEY_CONTROLLER_PATH = 'path';

    /**
     * Parser constructor.
     */
    final public function __construct()
    {
        parent::__construct();
        $this->addOption(self::CLO_INDEX_PATH, null, InputOption::VALUE_REQUIRED, 'Path to the file used to store the index', 'index.json')
             ->addOption(self::CLO_SOURCE_SUFFIX, null, InputOption::VALUE_REQUIRED, "Suffix for the PHP files that implement the controllers", 'Controller.php')
             ->addOption(self::CLO_PATH_DEPTH, null, InputOption::VALUE_REQUIRED, "Controller URI depth", 0)
             ->addArgument(self::CLA_CONTROLLER_BASE_DIRECTORY, InputArgument::REQUIRED, 'Path to the base directory that contains the controllers.');
    }

    /**
     * {@inheritdoc}
     * @see Command
     */
    protected function configure()
    {
        $this->setName('index')
             ->setDescription("Indexes the controllers.");
    }

    /**
     * {@inheritdoc}
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the CLI configuration.
        $indexPath = $input->getOption(self::CLO_INDEX_PATH);
        $controllerSuffix = $input->getOption(self::CLO_SOURCE_SUFFIX);
        $baseDirectory = realpath($input->getArgument(self::CLA_CONTROLLER_BASE_DIRECTORY));
        $depth = $input->getOption(self::CLO_PATH_DEPTH);
        $verbosityLevel = $output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;

        $explorer = new Explorer($baseDirectory, $controllerSuffix, $depth, $verbosityLevel);
        $ctrlData = $explorer->explore();

        // Build the index.
        $index = CtrlIndexer::index($ctrlData);
        $json = json_encode($index);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Error while converting the index into JSON. Error: " . json_last_error_msg());
        }

        if (false === file_put_contents($indexPath, $json)) {
            throw new \Exception("Can not open output file \"$indexPath\".");
        }
    }
}