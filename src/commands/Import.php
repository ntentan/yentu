<?php
namespace yentu\commands;

use yentu\DatabaseManipulator;
use yentu\CodeWriter;
use yentu\Yentu;

class Import implements \clearice\Command, \yentu\Reversible
{
    /**
     *
     * @var \yentu\DatabaseDriver
     */
    private $db;
    private $code;
    private $hasSchema = false;
    private $foreignKeys = array();
    private $newVersion;
    
    private function initializeCodeWriter()
    {
        if(!is_object($this->code))
        {
            $this->code = new CodeWriter();
        }
    }
    
    public function setCodeWriter($code)
    {
       $this->code = $code; 
    }

    public function run($options=array())
    {        
        Yentu::greet();
        $this->db = DatabaseManipulator::create();
        $this->initializeCodeWriter();
        $files = scandir(Yentu::getPath("migrations"));
        if(count($files) > 2)
        {
            throw new \yentu\exceptions\CommandException("Cannot run imports. Your migrations directory is not empty");
        }
        $description = $this->db->getDescription();
        
        $this->code->add('begin()');
        if(isset($description['schemata']))
        {
            $this->importSchemata($description['schemata']);
        }
        if(isset($description['tables']))
        {
            $this->importTables($description['tables']);
        }
        if(isset($description['views']))
        {
            $this->importViews($description['views']);
        }
        
        $this->importForeignKeys();
        $this->code->add('->end();');
        
        $this->newVersion = date('YmdHis', time());
        $path = Yentu::getPath("migrations/{$this->newVersion}_import.php");
        file_put_contents($path, $this->code);
        \clearice\ClearIce::output("Created `$path`\n");
        if(!$this->db->getAssertor()->doesTableExist('yentu_history'))
        {
            $this->db->createHistory();
        }
        $this->db->setVersion($this->newVersion);
        $this->db->disconnect();
        
        return $description;
    }
    
    public function getNewVersion()
    {
        return $this->newVersion;
    }
    
    private function generateSchemaCode($description, $ref = false, $prefix = '')
    {
        $refprefix = $ref === true ? 'ref' : '->';
        if($description["{$prefix}schema"] == false)
        {
            return "{$refprefix}table('{$description["{$prefix}table"]}')";
        }
        else
        {
            return "{$refprefix}schema('{$description["{$prefix}schema"]}')->table('{$description["{$prefix}table"]}')";
        }
    }
    
    protected function importForeignKeys()
    {
        foreach($this->foreignKeys as $name => $foreignKey)
        {
            //$this->code->add("\$this->schema('{$foreignKey['schema']}')->table('{$foreignKey['table']}')");
            $this->code->add($this->generateSchemaCode($foreignKey));
            $this->code->addIndent();
            $this->code->add("->foreignKey('" . implode("','", $foreignKey['columns']) . "')");
            $reference = $this->generateSchemaCode($foreignKey, true, 'foreign_');
            $this->code->add("->references({$reference})");
            $this->code->add("->columns('" . implode("','", $foreignKey['foreign_columns']) . "')");
            
            if($foreignKey['on_delete'] != '')
            {
                $this->code->add("->onDelete('{$foreignKey['on_delete']}')");
            }
            
            if($foreignKey['on_update'] != '')
            {
                $this->code->add("->onUpdate('{$foreignKey['on_update']}')");
            }
            
            $this->code->add("->name('$name')");
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
            
            if($column['length'] != '')
            {
                $this->code->addNoIndent("->length({$column['length']})");
            }
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
        
        if(count($schemata) > 0)
        {
            $this->hasSchema = true;
        }
        
        foreach($schemata as $schema)
        {
            $this->code->add("->schema('{$schema['name']}')");
            $this->code->addIndent();
            $this->importTables($schema['tables']);
            $this->importViews($schema['views']);
            $this->code->decreaseIndent(); 
        }        
        
        $this->hasSchema = false;
    }

    protected function importViews($views)
    {
        foreach($views as $view)
        {
            $definition = sprintf('->definition("%s")', str_replace('"', '\"', $view['definition']));
            $this->code->add("->view('{$view['name']}')$definition");
            $this->code->ln();
        }
    }
    
    protected function importTables($tables)
    {
        foreach($tables as $table)
        {
            if($table['name'] == 'yentu_history') continue;
            
            $this->code->add("->table('{$table['name']}')");
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
            $constraint = implode("','", $constraint['columns']);
            $this->code->add("->$type('$constraint')->name('$name')");
        }
    }

    public function reverse()
    {
        
    }

}

