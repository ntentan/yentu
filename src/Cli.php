<?php

namespace yentu;

use clearice\argparser\ArgumentParser;
use clearice\io\Io;
use yentu\commands\Command;

class Cli
{
    private $command;
    private $io;
    private $argumentParser;

    public function __construct(Io $io, ArgumentParser $argumentParser, Command $command = null)
    {
        $this->command = $command;
        $this->io = $io;
        $this->argumentParser = $argumentParser;
    }

    /**
     * Display the greeting for the CLI user interface.
     */
    private function greet()
    {
        $version = $this->getVersion();
        $welcome = <<<WELCOME
Yentu Database Migration Tool
Version $version


WELCOME;
        $this->io->output($welcome);
    }

    private function getVersion()
    {
        if (defined('PHING_BUILD_VERSION')) {
            return PHING_BUILD_VERSION;
        } else {
            $version = new \SebastianBergmann\Version(Yentu::VERSION, dirname(__DIR__));
            return $version->getVersion();
        }
    }

    public function run()
    {
        $this->greet();
        if($this->command) {
            try {
                $this->command->run();
            } catch (\yentu\exceptions\CommandException $e) {
                $this->io->resetOutputLevel();
                $this->io->error("Error! " . $e->getMessage() . "\n");
            } catch (\ntentan\atiaa\exceptions\DatabaseDriverException $e) {
                $this->io->resetOutputLevel();
                $this->io->error("Database driver failed: " . $e->getMessage() . "\n");
                if (isset($command)) {
                    $command->reverse();
                }
            } catch (\yentu\exceptions\DatabaseManipulatorException $e) {
                $this->io->resetOutputLevel();
                $this->io->error("Failed to perform database action: " . $e->getMessage() . "\n");
                if (isset($command)) {
                    $command->reverse();
                }
            } catch (\ntentan\atiaa\DescriptionException $e) {
                $this->io->resetOutputLevel();
                $this->io->error("Failed to perform database action: " . $e->getMessage() . "\n");
                if (isset($command)) {
                    $command->reverse();
                }
            } catch (\yentu\exceptions\SyntaxErrorException $e) {
                $this->io->resetOutputLevel();
                $this->io->error("Error found in syntax: {$e->getMessage()}\n");
                if (isset($command)) {
                    $command->reverse();
                }
            } catch (\PDOException $e) {
                $this->io->resetOutputLevel();
                $this->io->error("Failed to connect to database: {$e->getMessage()}\n");
            } catch (\ntentan\utils\exceptions\FileNotFoundException $e) {
                $this->io->resetOutputLevel();
                $this->io->error($e->getMessage() . "\n");        
            }
    
        } else {
            $this->io->error($this->argumentParser->getHelpMessage());
        }
    }
}
