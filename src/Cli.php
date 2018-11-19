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

    public function __construct(Io $io, Command $command = null, ArgumentParser $argumentParser)
    {
        $this->command = $command;
        $this->io = $io;
        $this->argumentParser = $argumentParser;
    }

    public function run()
    {
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
                    $yentu->reverseCommand($command);
                }
            } catch (\yentu\exceptions\DatabaseManipulatorException $e) {
                $this->io->resetOutputLevel();
                $this->io->error("Failed to perform database action: " . $e->getMessage() . "\n");
                if (isset($command)) {
                    $yentu->reverseCommand($command);
                }
            } catch (\ntentan\atiaa\DescriptionException $e) {
                $this->io->resetOutputLevel();
                $this->io->error("Failed to perform database action: " . $e->getMessage() . "\n");
                if (isset($command)) {
                    $yentu->reverseCommand($command);
                }
            } catch (\yentu\exceptions\SyntaxErrorException $e) {
                $this->io->resetOutputLevel();
                $this->io->error("Error found in syntax: {$e->getMessage()}\n");
                if (isset($command)) {
                    $yentu->reverseCommand($command);
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
