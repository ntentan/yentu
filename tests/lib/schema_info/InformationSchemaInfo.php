<?php

/* 
 * The MIT License
 *
 * Copyright 2015 ekow.
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

namespace yentu\tests\schema_info;

class InformationSchemaInfo extends \yentu\tests\SchemaInfo
{
    public function foreignKeyExists($table) 
    {
        if(!isset($table['schema']))
        {
            $table['schema'] = $GLOBALS['DEFAULT_SCHEMA'];
        }
        
        $response = $this->getPDO()->query(
            sprintf(
                "SELECT count(*) as c FROM information_schema.table_constraints WHERE table_name = '%s' and table_schema = '%s' and constraint_name = '%s' and constraint_type='FOREIGN KEY'",
                $table['table'], 
                $table['schema'],
                $table['name']
            )
        )->fetchAll(\PDO::FETCH_ASSOC);
        
        return $response[0]['c'] == 1;
    }

    public function columnExists($column) 
    {
        $response = $this->getPDO()->query(
            sprintf(
                "SELECT count(*) as c FROM information_schema.columns  where table_name = '%s' and table_schema = '%s' and column_name = '%s'",
                $this->table['table'], 
                $this->table['schema'],
                $column
            )
        )->fetchAll(\PDO::FETCH_ASSOC);
        return $response[0]['c'] == 1;
    }

    public function schemaExists($table) {
        
    }

    public function tableExists($table) 
    {
        if(is_string($table))
        {
            $table = array(
                'table' => $table,
                'schema' => $GLOBALS['DEFAULT_SCHEMA']
            );
        }
        
        $response = $this->getPDO()->query(
            sprintf(
                "SELECT count(*) as c FROM information_schema.tables  where table_name = '%s' and table_schema = '%s'",
                $table['table'], 
                $table['schema']
            )
        );      
        
        $count = $response->fetchAll(\PDO::FETCH_ASSOC);
        return $count[0]['c'] == 1;
    }

    public function columnNulable($column, $nullability) 
    {
        $response = $this->getPDO()->query(
            sprintf("SELECT count(*) as c FROM information_schema.columns  where table_name = '%s' and table_schema = '%s' and column_name = '%s' and is_nullable = '%s'",
                $this->table['table'], 
                $this->table['schema'],
                $column,
                $nullability ? 'YES' : 'NO'
            )
        )->fetchAll(\PDO::FETCH_ASSOC);
        return $response[0]['c'] == 1;
    }
}