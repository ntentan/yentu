<?php

namespace yentu\commands;

use clearice\io\Io;
use yentu\exceptions\NonReversibleCommandException;
use yentu\factories\DatabaseManipulatorFactory;
use yentu\Migrations;
use yentu\Parameters;
use yentu\exceptions\CommandException;
use ntentan\utils\Filesystem;

/**
 * The init command class.
 * This command initiates a project for yentu by creating the required migration directories and configuration files.
 * It also creates the database history table.
 */
class Init extends Command implements Reversible
{
    /**
     * Instance of io for CLI output
     * @var Io
     */
    private $io;

    /**
     * Provides access to migrations.
     * @var Migrations
     */
    private $migrations;

    /**
     * For performing database operations
     * @var DatabaseManipulatorFactory
     */
    private $manipulatorFactory;

    /**
     * Init constructor.
     *
     * @param Migrations $migrations
     * @param DatabaseManipulatorFactory $manipulatorFactory
     * @param Io $io
     */
    public function __construct(Migrations $migrations, DatabaseManipulatorFactory $manipulatorFactory, Io $io)
    {
        $this->migrations = $migrations;
        $this->io = $io;
        $this->manipulatorFactory = $manipulatorFactory;
    }

    /**
     * Extract parameters from command line arguments, or through interactive sessions.
     * @return array
     */
    private function getParams() : array
    {
        if (isset($this->options['interractive'])) {
            $params['driver'] = $this->io->getResponse(
                'What type of database are you working with?', 
                ['required' => true, 'answers' => ['postgresql', 'mysql', 'sqlite']]
            );

            if ($params['driver'] === 'sqlite') {
                $params['file'] = $this->io->getResponse('What is the path to your database file?', ['required' => true]);
            } else {
                $params['host'] = $this->io->getResponse('What is the host of your database connection?', ['default' => 'localhost']);
                $params['port'] = $this->io->getResponse('What is the port of your database connection? (Leave blank for default)');
                $params['user'] = $this->io->getResponse('What username do you connect with?', ['required' => true]);
                $params['password'] = $this->io->getResponse("What is the password for {$params['user']}?", ['required' => FALSE]);
                $params['dbname'] = $this->io->getResponse("What is the name database (schema) are you connecting to?",['required' => true]);
            }
        } else {
            $params = [];
            foreach(['driver', 'file', 'host', 'port', 'dbname', 'user', 'password'] as $key) {
                if(isset($this->options[$key])) {
                    $params[$key] = $this->options[$key];
                }
            }
        }
        return $params;
    }

    /**
     * @param $params
     * @return array
     * @throws \ntentan\utils\exceptions\FileAlreadyExistsException
     * @throws \ntentan\utils\exceptions\FileNotWriteableException
     */
    public function createConfigFile($params) : array
    {
        $params = Parameters::wrap(
            $params, ['port', 'file', 'host', 'dbname', 'user', 'password']
        );
        Filesystem::directory($this->migrations->getPath('config'))->create(true);
        Filesystem::directory($this->migrations->getPath('migrations'))->create(true);
        Filesystem::file($this->migrations->getPath('config/yentu.ini'))->putContents(
            <<<CONFIG
            [db]
            driver: {$params['driver']}
            host: {$params['host']}
            port: {$params['port']}
            dbname: {$params['dbname']}
            user: {$params['user']}
            password: {$params['password']}
            file: {$params['file']}
            CONFIG
        );

        return $params;
    }

    /**
     * @throws CommandException
     */
    public function run() : void
    {
        $home = $this->migrations->getPath('');
        if (file_exists($home)) {
            throw new NonReversibleCommandException("Could not initialize yentu. Your project has already been initialized with yentu.");
        } else if (!is_writable(dirname($home))) {
            throw new NonReversibleCommandException("Your home directory ($home) could not be created.");
        }

        $params = $this->getParams();
        
        if (count($params) == 0 && defined('STDOUT')) {
            throw new NonReversibleCommandException(
                "You didn't provide any parameters for initialization. Please execute yentu "
                . "with `init -i` to initialize yentu interractively. "
                . "You can also try `init --help` for more information."
            );
        }

        $config = $this->createConfigFile($params);
        $db = $this->manipulatorFactory->createManipulatorWithConfig($config);

        if ($db->getAssertor()->doesTableExist('yentu_history')) {
            throw new CommandException("Could not initialize yentu. Your database may have been initialized with yentu; the 'yentu_history' table already exists.");
        }

        $db->createHistory();
        $db->disconnect();

        $this->io->output("Yentu successfully initialized.\n");
    }

    public function reverseActions() : void
    {
        Filesystem::directory($this->migrations->getPath(""))->delete();
    }

}
