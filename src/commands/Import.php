<?php
namespace yentu\commands;

use yentu\DatabaseDriver;
use yentu\CodeWriter;

class Import implements \yentu\Command
{
    private $db;
    private $code;
    private $hasSchema = false;
    private $foreignKeys = array();
    
    public function __construct()
    {
        $this->db = DatabaseDriver::getConnection();
        $this->code = new CodeWriter();
    }
    
    public function run($options)
    {
        $files = scandir("yentu/migrations");
        if(count($files) > 2)
        {
            throw new \Exception("Cannot run imports. Your migrations directory is not empty");
        }
        $description = $this->db->getDescription();
        
        if(isset($description['schemata']))
        {
            $this->importSchemata($description['schemata']);
        }
        if(isset($description['tables']))
        {
            $this->importTables($description['tables']);
        }
        
        $this->importForeignKeys();
        $timestamp = date('YmdHis', time());
        file_put_contents("yentu/migrations/{$timestamp}_import.php", $this->code);
        $this->db->setVersion($timestamp);
    }
    
    protected function importForeignKeys()
    {
        foreach($this->foreignKeys as $name => $foreignKey)
        {
            $this->code->add("\$this->schema('{$foreignKey['schema']}')->table('{$foreignKey['table']}')");
            $this->code->addIndent();
            $this->code->add("->foreignKey('" . implode(',', $foreignKey['columns']) . "')");
            $this->code->add("->references(\$this->schema('{$foreignKey['foreign_schema']}')->table('{$foreignKey['foreign_table']}'))");
            $this->code->add("->columns('" . implode(',', $foreignKey['foreign_columns']) . "')");
            
            if($foreignKey['on_delete'] != '')
            {
                $this->code->add("->onDelete('{$foreignKey['on_delete']}')");
            }
            
            if($foreignKey['on_update'] != '')
            {
                $this->code->add("->onUpdate('{$foreignKey['on_update']}')");
            }
            
            $this->code->add("->name('$name');");
            $this->code->decreaseIndent();
            $this->code->ln();
        }
    }
    
    protected function importColumns($columns)
    {
        foreach($columns as $column)
        {
            $this->code->addNoLn("->column('{$column['name']}')");
            $this->code->addNoIndent("->type('{$column['type']}')");
            $this->code->addNoIndent("->nulls(" . ($column['nulls'] === true ? 'true' : 'false') . ")");
            
            if($column['default'] != '')
            {
                $this->code->addNoIndent("->defaultValue(\"{$column['default']}\")");
            }
            
            $this->code->ln();
        }
    }
    
    protected function importSchemata($schemata)
    {
        $this->code->add('// Schemata');
        $this->hasSchema = true;
        foreach($schemata as $schema)
        {
            $this->code->add("\$this->schema('{$schema['name']}')");
            $this->code->addIndent();
            $this->importTables($schema['tables']);
            $this->code->add(';');
            $this->code->decreaseIndent();
            
        }        
    }
    
    protected function importTables($tables)
    {
        foreach($tables as $table)
        {
            if($this->hasSchema)
            {
                $this->code->add("->table('{$table['name']}')");
            }
            else
            {
                $this->code->add("\$this->table('{$table['name']}')");
            }
            $this->code->addIndent();
            
            $this->importColumns($table['columns']);
            $this->importConstraints('primaryKey', $table['primary_key']);
            
            if($table['auto_increment'])
            {
                $this->code->add("->autoIncrement()");
            }
            $this->importConstraints('unique', $table['unique_keys']);
            $this->importConstraints('index', $table['indices']);
            
            $this->code->decreaseIndent();
            $this->code->ln();
            
            if(count($table['foreign_keys']) > 0)
            {
                $this->foreignKeys = array_merge($this->foreignKeys, $table['foreign_keys']);
            }
        }
    }
    
    protected function importConstraints($type, $constraints)
    {
        foreach($constraints as $name => $constraint)
        {
            $constraint = implode("','", $constraint);
            $this->code->add("->$type('$constraint')->name('$name')");
        }
    }
}
