<?php

/*
 * The MIT License
 *
 * Copyright 2014 ekow.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace yentu\tests\cases;

use clearice\io\Io;
use \org\bovigo\vfs\vfsStream;
use yentu\exceptions\CommandException;
use yentu\tests\TestBase;

class InitTest extends TestBase
{

    public function setUp() : void
    {
        $this->testDatabase = 'yentu_tests';
        parent::setup();
        $this->createDb($GLOBALS['DB_NAME']);
        $this->connect($GLOBALS['DB_FULL_DSN']);
        $this->setupStreams();
        $this->initYentu('dummy', false);
    }

    public function testParameters()
    {
        $initCommand = $this->getCommand('init');

        ob_start();
        $initCommand->setOptions(array(
            'driver' => $GLOBALS['DRIVER'],
            'host' => $GLOBALS['DB_HOST'],
            'dbname' => $GLOBALS['DB_NAME'],
            'user' => $GLOBALS['DB_USER'],
            'password' => $GLOBALS['DB_PASSWORD'],
            'file' => $GLOBALS['DB_FILE']
        ));
        $initCommand->run();
        $this->runAssertions();
    }

    private function runAssertions()
    {
        $output = ob_get_clean();
        $this->assertEquals(true, file_exists(vfsStream::url("home/yentu")));
        $this->assertEquals(true, is_dir(vfsStream::url("home/yentu")));
        $this->assertEquals(true, file_exists(vfsStream::url("home/yentu/config")));
        $this->assertEquals(true, is_dir(vfsStream::url("home/yentu/config")));
        $this->assertEquals(true, file_exists(vfsStream::url("home/yentu/migrations")));
        $this->assertEquals(true, is_dir(vfsStream::url("home/yentu/migrations")));
        $this->assertEquals(true, is_file(vfsStream::url('home/yentu/config/default.conf.php')));

        // provides $config below
        $config = require(vfsStream::url('home/yentu/config/default.conf.php'));

        $this->assertEquals(array(
            'driver' => $GLOBALS['DRIVER'],
            'host' => $GLOBALS['DB_HOST'],
            'port' => '',
            'dbname' => $GLOBALS['DB_NAME'],
            'user' => $GLOBALS['DB_USER'],
            'password' => $GLOBALS['DB_PASSWORD'],
            'file' => $GLOBALS['DB_FILE']
            ), $config['db']);

        $this->assertTableExists('yentu_history');
        $this->assertColumnExists('session', 'yentu_history');
        $this->assertColumnExists('version', 'yentu_history');
        $this->assertColumnExists('method', 'yentu_history');
        $this->assertColumnExists('arguments', 'yentu_history');
        $this->assertColumnExists('migration', 'yentu_history');
        $this->assertColumnExists('id', 'yentu_history');
    }

    public function testInterractive()
    {
        $this->io->setStreamUrl('error', vfsStream::url("home/error.out"));
        $this->io->setStreamUrl('output', vfsStream::url("home/standard.out"));
        $this->io->setStreamUrl('input', vfsStream::url("home/responses.in"));

        // Write the input for the interractive test
        if (getenv('YENTU_HOST') === false) {
            file_put_contents(vfsStream::url("home/responses.in"), "{$GLOBALS['DRIVER']}\n"
                . "{$GLOBALS['DB_FILE']}\n"
            );
        } else {
            file_put_contents(vfsStream::url("home/responses.in"), "{$GLOBALS['DRIVER']}\n"
                . "{$GLOBALS['DB_HOST']}\n"
                . "\n"
                . "{$GLOBALS['DB_NAME']}\n"
                . "{$GLOBALS['DB_USER']}\n"
                . "{$GLOBALS['DB_PASSWORD']}\n"
            );
        }

        $initCommand = $this->getCommand('init');
        //$this->migrations->setDefaultHome(vfsStream::url('home/yentu'));
        ob_start();
        $initCommand->setOptions(['interractive' => true]);
        $initCommand->run();
        $this->runAssertions();
    }

    public function testUnwritable()
    {
        $this->expectException(CommandException::class);
        vfsStream::setup('home', 0444);
        $initCommand = $this->getCommand('init');
        //$this->migrations->setDefaultHome(vfsStream::url("home/yentu"));
        $this->io->setOutputLevel(Io::OUTPUT_LEVEL_0);
        $initCommand->run( [
            'driver' => 'postgresql',
            'host' => $GLOBALS['DB_HOST'],
            'dbname' => $GLOBALS['DB_NAME'],
            'user' => $GLOBALS['DB_USER'],
            'password' => $GLOBALS['DB_PASSWORD']
        ]);
    }

    public function testExistingDir()
    {
        $this->expectException(CommandException::class);
        mkdir(vfsStream::url('home/yentu'));
        $initCommand = $this->getCommand('init');
        //$this->migrations->setDefaultHome(vfsStream::url("home/yentu"));
        $initCommand->run([
            'driver' => 'postgresql',
            'host' => $GLOBALS['DB_HOST'],
            'dbname' => $GLOBALS['DB_NAME'],
            'user' => $GLOBALS['DB_USER'],
            'password' => $GLOBALS['DB_PASSWORD']
        ]);
    }

    public function testNoParams()
    {
        $this->expectException(CommandException::class);
        $initCommand = $this->getCommand('init');
        //$this->migrations->setDefaultHome(vfsStream::url("home/yentu"));
        $initCommand->run([]);
    }

    public function testExistingDb()
    {
        $this->expectException(CommandException::class);
        $this->pdo->query('CREATE TABLE yentu_history(dummy INTEGER)');
        $initCommand = $this->getCommand('init');
        //$this->migrations->setDefaultHome(vfsStream::url("home/yentu"));
        $initCommand->run([
            'driver' => $GLOBALS['DRIVER'],
            'host' => $GLOBALS['DB_HOST'],
            'dbname' => $GLOBALS['DB_NAME'],
            'user' => $GLOBALS['DB_USER'],
            'password' => $GLOBALS['DB_PASSWORD'],
            'file' => $GLOBALS['DB_FILE']
        ]);
    }

}
