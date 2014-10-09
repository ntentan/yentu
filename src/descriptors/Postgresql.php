<?php
namespace yentu\descriptors;

use yentu\SchemaDescriptor;

class Postgresql extends SchemaDescriptor
{
    /**
     * 
     * @note Query sourced from http://stackoverflow.com/questions/2204058/show-which-columns-an-index-is-on-in-postgresql
     * @param type $table
     * @return type
     */
    protected function getIndices(&$table)
    {
        return $this->driver->query(
            sprintf("select
                        t.relname as table_name,
                        i.relname as name,
                        a.attname as column
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
    }
    
    public static function convertTypes($type, $direction, $length = null)
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
            case self::CONVERT_TO_YENTU: 
                $destinationType = $types[$type];
                break;
            
            case self::CONVERT_TO_DRIVER:
                $destinationType = array_search($type, $types);
                break;
        }
        
        if($destinationType == '')
        {
            throw new \yentu\DatabaseDriverException("Invalid data type {$type} requested"); 
        }
        else
        {
            $destinationType .= $length === null ? '' : "($length)";
            return $destinationType;
        }
    }
    
    protected function getColumns(&$table)
    {
        return $this->driver->query(
            sprintf(
                "select column_name as name, data_type as type, is_nullable as nulls, column_default as default, character_maximum_length as length
                from information_schema.columns
                where table_name = '%s' and table_schema='%s'", 
                $table['name'], $table['schema']
            )
        );
    }
    
    /**
     * @note Query sourced from http://stackoverflow.com/questions/1152260/postgres-sql-to-list-table-foreign-keys
     * @param type $table
     */
    protected function getForeignKeys(&$table)
    {
        return $this->driver->query(
            sprintf("SELECT
                        kcu.constraint_name as name,
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
                          ON tc.constraint_name = kcu.constraint_name and tc.table_schema = kcu.table_schema
                        JOIN information_schema.constraint_column_usage AS ccu
                          ON ccu.constraint_name = tc.constraint_name and tc.table_schema = ccu.table_schema
                        JOIN information_schema.referential_constraints AS rc
                          ON rc.constraint_name = tc.constraint_name and rc.constraint_schema = tc.table_schema
                    WHERE constraint_type = 'FOREIGN KEY' 
                        AND tc.table_name='%s' AND tc.table_schema='%s'",
                $table['name'], $table['schema']
            )
        ); 
    }
    
    protected function getPrimaryKey(&$table)
    {
        return $this->getConstraint($table, 'PRIMARY KEY');
    }
    
    protected function getUniqueKeys(&$table)
    {
        return $this->getConstraint($table, 'UNIQUE');
    }

    private function getConstraint($table, $type)
    {
        return $this->driver->query(
            sprintf("select column_name as column, pk.constraint_name as name from 
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
    }
    
    protected function getViews(&$schema)
    {
        return $this->driver->query(
            "select table_schema as schema, table_name as name, view_definition as definition
            from information_schema.views
            where table_schema = '$schema'"
        );
    }
    
    protected function getTables($schema)
    {
        return $tables = $this->driver->query(
            "select table_schema as schema, table_name as name 
            from information_schema.tables
            where table_schema = '$schema' and table_type = 'BASE TABLE'"
        );        
    }
    
    public function getSchemata()
    {
        return $this->driver->query(
            "select schema_name as name from information_schema.schemata 
            where schema_name not like 'pg_temp%' and 
            schema_name not like 'pg_toast%' and 
            schema_name not in ('pg_catalog', 'information_schema')"
        );
    }

    protected function hasAutoIncrementingKey(&$table)
    {
        $auto = false;
        $primaryKey = reset($table['primary_key']);
        if(count($primaryKey) == 1 && substr_count($table['columns'][$primaryKey['columns'][0]]['default'], 'nextval'))
        {
            unset($table['columns'][$primaryKey['columns'][0]]['default']);
            $auto = true;
        }
        return $auto;
    }
}

