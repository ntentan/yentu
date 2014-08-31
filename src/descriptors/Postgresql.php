<?php
namespace yentu\descriptors;

class Postgresql extends \yentu\SchemaDescriptor
{
    const TO_YENTU = 'yentu';
    const TO_POSTGRESQL = 'pgsql';
    
    /**
     * 
     * @note Query sourced from http://stackoverflow.com/questions/2204058/show-which-columns-an-index-is-on-in-postgresql
     * @param type $table
     * @return type
     */
    protected function getIndices($table)
    {
        $constraints = array();        
        $constraintColumns = $this->driver->query(
            sprintf("select
                        t.relname as table_name,
                        i.relname as index_name,
                        a.attname as column_name
                    from
                        pg_class t,
                        pg_class i,
                        pg_index ix,
                        pg_attribute a,
                        pg_namespace n
                    where
                        t.oid = ix.indrelid
                        and i.oid = ix.indexrelid
                        and a.attrelid = t.oid
                        and a.attnum = ANY(ix.indkey)
                        and t.relkind = 'r'
                        and t.relname = '%s'
                        and n.nspname = '%s'
                        and i.relnamespace = n.oid
                            AND indisunique != 't'
                            AND indisprimary != 't'", 
            $table['name'], $table['schema'])
        );
        foreach($constraintColumns as $column)
        {
            $constraints[$column['index_name']][] = $column['column_name'];
        }
        
        return $constraints;        
    }
    
    public static function convertTypes($type, $direction)
    {
        $types = array(
            'integer' => 'integer',
            'bigint' => 'bigint',
            'character varying' => 'string',
            'numeric' => 'double',
            'timestamp with time zone' => 'timestamp',
            'timestamp without time zone' => 'timestamp',
            'text' => 'text',
            'boolean' => 'boolean',
            'date' => 'date',
            'bytea' => 'blob'
        );
        
        switch($direction)
        {
            case self::TO_YENTU: 
                $destinationType = $types[$type];
                break;
            
            case self::TO_POSTGRESQL: 
                $destinationType = array_search($type, $types);
                break;
        }
        
        if($destinationType == '')
        {
            throw new \Exception("Invalid data type {$type} requested"); 
        }
        else
        {
            return $destinationType;
        }
    }
    
    protected function getColumns($table)
    {
        $columns = array();
        $columnDetails = $this->driver->query(
            sprintf(
                "select column_name as name, data_type as type, is_nullable as nulls, column_default as default
                from information_schema.columns
                where table_name = '%s' and table_schema='%s'", 
                $table['name'], $table['schema']
            )
        );
        
        foreach($columnDetails as $i => $column)
        {
            $columns[$column['name']] = $column;
            $columns[$column['name']]['type'] = self::convertTypes($columnDetails[$i]['type'], self::TO_YENTU);
            $columns[$column['name']]['nulls'] = $columns[$column['name']]['nulls'] == 'YES' ? true : false;
        }
        
        return $columns;
    }
    
    /**
     * @note Query sourced from http://stackoverflow.com/questions/1152260/postgres-sql-to-list-table-foreign-keys
     * @param type $table
     */
    protected function getForeignConstraints($table)
    {
        $constraints = array();        
        $constraintColumns = $this->driver->query(
            sprintf("SELECT
                        kcu.constraint_name,
                        kcu.table_schema as schema,
                        kcu.table_name as table, 
                        kcu.column_name as column, 
                        ccu.table_name AS foreign_table,
                        ccu.table_schema AS foreign_schema,
                        ccu.column_name AS foreign_column,
                        rc.update_rule as on_update,
                        rc.delete_rule as on_delete
                    FROM 
                        information_schema.table_constraints AS tc 
                        JOIN information_schema.key_column_usage AS kcu
                          ON tc.constraint_name = kcu.constraint_name
                        JOIN information_schema.constraint_column_usage AS ccu
                          ON ccu.constraint_name = tc.constraint_name
                        JOIN information_schema.referential_constraints AS rc
                          ON rc.constraint_name = tc.constraint_name
                    WHERE constraint_type = 'FOREIGN KEY' 
                        AND tc.table_name='%s' AND tc.table_schema='%s'",
                $table['name'], $table['schema']
            )
        );  
                
        foreach($constraintColumns as $column)
        {
            $constraints[$column['constraint_name']]['columns'][] = $column['column'];
            $constraints[$column['constraint_name']]['foreign_columns'][] = $column['foreign_column'];
            $constraints[$column['constraint_name']]['table'] = $column['table'];
            $constraints[$column['constraint_name']]['schema'] = $this->fixSchema($column['schema']);
            $constraints[$column['constraint_name']]['foreign_table'] = $column['foreign_table'];
            $constraints[$column['constraint_name']]['foreign_schema'] = $this->fixSchema($column['foreign_schema']);
            $constraints[$column['constraint_name']]['on_update'] = $column['on_update'];
            $constraints[$column['constraint_name']]['on_delete'] = $column['on_delete'];
        }   
        
        return $constraints;
    }
    
    protected function getConstraint($table, $type)
    {
        $constraints = array();
        $constraintColumns = $this->driver->query(
            sprintf("select column_name, pk.constraint_name from 
                information_schema.table_constraints pk 
                join information_schema.key_column_usage c on 
                   c.table_name = pk.table_name and 
                   c.constraint_name = pk.constraint_name and
                   c.constraint_schema = pk.table_schema
                where pk.table_name = '%s' and pk.table_schema='%s'
                and constraint_type = '%s'",
                $table['name'], $table['schema'], $type
            )
        );
        
        foreach($constraintColumns as $column)
        {
            $constraints[$column['constraint_name']][] = $column['column_name'];
        }
        
        return $constraints;
    }
    
    private function fixSchema($schema)
    {
        if($schema == false || $schema == 'public')
        {
            return '';
        }
        else
        {
            return $schema;
        }
    }
    
    protected function getViews($schema)
    {
		$description = array();
		$views = $this->driver->query(
            "select table_schema as schema, table_name as name, view_definition as query
            from information_schema.views
            where table_schema = '$schema'");
            
		foreach($views as $view)
		{
			$description[$view['name']] = array(
				'name' => $view['name'],
				'schema' => $view['schema'],
				'query' => $view['query']
			);
		}
		return $description;
	}
    
    protected function getTables($schema)
    {
        $description = array();
        $tables = $this->driver->query(
            "select table_schema as schema, table_name as name 
            from information_schema.tables
            where table_schema = '$schema' and table_type = 'BASE TABLE'"
        );
        
        foreach($tables as $table)
        {
            $table['columns'] = $this->getColumns($table);
            $table['primary_key'] = $this->getConstraint($table, 'PRIMARY KEY');
            $table['unique_keys'] = $this->getConstraint($table, 'UNIQUE');
            $table['foreign_keys'] = $this->getForeignConstraints($table);
            $table['indices'] = $this->getIndices($table);
            $table['schema'] = $this->fixSchema($table['schema']);
            
            $primaryKey = reset($table['primary_key']);
            if(count($primaryKey) == 1 && substr_count($table['columns'][$primaryKey[0]]['default'], 'nextval'))
            {
                $table['auto_increment'] = true;
                unset($table['columns'][$primaryKey[0]]['default']);
            }
            
            $description[$table['name']] = $table;
        }        
        
        return $description;
    }

    public function describe() 
    {
        $description = array(
            'schemata' => array(),
        );
        
        $schemata = $this->driver->query(
            "select schema_name as name from information_schema.schemata 
            where schema_name not like 'pg_temp%' and 
            schema_name not like 'pg_toast%' and 
            schema_name not in ('pg_catalog', 'information_schema')"
        );
        
        foreach($schemata as $i => $schema)
        {
            if($schema['name'] == 'public')
            {
                $description['tables'] = $this->getTables('public');
                $description['views'] = $this->getViews('public');
            }
            else
            {
                $description['schemata'][$schema['name']]['name'] = $schema['name'];
                $description['schemata'][$schema['name']]['tables'] = $this->getTables($schema['name']);                
                $description['schemata'][$schema['name']]['views'] = $this->getViews($schema['name']);                
            }
        }
        
        return \yentu\SchemaDescription::wrap($description);
    }
}

