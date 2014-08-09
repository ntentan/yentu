<?php
namespace yentu;

class Importer
{
    private $db;
    private $code;
    private $hasSchema = false;
    
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
        
        file_put_contents('yentu/migrations/seed.php', $this->code);
    }
    
    protected function importColumns($columns)
    {
        foreach($columns as $column)
        {
            $this->code->addNoLn("->column('{$column['name']}')");
            $this->code->addNoIndent("->type('{$column['type']}')");
            $this->code->addNoIndent("->nulls(" . ($column['nulls'] ? 'true' : 'false') . ")");
            
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
            
            $this->code->decreaseIndent();
            $this->code->ln();
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
