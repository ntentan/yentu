<?php

namespace yentu;

use clearice\argparser\ArgumentParser;
use clearice\io\Io;
use yentu\commands\Command;
use SebastianBergmann\Version;
use function Symfony\Component\String\u;

class Cli
{
    private ?Command $command;
    private Io $io;
    private ArgumentParser $argumentParser;
    private const string VERSION = "v0.4.0";

    public function __construct(Io $io, ArgumentParser $argumentParser, ?Command $command = null)
    {
        $this->command = $command;
        $this->io = $io;
        $this->argumentParser = $argumentParser;
    }

    /**
     * Display the greeting for the CLI user interface.
     */
    private function greet(): void
    {
        $version = $this->getVersion();
        $welcome = <<<WELCOME
        Yentu Database Migration Tool
        Version $version


        WELCOME;
        $this->io->output($welcome);
    }

    private function getVersion(): string
    {
        if (defined('PHING_BUILD_VERSION')) {
            return PHING_BUILD_VERSION;
        } else {
            $version = new Version(self::VERSION, dirname(__DIR__));
            return $version->asString();
        }
    }

    public function run(): int
    {
        $this->greet();
        $status = 0;

        if($this->command === null) {
            $this->io->error($this->argumentParser->getHelpMessage());
        } else {
            try {
                $this->command->run();
            } catch (\yentu\exceptions\NonReversibleCommandException $e) {
                $this->io->resetOutputLevel();
                $this->io->error("\nError: " . $e->getMessage() . "\n");
                $status = 1;
            } 
            catch (\ntentan\atiaa\exceptions\DatabaseDriverException $e) {
                $this->io->resetOutputLevel();
                $this->io->error("\nDatabase error: " . $e->getMessage() . "\n");
                $this->command->reverse();
                $status = 2;
            } 
            catch (\yentu\exceptions\YentuException $e) {
                $this->io->resetOutputLevel();
                $this->io->error("\nError: " . $e->getMessage() . "\n");
                $this->command->reverse();
                $status = 3;
            } catch (\PDOException $e) {
                $this->io->resetOutputLevel();
                $this->io->error("\nFailed to connect to database: {$e->getMessage()}\n");
                $status = 4;
            } catch (\ntentan\utils\exceptions\FileNotFoundException $e) {
                $this->io->resetOutputLevel();
                $this->io->error($e->getMessage() . "\n");
                $status = 5;
            }
        }

        return $status;
    }
}
