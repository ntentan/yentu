<?php

namespace yentu\tests\cases\unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\ChangeLogger;
use yentu\database\ItemType;
use yentu\database\Schema;
use yentu\database\Table;
use yentu\database\View;
use yentu\factories\DatabaseItemFactory;

#[Group('unit')]
class ViewTest extends TestCase
{
    private function createSchemaStub(string $name = 'public'): Schema
    {
        $schema = $this->createStub(Schema::class);
        $schema->method('getName')->willReturn($name);
        return $schema;
    }

    public function testNewViewInitializationAndDefinition(): void
    {
        $schema = $this->createSchemaStub('public');
        $logger = $this->createMock(ChangeLogger::class);

        $logger->expects($this->exactly(2))
            ->method('__call')
            ->willReturnCallback(function ($method, $args) {
                if ($method === 'doesViewExist') {
                    $this->assertEquals([
                        'name' => 'active_users',
                        'schema' => 'public',
                        'definition' => null,
                    ], $args[0]);
                    return false;
                }
                if ($method === 'addView') {
                    $this->assertEquals([
                        'name' => 'active_users',
                        'schema' => 'public',
                        'definition' => 'SELECT * FROM users WHERE active = 1',
                    ], $args[0]);
                    return true;
                }
                return null;
            });

        $view = new View('active_users', $schema);
        $view->setChangeLogger($logger);
        $view->init();

        $this->assertTrue($view->isNew());

        $view->definition('SELECT * FROM users WHERE active = 1');
        $this->assertEquals('SELECT * FROM users WHERE active = 1', $view->definition);
    }

    public function testExistingViewDefinitionUpdate(): void
    {
        $schema = $this->createSchemaStub('public');
        $logger = $this->createMock(ChangeLogger::class);

        $logger->expects($this->exactly(2))
            ->method('__call')
            ->willReturnCallback(function ($method, $args) {
                if ($method === 'doesViewExist') {
                    return 'SELECT 1';
                }
                if ($method === 'changeViewdefinition') {
                    $this->assertEquals('SELECT 1', $args[0]['from']['definition']);
                    $this->assertEquals('SELECT 2', $args[0]['to']['definition']);
                    return true;
                }
                return null;
            });

        $view = new View('my_view', $schema);
        $view->setChangeLogger($logger);
        $view->init();

        $this->assertFalse($view->isNew());

        $view->definition('SELECT 2');
        $view->commit();
    }

    public function testViewDrop(): void
    {
        $schema = $this->createSchemaStub('public');
        $logger = $this->createMock(ChangeLogger::class);

        $logger->expects($this->once())
            ->method('__call')
            ->with('dropView', $this->callback(function ($args) {
                return $args[0]['name'] === 'old_view'
                    && $args[0]['schema'] === 'public';
            }))
            ->willReturn(true);

        $view = new View('old_view', $schema);
        $view->setChangeLogger($logger);

        $view->drop();
    }

    public function testViewFactoryDelegation(): void
    {
        $schema = $this->createSchemaStub('public');
        $view = new View('active_users', $schema);

        $factory = $this->createMock(DatabaseItemFactory::class);
        $subView = $this->createStub(View::class);
        $subTable = $this->createStub(Table::class);

        $factory->expects($this->exactly(2))
            ->method('create')
            ->willReturnCallback(function ($type, $name, $argSchema) use ($schema, $subView, $subTable) {
                $this->assertSame($schema, $argSchema);
                if ($type === ItemType::View && $name === 'sub_view') {
                    return $subView;
                }
                if ($type === ItemType::Table && $name === 'users') {
                    return $subTable;
                }
                return null;
            });

        $view->setFactory($factory);

        $this->assertSame($subView, $view->view('sub_view'));
        $this->assertSame($subTable, $view->table('users'));
    }
}
