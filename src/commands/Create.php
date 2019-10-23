<?php

namespace yentu\commands;

use yentu\Yentu;
use yentu\exceptions\CommandException;
use clearice\io\Io;

class Create implements CommandInterface
{
    /**
     * @throws CommandException
     */
    public function run($args)
    {
        if (isset($this->options['__args'])) {
            $this->createFile($args['__args'][0]);
        } else {
            $this->checkName(null);
        }
    }

    /**
     * @param $name
     * @throws CommandException
     */
    private function checkExisting($name)
    {
        if (count(glob($this->yentu->getPath("migrations/*_{$name}.php"))) > 0) {
            throw new CommandException("A migration already exists with the name {$name}");
        }
    }

    /**
     * @throws CommandException
     */
    private function checkPermission()
    {
        if (!file_exists($this->yentu->getPath("migrations/"))) {
            throw new CommandException("The migrations directory `" . $this->yentu->getPath("migrations/") . "` does not exist.");
        }
        if (!is_dir($this->yentu->getPath("migrations/"))) {
            throw new CommandException($this->yentu->getPath("migrations/") . ' is not a directory');
        }
        if (!is_writable($this->yentu->getPath("migrations/"))) {
            throw new CommandException("You do not have the permission to write to " . $this->yentu->getPath("migrations/"));
        }
    }

    /**
     * @param $name
     * @throws CommandException
     */
    private function checkName($name)
    {
        if ($name == '') {
            throw new CommandException(
                "Please provide a name for your new migration"
            );
        } else if (!preg_match("/[a-z][a-z0-9\_]*/", $name)) {
            throw new CommandException(
                "Migration names must always start with a lowercase alphabet and "
                . "can only consist of lower case alphabets, numbers and underscores."
            );
        }
    }

    /**
     * @param $name
     * @throws CommandException
     */
    public function createFile($name)
    {
        $this->checkExisting($name);
        $this->checkName($name);
        $this->checkPermission();

        $timestamp = gmdate('YmdHis', time());
        $code = new \yentu\CodeWriter();
        $code->add('');
        $code->add('begin()');
        $code->add('');
        $code->add('->end();');
        $path = $this->yentu->getPath("migrations/{$timestamp}_{$name}.php");
        file_put_contents($path, $code);
        $this->io->output("Added $path for new migration.\n");
    }

}
