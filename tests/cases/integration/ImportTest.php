<?php

namespace yentu\tests\cases\integration;

use PHPUnit\Framework\Attributes\Group;
use org\bovigo\vfs\vfsStream;
use yentu\commands\Import;
use yentu\exceptions\CommandException;
use yentu\exceptions\NonReversibleCommandException;
use yentu\tests\TestBase;

#[Group('integration')]
class ImportTest extends TestBase
{

    public function setUp() : void
    {
        $this->testDatabase = 'yentu_import_test';
        parent::setup();
        $this->createDb($GLOBALS['DB_NAME']);
        $this->initYentu($GLOBALS['DB_NAME']);
    }

    public function testImport()
    {
        $this->initDb($GLOBALS['DB_FULL_DSN'], file_get_contents("tests/sql/{$GLOBALS['DRIVER']}/system.sql"));
        $codeWriter = $this->createStub(\yentu\CodeWriter::class);
        $codeWriter->method('getTimestamp')->willReturn('25th August, 2014 14:30:13');

        $import = $this->getCommand('import', [$codeWriter]);
        $description = $import->run(array());
        $newVersion = $import->getNewVersion();
        $this->assertFileExists(
            vfsStream::url("home/yentu/migrations/{$newVersion}_import.php")
        );

        require "tests/expected/{$GLOBALS['DRIVER']}/import.php";
        $descriptionArray = $description->getArray();

        unset($descriptionArray['tables']['yentu_history']);
        if (isset($descriptionArray['schemata']['main'])) {
            unset($descriptionArray['schemata']['main']);
        }
        if (isset($descriptionArray['schemata']['public'])) {
            unset($descriptionArray['schemata']['public']);
        }
        if (isset($descriptionArray['schemata'][$GLOBALS['DB_NAME']])) {
            unset($descriptionArray['schemata'][$GLOBALS['DB_NAME']]);
        }
        $this->assertEquals(
            $expectedDescription, [
            'schemata' => $descriptionArray['schemata'],
            'tables' => $descriptionArray['tables'],
            'views' => $descriptionArray['views']
        ]);
    }

    public function testSchemaImport()
    {
        $this->skipSchemaTests();
        $this->connect($GLOBALS['DB_FULL_DSN']);
        $this->pdo->query('DROP SCHEMA IF EXISTS hr');
        $this->pdo->query('DROP SCHEMA IF EXISTS common');

        $this->initDb($GLOBALS['DB_FULL_DSN'], file_get_contents("tests/sql/{$GLOBALS['DRIVER']}/import_schema.sql"));
        $this->connect($GLOBALS['DB_FULL_DSN']);

        $codeWriter = $this->createStub(\yentu\CodeWriter::class);
        $codeWriter->method('getTimestamp')->willReturn('25th August, 2014 14:30:13');
        $import = new Import($this->migrations, $this->getManipulatorFactory(), $this->io, $codeWriter);
        $import->run(array());
        $newVersion = $import->getNewVersion();
        $this->assertFileExists(
            vfsStream::url("home/yentu/migrations/{$newVersion}_import.php")
        );
        $this->assertSchemaExists('common');
        $this->assertSchemaExists('hr');
    }

    public function testViewImport()
    {
        $this->initDb($GLOBALS['DB_FULL_DSN'], file_get_contents("tests/sql/{$GLOBALS['DRIVER']}/import_views.sql"));
        $this->connect($GLOBALS['DB_FULL_DSN']);

        $codeWriter = $this->createStub(\yentu\CodeWriter::class);
        $codeWriter->method('getTimestamp')->willReturn('25th August, 2014 14:30:13');
        $import = new Import($this->migrations, $this->getManipulatorFactory(), $this->io, $codeWriter);
        $import->run(array());
        $newVersion = $import->getNewVersion();
        $this->assertFileExists(
            vfsStream::url("home/yentu/migrations/{$newVersion}_import.php")
        );

        $this->assertTableExists('employees_view');
        $this->assertTableExists('disabled_employees_view');
    }

    public function testImportNonEmptyMigrations()
    {
        $this->expectException(NonReversibleCommandException::class);
        file_put_contents(vfsStream::url('home/yentu/migrations/1234568901234_existing.php'), 'nothing');
        $codeWriter = $this->createStub(\yentu\CodeWriter::class);
        $codeWriter->method('getTimestamp')->willReturn('25th August, 2014 14:30:13');
        $import = new Import($this->migrations, $this->getManipulatorFactory(), $this->io, $codeWriter);
        $import->run(array());
    }

    public function testDatabaseNotExisting()
    {
        $this->skipSchemaTests();
        $this->connect($GLOBALS['DB_FULL_DSN']);
        try {
            $this->pdo->query('DROP TABLE IF EXISTS yentu_history');
            $this->pdo->query('DROP SEQUENCE IF EXISTS yentu_history_id_seq');
        } catch (\PDOException $e) {
            
        }
        $codeWriter = $this->createStub(\yentu\CodeWriter::class);
        $codeWriter->method('getTimestamp')->willReturn('25th August, 2014 14:30:13');
        $import = new Import($this->migrations, $this->getManipulatorFactory(), $this->io, $codeWriter);
        $import->run(array());
        $this->assertTableExists('yentu_history');
    }

}

class NegativeMockAssertor
{

    public function __call($name, $arguments)
    {
        return false;
    }

}
