<?php

namespace yentu;

class SchemaDescription implements \ArrayAccess
{

    private $description = array(
        'schemata' => array(),
        'tables' => array(),
        'views' => array()
    );

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
        return $this->description[$offset];
    }

    public function offsetSet($offset, $value)
    {
        
    }

    public function offsetUnset($offset)
    {
        
    }

    public function addSchema($name)
    {
        $this->description['schemata'][$name] = array(
            'name' => $name,
            'tables' => array()
        );
    }

    public function dropSchema($name)
    {
        unset($this->description['schemata'][$name]);
    }

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

    public function changeTableName($details)
    {
        // Use the from details to query for the existing table
        $query = $details['from'];
        $query['table'] = $details['from']['name'];
        $table = $this->getTable($query);
        
        // unset the existing table from the description array
        if ($details['schema'] == '') {
            unset($this->description["tables"][$details['from']['name']]);
        } else {
            unset($this->description['schemata'][$details['from']['schema']]["tables"][$details['from']['name']]);
        }
        
        // reassign the existing table to the description array using the to details
        $table['name'] = $details['to']['name'];
        $details['to']['table'] = $table['name'];
        $this->setTable($details['to'], $table);
    }

    public function addView($details)
    {
        $view = array(
            'name' => $details['name'],
            'definition' => $details['definition']
        );

        if ($details['schema'] != '') {
            $this->description['schemata'][$details['schema']]['views'][$details['name']] = $view;
        } else {
            $this->description['views'][$details['name']] = $view;
        }
    }

    public function dropView($details)
    {
        $this->dropTable($details, 'views');
    }

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

    public function dropTable($details, $type = 'tables')
    {
        if ($details['schema'] != '') {
            unset($this->description['schemata'][$details['schema']][$type][$details['name']]);
        } else {
            unset($this->description[$type][$details['name']]);
        }
    }

    public function getTable($details, $type = 'table')
    {
        if ($details['schema'] == '') {
            return $this->description["{$type}s"][$details[$type]];
        } else {
            return $this->description['schemata'][$details['schema']]["{$type}s"][$details[$type]];
        }
    }

    private function setTable($details, $table, $type = 'table')
    {
        if ($details['schema'] == '') {
            $this->description["{$type}s"][$details[$type]] = $table;
        } else {
            $this->description['schemata'][$details['schema']]["{$type}s"][$details[$type]] = $table;
        }
        $this->flattenAllColumns();
    }

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

    public function addColumn($details)
    {
        $table = $this->getTable($details);
        if ($details['type'] == '') {
            throw new DatabaseManipulatorException("Please specify a data type for the '{$details['name']}' column of the '{$table['name']}' table");
        }
        $table['columns'][$details['name']] = array(
            'name' => $details['name'],
            'type' => $details['type'],
            'nulls' => $details['nulls']
        );
        $this->setTable($details, $table);
    }

    public function dropColumn($details)
    {
        $table = $this->getTable($details);
        unset($table['columns'][$details['name']]);

        foreach ($table['foreign_keys'] as $i => $foreignKey) {
            if (array_search($details['name'], $foreignKey['columns']) !== false) {
                unset($table['foreign_keys'][$i]);
            }
        }

        $this->setTable($details, $table);
    }

    public function changeColumnNulls($details)
    {
        $table = $this->getTable($details['to']);
        $table['columns'][$details['to']['name']]['nulls'] = $details['to']['nulls'];
        $this->setTable($details['to'], $table);
    }

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

    public function changeColumnDefault($details)
    {
        $table = $this->getTable($details['to']);
        $table['columns'][$details['to']['name']]['default'] = $details['to']['default'];
        $this->setTable($details['to'], $table);
    }

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

    public function dropPrimaryKey($details)
    {
        $table = $this->getTable($details);
        unset($table['primary_key']);
        $this->setTable($details, $table);
    }

    public function addAutoPrimaryKey($details)
    {
        $table = $this->getTable($details);
        $table['auto_increment'] = true;
        $this->setTable($details, $table);
    }

    public function dropAutoPrimaryKey($details)
    {
        $table = $this->getTable($details);
        $table['auto_increment'] = false;
        $this->setTable($details, $table);
    }

    public function addUniqueKey($details)
    {
        $table = $this->getTable($details);
        $table['unique_keys'][$details['name']]['columns'] = $details['columns'];
        $this->setTable($details, $table);
    }

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

    public function toArray()
    {
        return $this->description;
    }

}
