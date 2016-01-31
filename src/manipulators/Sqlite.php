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

use yentu\Parameters;

/**
 * SQLite Database Structure Manipulator for the yentu migration engine.
 *
 * @author Ekow Abaka Ainooson
 */
class Sqlite extends \yentu\DatabaseManipulator
{
    private $placeholders = [];
    
    protected function _addAutoPrimaryKey($details) 
    {
        $this->rebuildTableFromDefinition($details['table']);
    }
    
    private function renameColumns(&$columns, $options)
    {
        if(isset($options['renamed_column'])) {
            foreach($columns as $i => $column) {
                if($options['renamed_column']['from']['name'] === $column) {
                    $columns[$i] = $options['renamed_column']['to']['name'];
                }
            }
        }
    }
    
    /**
     * Generate a query stub to represent the constraints section of a full
     * query (usually a CREATE TABLE or ADD COlUMN query).
     * 
     * @param array<string> $columns An array of the names of columns in the constraint.
     * @param string $type The type of constraint `FOREIGN KEY`, `UNIQUE` etc.
     * @param string $name The name of the constraint.
     * @return string
     */
    private function getConstraintQuery($columns, $type, $name, $options)
    {
        $this->renameColumns($columns, $options);
        return ", CONSTRAINT `$name` $type (`" . implode('`, `', $columns) . "`)";
    }
    
    private function createIndices($indices, $table)
    {
        foreach($indices as $name => $index)
        {
            $this->_addIndex(['name' => $name, 'table' => $table, 'columns' => $index['columns']]);
        }
    }
    
    /**
     * Generate all the constraint queries of a table.
     * This function is used when executing UNIQUE or PRIMARY KEY constraints.
     * 
     * @param array<array> $constraints An array of details of all constraints.
     * @param string $type The type of constraint 'FOREIGN KEY' ... etc.
     * @return string
     */
    private function generateConstraintsQueries($constraints, $type, $options)
    {
        $query = '';
        foreach($constraints as $name => $constraint)
        {
            $query .= $this->getConstraintQuery($constraint['columns'], $type, $name, $options);
        }
        return $query;
    }
    
    /**
     * Generate the query for a foreign key constraint.
     * 
     * @param array<array> $constraintDetails
     * @return string
     */
    private function getFKConstraintQuery($constraints, $options)
    {
        $query = '';
        foreach($constraints as $name => $constraint)
        {
            $this->renameColumns($constraint['foreign_columns'], $options);
            $query .= $this->getConstraintQuery($constraint['columns'], 'FOREIGN KEY', $name, $options) . 
                sprintf(
                    " REFERENCES `{$constraint['foreign_table']}` (`" . implode('`, `', $constraint['foreign_columns']) . "`) %s %s",
                    isset($constraint['on_delete']) ? "ON DELETE {$constraint['on_delete']}" : '',
                    isset($constraint['on_update']) ? "ON UPDATE {$constraint['on_update']}" : ''
                );
        }
        return $query;
    }
    
    /**
     * Generate an SQL query field list to be used in a query for moving data
     * between a table and its altered counterpart.
     * This function is called when reconstructing SQLite tables.
     * New columns would be ignored as their default value would be appended.
     * Renamed columns would return the old name of the column with the new 
     * name as an alias. Fields which are not altered in any way are returned
     * just as they are.
     * 
     * @param array $column An array containing information about the column
     * @param array $options Contains information about the current operation.
     *     Through this variable we can know whether a new column has been
     *     added or an existing column has been renamed.
     * @param string $comma The comma state. Helps in comma placements for 
     *     correct query generation.
     * @return string
     */
    private function getFieldListColumn($column, $options, $comma)
    {
        $return = false;
        if(isset($options['new_column'])) {
            if($column['name'] == $options['new_column']['name']) {
                $return = '';
            }
        } else if(isset($options['renamed_column'])) {
            if($column['name'] == $options['renamed_column']['to']['name']) {
                $return = $comma . "`{$options['renamed_column']['from']['name']}` as `{$options['renamed_column']['to']['name']}`";
            }
        }
        
        if($return === false) {
            $return = $comma . "`{$column['name']}`";
        }
        return $return;
    }
    
    /**
     * Rebuids an entire table based on the current state of yentu's schema
     * description.
     * In order to work around SQLite's lack of full table altering routines,
     * this function does the work of creating new tables based on altered 
     * versions of old tables and moves data between the old and new tables.
     * It takes advantage of the fact that yentu maintains an internal schema
     * description of all operations performed.
     * 
     * @param string $tableName The name of the table to rebuild.
     * @param array $options An array which contains extra details about the 
     *     operation which led to the rebuilding of the table. Currently the
     *     only options that are of interest are those that are passed when
     *     adding new columns and those that are passed when modifying existing
     *     columns.
     */
    private function rebuildTableFromDefinition($tableName, $options = [])
    {
        $this->query("PRAGMA foreign_keys=OFF");
        $table = Parameters::wrap(
            $this->getDescription()->getTable(['table' => $tableName, 'schema' => false]),
            ['auto_increment']
        );
        $dummyTable = "__yentu_{$table['name']}";
        $query = "CREATE TABLE `$dummyTable` (";
        $fieldList = '';
        $comma = '';
        $primaryKeyAdded = false;
        $primaryKeyColumn = '';
        
        if($table['auto_increment'] && isset($table['primary_key'][0]))
        {
            $key = $table['primary_key'][0];
            $primaryKeyColumn = $key['columns'][0];
        }
        if(count($table['columns']))
        {
            foreach($table['columns'] as $column)
            {
                $query .= $comma . $this->getColumnDef($column);
                $fieldList .= $this->getFieldListColumn($column, $options, $comma);
                if($column['name'] === $primaryKeyColumn)
                {
                    $query .= ' PRIMARY KEY AUTOINCREMENT';
                    $primaryKeyAdded = true;
                }
                $comma = ', ';
            }
        }
        else
        {
            // put back the placeholder column so the table can stand
            $query .= '`__yentu_placeholder_col` INTEGER';
        }
        
        if(!$primaryKeyAdded && isset($table['primary_key']))
        {
            $query .= $this->generateConstraintsQueries($table['primary_key'], 'PRIMARY KEY', $options);
        }
        $query .= $this->generateConstraintsQueries($table['unique_keys'], 'UNIQUE', $options);
        $query .= $this->getFKConstraintQuery($table['foreign_keys'], $options);
        
        $query .= ')';
        
        $this->query($query);
        
        if(isset($options['new_column']))
        {
            $this->query("INSERT INTO `$dummyTable` SELECT {$fieldList} , ? FROM `{$table['name']}`", $options['new_column']['default']);
        }
        else if(count($table['columns']) > 0)
        {
            $this->query("INSERT INTO `$dummyTable` SELECT {$fieldList} FROM `{$table['name']}`");
        }
                
        $this->query("DROP TABLE `{$table['name']}`");
        $this->query("ALTER TABLE `$dummyTable` RENAME TO `{$table['name']}`");
        $this->createIndices($table['indices'], $table['name']);
        $this->query("PRAGMA foreign_keys=ON");
    }
    
    /**
     * Generate an SQL query stub which represent a column definition.
     * 
     * @param array $details
     * @return string
     */
    private function getColumnDef($details)
    {
        $details = Parameters::wrap($details, ['length', 'default']);
        return trim(sprintf(
            "`%s` %s %s %s", 
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
        if(isset($this->placeholders[$details['table']]))
        {
            $this->query("DROP TABLE `{$details['table']}`");
            $this->query(sprintf("CREATE TABLE `%s` (%s)",
                    $details['table'],
                    $this->getColumnDef($details)
                )
            );
            unset($this->placeholders[$details['table']]);
        }
        else if($details['nulls'] === null || $details['nulls'] == true || ($details['nulls'] === false && $details['default'] !== null))
        {
            $this->query("ALTER TABLE `{$details['table']}` ADD COLUMN " . $this->getColumnDef($details));
        }
        else
        {
            $this->rebuildTableFromDefinition($details['table'], ['new_column' => $details]);
        }
    }

    protected function _addForeignKey($details) 
    {
        $this->rebuildTableFromDefinition($details['table']);
    }

    protected function _addIndex($details) {
        $this->query("CREATE INDEX `{$details['name']}` ON `{$details['table']}` (`" . implode("`, `", $details['columns']) ."`)");
    }

    protected function _addPrimaryKey($details) 
    {
        $this->rebuildTableFromDefinition($details['table']);
    }

    protected function _addSchema($name) {
        
        
    }

    protected function _addTable($details) 
    {
        $this->query("CREATE TABLE `{$details['name']}` (`__yentu_placeholder_col` INTEGER)");
        $this->placeholders[$details['name']] = true;
    }

    protected function _addUniqueKey($details) 
    {
        $this->rebuildTableFromDefinition($details['table']);
    }

    protected function _addView($details) 
    {
        $this->query("CREATE VIEW `{$details['name']}` AS {$details['definition']}");
    }

    protected function _changeColumnDefault($details) 
    {
        $this->rebuildTableFromDefinition($details['to']['table']);    
    }

    protected function _changeColumnName($details) 
    {
        $this->rebuildTableFromDefinition($details['to']['table'], ['renamed_column' => $details]);    
    }

    protected function _changeColumnNulls($details) 
    {
        $this->rebuildTableFromDefinition($details['to']['table']);    
    }

    protected function _changeViewDefinition($details) 
    {
        $this->query("DROP VIEW `{$details['to']['name']}`");
        $this->_addView($details['to']);
    }

    protected function _dropAutoPrimaryKey($details) 
    {
        $this->rebuildTableFromDefinition($details['table']);    
    }

    protected function _dropColumn($details) {
        $this->rebuildTableFromDefinition($details['table']);
        
    }

    protected function _dropForeignKey($details) 
    {    
        $this->rebuildTableFromDefinition($details['table']);
    }

    protected function _dropIndex($details) 
    {
        $this->query("DROP INDEX `{$details['name']}`");
    }

    protected function _dropPrimaryKey($details) 
    {
        $this->rebuildTableFromDefinition($details['table']);    
    }

    protected function _dropSchema($name) {
        
        
    }

    protected function _dropTable($details) {
        $this->query("DROP TABLE `{$details['name']}`");
    }

    protected function _dropUniqueKey($details) {
        $this->rebuildTableFromDefinition($details['table']);
    }

    protected function _dropView($details) 
    {
        $this->query("DROP VIEW `{$details['name']}`");
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
            throw new \yentu\exceptions\DatabaseManipulatorException("Invalid data type {$type} requested"); 
        }
        else if($destinationType == 'text')
        {
            $destinationType .= $length === null ? '' : "($length)";
        }
        return $destinationType;              
    }

    protected function _changeTableName($details) {
        $this->query(
            sprintf(
                "ALTER TABLE `%s` RENAME TO `%s`",  
                $details['from']['name'], $details['to']['name']
            )
        );        
    }

}

