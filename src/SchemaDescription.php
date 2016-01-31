<?php

/* 
 * The MIT License
 *
 * Copyright 2015 James Ekow Abaka Ainooson.
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

namespace yentu;

/**
 * Class which holds the description of the schema.
 * This class holds a parallel copy of the schema description used as a basis
 * for determining changes during migrations.
 */
class SchemaDescription implements \ArrayAccess
{

    /**
     * Schema description that this class wraps.
     * @var array
     */
    private $description = array(
        'schemata' => array(),
        'tables' => array(),
        'views' => array()
    );

    /**
     * Create a new instance of the schema description.
     * 
     * @param array $description
     * @param DatabaseManipulator $manipulator
     */
    private function __construct($description, $manipulator)
    {
        $this->description = $description;
        $this->description['tables'] = $this->convertColumnTypes(
                $this->description['tables'], $manipulator
        );
        foreach ($this->description['schemata'] as $name => $schema) {
            if (is_array($schema['tables'])) {
                $this->description['schemata'][$name]['tables'] = $this->convertColumnTypes(
                        $schema['tables'], $manipulator
                );
            }
        }
        $this->flattenAllColumns();
    }

    /**
     * Convert the column types between generic yentu types and native database
     * types on the tables 
     * 
     * @param array $tables
     * @param DatabaseManipulator $manipulator
     * @return array
     */
    private function convertColumnTypes($tables, $manipulator)
    {
        foreach ($tables as $i => $table) {
            foreach ($table['columns'] as $j => $column) {
                $tables[$i]['columns'][$j]['type'] = $manipulator->convertTypes(
                        $column['type'], DatabaseManipulator::CONVERT_TO_YENTU, $column['length']
                );
            }
        }
        return $tables;
    }
    
    public function offsetExists($offset)
    {
        return isset($this->description[$offset]);
    }

    public function offsetGet($offset)
    {
        $return = $this->description[$offset];
        if(is_array($return)) {
            $return = Parameters::wrap($this->description[$offset]);
            $this->description[$offset] = $return;
        }
        return $return;
    }

    public function offsetSet($offset, $value)
    {
        
    }

    public function offsetUnset($offset)
    {
        unset($this->description[$offset]);
    }

    /**
     * Add a schema to the schema description.
     * @param  $name
     */
    public function addSchema($name)
    {
        $this->description['schemata'][$name] = array(
            'name' => $name,
            'tables' => array()
        );
    }

    /**
     * Drop the schema from the schema description.
     * @param string $name
     */
    public function dropSchema($name)
    {
        unset($this->description['schemata'][$name]);
    }

    /**
     * Add a table to the schema description. 
     * The table array contains the `columns`, `primary_key`, `unique_keys`,
     * `foreign_keys` and `indices`.
     * @param array $details
     */
    public function addTable($details)
    {
        $table = array(
            'name' => $details['name'],
            'schema' => $details['schema'],
            'columns' => array(),
            'primary_key' => array(),
            'unique_keys' => array(),
            'foreign_keys' => array(),
            'indices' => array()
        );
        $this->setTable(array('schema' => $details['schema'], 'table' => $details['name']), $table);
    }

    /**
     * Change the name of a table. 
     * The details contains two separate 
     * @param array $details
     */
    public function changeTableName($details)
    {
        // Use the from details to query for the existing table
        $query = $details['from'];
        $query['table'] = $details['from']['name'];
        $table = $this->getTable($query);
        
        // unset the existing table from the description array
        if ($details['from']['schema'] == '') {
            unset($this->description["tables"][$details['from']['name']]);
        } else {
            unset($this->description['schemata'][$details['from']['schema']]["tables"][$details['from']['name']]);
        }
        
        // reassign the existing table to the description array using the to details
        $table['name'] = $details['to']['name'];
        $details['to']['table'] = $table['name'];
        $this->setTable($details['to'], $table);
    }

    /**
     * Add a view to the schema description.
     * 
     * @param array $details
     */
    public function addView($details)
    {
        $view = array(
            'name' => $details['name'],
            'definition' => $details['definition']
        );

        if ($details['schema'] != '') {
            $schemata = $this->description['schemata'];
            $schemata[$details['schema']]['views'][$details['name']] = $view;
            $this->description['schemata'] = $schemata;
        } else {
            $this->description['views'][$details['name']] = $view;
        }
    }

    /**
     * Drop a view from the description.
     * 
     * @param array $details
     */
    public function dropView($details)
    {
        $this->dropTable($details, 'views');
    }

    /**
     * Change the definition of a view in the schema description.
     * 
     * @param type $details
     */
    public function changeViewDefinition($details)
    {
        $viewDetails = array(
            'view' => $details['from']['name'],
            'schema' => $details['from']['schema']
        );
        $view = $this->getTable($viewDetails, 'view');
        $view['definition'] = $details['to']['definition'];
        $this->setTable($viewDetails, $view, 'view');
    }

    /**
     * Drop a table from the schema description.
     * 
     * @param array $details
     * @param string $type
     */
    public function dropTable($details, $type = 'tables')
    {
        if ($details['schema'] != '') {
            unset($this->description['schemata'][$details['schema']][$type][$details['name']]);
        } else {
            unset($this->description[$type][$details['name']]);
        }
    }

    /**
     * Get a table from the schema description.
     * 
     * @param array $details
     * @param string $type
     * @return array
     */
    public function getTable($details, $type = 'table')
    {
        if ($details['schema'] == '') {
            return $this->description["{$type}s"][$details[$type]];
        } else {
            return $this->description['schemata'][$details['schema']]["{$type}s"][$details[$type]];
        }
    }

    /**
     * Replace a table on the schema description with a new one.
     * 
     * @param array $details
     * @param string $table
     * @param type $type
     */
    private function setTable($details, $table, $type = 'table')
    {
        if ($details['schema'] == '') {
            $this->description["{$type}s"][$details[$type]] = $table;
        } else {
            $this->description['schemata'][$details['schema']]["{$type}s"][$details[$type]] = $table;
        }
        $this->flattenAllColumns();
    }

    /**
     * Flatten all table constraints to make it easy to address them through
     * a single linear array.
     */
    private function flattenAllColumns()
    {
        foreach ($this->description['schemata'] as $schemaName => $schema) {
            foreach ($schema['tables'] as $tableName => $table) {
                $this->description['schemata'][$schemaName]['tables'][$tableName]['flat_foreign_keys'] = $this->flattenColumns($table['foreign_keys'], 'columns');
                $this->description['schemata'][$schemaName]['tables'][$tableName]['flat_unique_keys'] = $this->flattenColumns($table['unique_keys'], 'columns');
                $this->description['schemata'][$schemaName]['tables'][$tableName]['flat_indices'] = $this->flattenColumns($table['indices'], 'columns');
            }
        }
    }

    /**
     * Flatten column keys so they can be accessed linearly in an array.
     * 
     * @param type $items
     * @param type $key
     * @return type
     */
    private function flattenColumns($items, $key = false)
    {
        $flattened = array();
        if (is_array($items)) {
            foreach ($items as $name => $item) {
                foreach ($key === false ? $item : $item[$key] as $column) {
                    $flattened[$column] = $name;
                }
            }
        }
        return $flattened;
    }

    /**
     * Add a column to a table.
     * 
     * @param array $details
     * @throws DatabaseManipulatorException
     */
    public function addColumn($details)
    {
        $table = $this->getTable($details);
        if ($details['type'] == '') {
            throw new exceptions\DatabaseManipulatorException("Please specify a data type for the '{$details['name']}' column of the '{$table['name']}' table");
        }
        $table['columns'][$details['name']] = array(
            'name' => $details['name'],
            'type' => $details['type'],
            'nulls' => $details['nulls']
        );
        $this->setTable($details, $table);
    }

    /**
     * Drop a column from a table in the schema description.
     * 
     * @param array $details
     */
    public function dropColumn($details)
    {
        $table = $this->getTable($details);
        unset($table['columns'][$details['name']]);

        foreach ($table['foreign_keys'] as $i => $foreignKey) {
            if (array_search($details['name'], $foreignKey['columns']) !== false) {
                unset($table['foreign_keys'][$i]);
            }
        }

        foreach ($table['unique_keys'] as $i => $uniqueKey) {
            if (array_search($details['name'], $uniqueKey['columns']) !== false) {
                unset($table['unique_keys'][$i]);
            }
        }

        $this->setTable($details, $table);
    }

    /**
     * Change the null state of a column.
     * 
     * @param array $details
     */
    public function changeColumnNulls($details)
    {
        $table = $this->getTable($details['to']);
        $table['columns'][$details['to']['name']]['nulls'] = $details['to']['nulls'];
        $this->setTable($details['to'], $table);
    }

    /**
     * Change the name of a column.
     * 
     * @param array $details
     */
    public function changeColumnName($details)
    {
        $table = $this->getTable($details['to']);
        $column = $table['columns'][$details['from']['name']];
        $column['name'] = $details['to']['name'];
        unset($table['columns'][$details['from']['name']]);
        $table['columns'][$details['to']['name']] = $column;

        // Rename all indices
        foreach ($table['unique_keys'] as $name => $key) {
            $position = array_search($details['from']['name'], $key['columns']);
            if ($position !== false) {
                $table['unique_keys'][$name]['columns'][$position] = $column['name'];
            }
        }

        // Rename all unique keys
        // Rename all foreign keys
        // rename all primary keys

        $this->setTable($details['to'], $table);
    }

    /**
     * Change the default value of the column.
     * 
     * @param array $details
     */
    public function changeColumnDefault($details)
    {
        $table = $this->getTable($details['to']);
        $table['columns'][$details['to']['name']]['default'] = $details['to']['default'];
        $this->setTable($details['to'], $table);
    }

    /**
     * Add a primary key to a table in the schema description.
     * 
     * @param array $details
     */
    public function addPrimaryKey($details)
    {
        $table = $this->getTable($details);
        $table['primary_key'] = array(
            $details['name'] => array(
                'columns' => $details['columns']
            )
        );
        $this->setTable($details, $table);
    }

    /**
     * Drop a primarykey from a table in the schema description.
     * 
     * @param array $details
     */
    public function dropPrimaryKey($details)
    {
        $table = $this->getTable($details);
        unset($table['primary_key']);
        $this->setTable($details, $table);
    }

    /**
     * Make the primary key auto increment.
     * 
     * @param array $details
     */
    public function addAutoPrimaryKey($details)
    {
        $table = $this->getTable($details);
        $table['auto_increment'] = true;
        $this->setTable($details, $table);
    }

    /**
     * Prevent the primary key from auto incrementing.
     * 
     * @param array $details
     */
    public function dropAutoPrimaryKey($details)
    {
        $table = $this->getTable($details);
        $table['auto_increment'] = false;
        $this->setTable($details, $table);
    }

    /**
     * Add a unique constraint to a set of columns of a given table in the
     * schema description.
     * 
     * @param array $details
     */
    public function addUniqueKey($details)
    {
        $table = $this->getTable($details);
        $table['unique_keys'][$details['name']]['columns'] = $details['columns'];
        $this->setTable($details, $table);
    }

    /**
     * Drop a unique key from a table in the schema description.
     * 
     * @param type $details
     */
    public function dropUniqueKey($details)
    {
        $table = $this->getTable($details);
        if (isset($table['unique_keys'][$details['name']])) {
            unset($table['unique_keys'][$details['name']]);
        } else {
            // Deal with special edge cases as in SQLite where unique keys are not named
            foreach ($table['unique_keys'] as $i => $key) {
                if ($key['columns'] == $details['columns']) {
                    unset($table['unique_keys'][$i]);
                }
            }
        }
        $this->setTable($details, $table);
    }

    /**
     * Add an index to the table.
     * 
     * @param array $details
     */
    public function addIndex($details)
    {
        $table = $this->getTable($details);
        $table['indices'][$details['name']]['columns'] = $details['columns'];
        $this->setTable($details, $table);
    }

    public function dropIndex($details)
    {
        $table = $this->getTable($details);
        unset($table['indices'][$details['name']]);
        $this->setTable($details, $table);
    }

    public function addForeignKey($details)
    {
        $table = $this->getTable($details);
        $table['foreign_keys'][$details['name']] = $details;
        $this->setTable($details, $table);
    }

    public function dropForeignKey($details)
    {
        $table = $this->getTable($details);
        unset($table['foreign_keys'][$details['name']]);
        $this->setTable($details, $table);
    }

    public function changeForeignKeyOnDelete($details)
    {
        $table = $this->getTable($details['from']);
        $table['foreign_keys'][$details['from']['name']]['on_delete'] = $details['to']['on_delete'];
        $this->setTable($details['from'], $table);
    }

    public function changeForeignKeyOnUpdate($details)
    {
        $table = $this->getTable($details['from']);
        $table['foreign_keys'][$details['from']['name']]['on_update'] = $details['to']['on_update'];
        $this->setTable($details['from'], $table);
    }

    public function executeQuery()
    {
        
    }

    public function reverseQuery()
    {
        
    }

    public static function wrap($description, $manipulator)
    {
        return new SchemaDescription($description, $manipulator);
    }

    public function getArray()
    {
        return $this->description;
    }

}
