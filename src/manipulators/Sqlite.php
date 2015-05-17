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

namespace yentu\manipulators;

/**
 * Description of Sqlite
 *
 * @author ekow
 */
class Sqlite extends \yentu\DatabaseManipulator
{
    private $placeholders = [];
    
    protected function _addAutoPrimaryKey($details) 
    {
        $this->rebuildTableFromDefinition($details['table']);
    }
    
    private function getConstraintQuery($constraints, $type, $autoIncrement = false)
    {
        $query = '';
        foreach($constraints as $name => $constraint)
        {
            $query = ", CONSTRAINT $name $type (" . implode($constraint['columns']) . ")";
        }
        return $query;
    }
    
    private function rebuildTableFromDefinition($tableName, $options = null)
    {
        $description = $this->getDescription();
        $table = $description['tables'][$tableName];
        $dummyTable = "__yentu_{$table['name']}";
        $query = "CREATE TABLE $dummyTable (";
        $comma = '';
        $primaryKeyAdded = false;
        
        if($table['auto_increment'])
        {
            $key = reset($table['primary_key']);
            $primaryKeyColumn = $key['columns'][0];
        }
        
        foreach($table['columns'] as $column)
        {
            $query .= $comma . $this->getColumnDef($column);
            if($column['name'] === $primaryKeyColumn)
            {
                $query .= ' PRIMARY KEY AUTOINCREMENT';
                $primaryKeyAdded = true;
            }
            $comma = ', ';
        }
        
        if(!$primaryKeyAdded)
        {
            $query .= $this->getConstraintQuery($table['primary_key'], 'PRIMARY KEY', $table['auto_increment']);
        }
        $query .= $this->getConstraintQuery($table['unique_keys'], 'UNIQUE');
        $query .= $this->getConstraintQuery($table['indices'], 'INDEX');
        
        $query .= ')';
        
        $this->query($query);
        
        if(isset($options['new_column']))
        {
            $this->query("INSERT INTO $dummyTable SELECT *, ? FROM {$table['name']}", $options['new_column']['default']);
        }
        else
        {
            $this->query("INSERT INTO $dummyTable SELECT * FROM {$table['name']}");
        }
        $this->query("DROP TABLE {$table['name']}");
        $this->query("ALTER TABLE $dummyTable RENAME TO {$table['name']}");
    }
    
    private function getColumnDef($details)
    {
        return trim(sprintf(
            "%s %s %s %s", 
            $details['name'], 
            $this->convertTypes(
                $details['type'], 
                self::CONVERT_TO_DRIVER, 
                $details['length']
            ),
            $details['nulls'] === false ? 'NOT NULL' : '',
            $details['default'] === null ? null : "DEFAULT {$details['default']}"
        ));
    }

    protected function _addColumn($details) 
    {
        if($this->placeholders[$details['table']])
        {
            $this->query("DROP TABLE {$details['table']}");
            $this->query(sprintf("CREATE TABLE %s (%s)",
                    $details['table'],
                    $this->getColumnDef($details)
                )
            );
            unset($this->placeholders[$details['table']]);
        }
        else if($details['nulls'] === null || $details['nulls'] == true || ($details['nulls'] === false && $details['default'] !== null))
        {
            $this->query("ALTER TABLE {$details['table']} ADD COLUMN " . $this->getColumnDef($details));
        }
        else
        {
            $this->rebuildTableFromDefinition($details['table'], ['new_column' => $details]);
        }
    }

    protected function _addForeignKey($details) {
        throw new \Exception("Implement");
        
    }

    protected function _addIndex($details) {
        $this->rebuildTableFromDefinition($details['table']);
        
    }

    protected function _addPrimaryKey($details) 
    {
        $this->rebuildTableFromDefinition($details['table']);
    }

    protected function _addSchema($name) {
        throw new \Exception("Implement");
        
    }

    protected function _addTable($details) 
    {
        $this->query("CREATE TABLE {$details['name']} (__yentu_placeholder_col INTEGER)");
        $this->placeholders[$details['name']] = true;
    }

    protected function _addUniqueKey($details) 
    {
        $this->rebuildTableFromDefinition($details['table']);
    }

    protected function _addView($details) {
        throw new \Exception("Implement");
        
    }

    protected function _changeColumnDefault($details) {
        throw new \Exception("Implement");
        
    }

    protected function _changeColumnName($details) {
        throw new \Exception("Implement");
        
    }

    protected function _changeColumnNulls($details) {
        throw new \Exception("Implement");
        
    }

    protected function _changeViewDefinition($details) {
        throw new \Exception("Implement");
        
    }

    protected function _dropAutoPrimaryKey($details) {
        throw new \Exception("Implement");
        
    }

    protected function _dropColumn($details) {
        throw new \Exception("Implement");
        
    }

    protected function _dropForeignKey($details) {
        throw new \Exception("Implement");
        
    }

    protected function _dropIndex($details) {
        throw new \Exception("Implement");
        
    }

    protected function _dropPrimaryKey($details) {
        throw new \Exception("Implement");
        
    }

    protected function _dropSchema($name) {
        throw new \Exception("Implement");
        
    }

    protected function _dropTable($details) {
        throw new \Exception("Implement");
        
    }

    protected function _dropUniqueKey($details) {
        throw new \Exception("Implement");
        
    }

    protected function _dropView($details) {
        throw new \Exception("Implement");
        
    }

    public function convertTypes($type, $direction, $length) 
    {
        $destinationType = null;
        $types = [
            ['integer', 'integer'],
            ['text', 'string'],
            ['real', 'double'],
            ['text', 'timestamp'],
            ['text', 'text'],
            ['blob', 'blob'],
            ['integer', 'boolean'],
            ['integer', 'bigint'],
            ['text', 'date']
        ];
        
        $type = strtolower($type);
        
        foreach($types as $testType)
        {
            if($direction === self::CONVERT_TO_DRIVER && $testType[1] === $type)
            {
                $destinationType = $testType[0];
                break;
            }
            else if($direction === self::CONVERT_TO_YENTU && $testType[0] === $type)
            {
                $destinationType = $testType[1];
                break;
            }
        }
        
        if($destinationType == '')
        {
            throw new \yentu\DatabaseManipulatorException("Invalid data type {$type} requested"); 
        }
        else if($destinationType == 'text')
        {
            $destinationType .= $length === null ? '' : "($length)";
        }
        return $destinationType;              
    }
}
