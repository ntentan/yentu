<?php

namespace yentu\tests\cases\unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\Parameters;

#[Group('unit')]
class ParametersTest extends TestCase
{
    public function testParseDsnMysql()
    {
        $dsn = 'mysql:host=localhost;port=3306;dbname=test_db;charset=utf8mb4';
        $config = Parameters::parseDsn($dsn);

        $this->assertEquals([
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => '3306',
            'dbname' => 'test_db',
            'charset' => 'utf8mb4',
        ], $config);
    }

    public function testParseDsnPostgresql()
    {
        $dsn = 'pgsql:host=localhost;port=5432;dbname=test_db;user=postgres;password=secret';
        $config = Parameters::parseDsn($dsn);

        $this->assertEquals([
            'driver' => 'postgresql',
            'host' => 'localhost',
            'port' => '5432',
            'dbname' => 'test_db',
            'user' => 'postgres',
            'password' => 'secret',
        ], $config);
    }

    public function testParseDsnPostgresqlAlias()
    {
        $dsn = 'postgres:host=db.example.com;dbname=app_db';
        $config = Parameters::parseDsn($dsn);

        $this->assertEquals([
            'driver' => 'postgresql',
            'host' => 'db.example.com',
            'dbname' => 'app_db',
        ], $config);
    }

    public function testParseDsnSqliteFilePath()
    {
        $dsn = 'sqlite:/path/to/my.db';
        $config = Parameters::parseDsn($dsn);

        $this->assertEquals([
            'driver' => 'sqlite',
            'file' => '/path/to/my.db',
        ], $config);
    }

    public function testParseDsnSqliteMemory()
    {
        $dsn = 'sqlite::memory:';
        $config = Parameters::parseDsn($dsn);

        $this->assertEquals([
            'driver' => 'sqlite',
            'file' => ':memory:',
        ], $config);
    }

    public function testParseDsnSqliteKeyValue()
    {
        $dsn = 'sqlite:file=/var/data/test.sqlite';
        $config = Parameters::parseDsn($dsn);

        $this->assertEquals([
            'driver' => 'sqlite',
            'file' => '/var/data/test.sqlite',
        ], $config);
    }

    public function testParseDsnConfigArrayWithOverrides()
    {
        $config = [
            'dsn' => 'mysql:host=localhost;port=3306;dbname=original_db',
            'host' => '192.168.1.100',
            'dbname' => 'overridden_db',
            'user' => 'custom_user',
        ];

        $parsed = Parameters::parseDsn($config);

        $this->assertEquals([
            'driver' => 'mysql',
            'host' => '192.168.1.100',
            'port' => '3306',
            'dbname' => 'overridden_db',
            'user' => 'custom_user',
            'dsn' => 'mysql:host=localhost;port=3306;dbname=original_db',
        ], $parsed);
    }

    public function testSpecificHostOverridesDsn()
    {
        $config = [
            'dsn' => 'pgsql:host=dsn-host;dbname=dsn-db',
            'host' => 'override-host',
        ];

        $parsed = Parameters::parseDsn($config);

        $this->assertEquals('override-host', $parsed['host']);
        $this->assertEquals('dsn-db', $parsed['dbname']);
        $this->assertEquals('postgresql', $parsed['driver']);
    }

    public function testSpecificDbnameOverridesDsn()
    {
        $config = [
            'dsn' => 'pgsql:host=dsn-host;dbname=dsn-db',
            'dbname' => 'override-db',
        ];

        $parsed = Parameters::parseDsn($config);

        $this->assertEquals('dsn-host', $parsed['host']);
        $this->assertEquals('override-db', $parsed['dbname']);
        $this->assertEquals('postgresql', $parsed['driver']);
    }

    public function testNullValuesDoNotOverrideDsn()
    {
        $config = [
            'dsn' => 'mysql:host=localhost;dbname=test_db',
            'host' => null,
            'dbname' => null,
        ];

        $parsed = Parameters::parseDsn($config);

        $this->assertEquals('localhost', $parsed['host']);
        $this->assertEquals('test_db', $parsed['dbname']);
        $this->assertEquals('mysql', $parsed['driver']);
    }

    public function testParseDsnWithoutDsnConfig()
    {
        $config = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'dbname' => 'test_db',
        ];

        $parsed = Parameters::parseDsn($config);

        $this->assertEquals($config, $parsed);
    }

    public function testParseDsnWithQuotedValues()
    {
        $dsn = 'mysql:host="localhost";dbname=\'quoted_db\';port=3306';
        $config = Parameters::parseDsn($dsn);

        $this->assertEquals([
            'driver' => 'mysql',
            'host' => 'localhost',
            'dbname' => 'quoted_db',
            'port' => '3306',
        ], $config);
    }

    public function testParseDsnAlternativeAliases()
    {
        $dsn = 'pgsql:database=alias_db;username=alias_user';
        $config = Parameters::parseDsn($dsn);

        $this->assertEquals([
            'driver' => 'postgresql',
            'database' => 'alias_db',
            'dbname' => 'alias_db',
            'username' => 'alias_user',
            'user' => 'alias_user',
        ], $config);
    }

    public function testInitCreateConfigFileWithDsnAndOverrides()
    {
        \org\bovigo\vfs\vfsStream::setup('home');
        $io = new \clearice\io\Io();
        $driverFactory = new \ntentan\atiaa\DefaultDriverFactory([]);
        $manipulatorFactory = new \yentu\factories\DatabaseManipulatorFactory($driverFactory, $io);
        $migrations = new \yentu\Migrations($io, $manipulatorFactory, [
            'home' => \org\bovigo\vfs\vfsStream::url('home/yentu'),
            'variables' => [],
            'other_migrations' => []
        ]);

        $init = new \yentu\commands\Init($migrations, $manipulatorFactory, $io);
        $params = [
            'dsn' => 'pgsql:host=dsn-host;port=5432;dbname=dsn-db',
            'host' => 'override-host',
            'dbname' => 'override-db',
            'user' => 'postgres',
        ];

        $returnedConfig = $init->createConfigFile($params);

        $this->assertEquals('postgresql', $returnedConfig['driver']);
        $this->assertEquals('override-host', $returnedConfig['host']);
        $this->assertEquals('override-db', $returnedConfig['dbname']);
        $this->assertEquals('5432', $returnedConfig['port']);
        $this->assertEquals('postgres', $returnedConfig['user']);

        $iniFile = \org\bovigo\vfs\vfsStream::url('home/yentu/config/yentu.ini');
        $this->assertFileExists($iniFile);
        $parsedIni = parse_ini_file($iniFile, true);
        $parsedConfig = Parameters::parseDsn($parsedIni['db']);

        $this->assertEquals('postgresql', $parsedConfig['driver']);
        $this->assertEquals('override-host', $parsedConfig['host']);
        $this->assertEquals('override-db', $parsedConfig['dbname']);
        $this->assertEquals('5432', $parsedConfig['port']);
        $this->assertEquals('postgres', $parsedConfig['user']);
    }
}

