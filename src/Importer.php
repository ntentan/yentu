<?php
namespace yentu;

class Importer
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
    
    public function run()
    {
        $description = $this->db->describe();
        
        if(isset($description['schemata']))
        {
            $this->importSchemata($description['schemata']);
        }
        
        if(isset($description['tables']))
        {
            $this->importTables($description['tables']);
        }
        
        var_dump($this->foreignKeys);
        
        $this->importForeignKeys($description['foreign_keys']);
        file_put_contents('yentu/migrations/seed.php', $this->code);
    }
    
    protected function importForeignKeys($foreignKeys)
    {
        var_dump($foreignKeys);
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
            
            $this->code->decreaseIndent();
            $this->code->ln();
            
            if(count($table['foreign_keys']) > 0)
            {
                $this->foreignKeys[] = $table['foreign_keys'];
            }
        }
    }
    
    protected function importConstraints($type, $constraints)
    {
        foreach($constraints as $constraint)
        {
            $constraint = implode("','", $constraint);
            $this->code->add("->$type('$constraint')");
        }
    }
}
