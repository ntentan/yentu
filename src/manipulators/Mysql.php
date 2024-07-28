<?php
namespace yentu\manipulators;

use yentu\Parameters;

class Mysql extends AbstractDatabaseManipulator
{

    private $autoIncrementPending;
    private $placeholders = array();

    #[\Override]
    public function convertTypes($type, $direction, $length = null)
    {
        $types = array(
            'integer' => 'integer',
            'int' => 'integer',
            'decimal' => 'integer',
            'bigint' => 'bigint',
            'varchar' => 'string',
            'char' => 'string',
            'double' => 'double',
            'datetime' => 'timestamp',
            'timestamp' => 'timestamp',
            'text' => 'text',
            'tinytext' => 'text',
            'mediumtext' => 'text',
            'boolean' => 'boolean',
            'tinyint' => 'boolean',
            'date' => 'date',
            'blob' => 'blob'
        );

        switch ($direction) {
            case self::CONVERT_TO_YENTU:
                $destinationType = $types[strtolower($type)];
                break;

            case self::CONVERT_TO_DRIVER:
                $destinationType = array_search(strtolower($type), $types);
                break;
        }

        if ($destinationType == '') {
            throw new \yentu\exceptions\DatabaseManipulatorException("Invalid data type {$type} requested");
        } else if ($destinationType == 'varchar') {
            $destinationType .= $length === null ? '' : "($length)";
        }
        return $destinationType;
    }

    /**
     * Returns an identifier quoted table name.
     * In cases where a schema is not available, just the table is returned.
     * However, in cases where both a schema and a table are available a dot
     * separated version of the name is returned.
     * 
     * @param string $name
     * @param string $schema
     * @return string
     */
    private function buildTableName($name, $schema)
    {
        return ($schema === false || $schema == '' ? '' : "`{$schema}`.") . "`$name`";
    }

    #[\Override]
    protected function _addAutoPrimaryKey($details)
    {
        $table = $this->getDescription()->getTable($details);
        $column = ($table['columns'][$details['column']]);

        if (count($table['primary_key']) > 0) {
            $this->query(
                sprintf('ALTER TABLE %s MODIFY `%s` %s %s AUTO_INCREMENT', $this->buildTableName($details['table'], $details['schema']), $details['column'], $this->convertTypes(
                        $column['type'], self::CONVERT_TO_DRIVER, isset($column['length']) ? $column['length'] : 255
                    ), $column['nulls'] === false ? 'NOT NULL' : ''
                )
            );
        } else {
            $this->autoIncrementPending = $details;
        }
    }

    #[\Override]
    protected function _addColumn($details)
    {
        $tableName = $this->buildTableName($details['table'], $details['schema']);

        $this->query(
            sprintf('ALTER TABLE %s ADD COLUMN `%s` %s %s', $tableName, $details['name'], $this->convertTypes(
                    $details['type'], self::CONVERT_TO_DRIVER, $details['length'] == '' ? 255 : $details['length']
                ), $details['nulls'] === false ? 'NOT NULL' : ''
            )
        );

        if (isset($this->placeholders[$tableName])) {
            $this->query(sprintf('ALTER TABLE %s DROP COLUMN `__yentu_placeholder_col`', $tableName));
            unset($this->placeholders[$tableName]);
        }
    }

    #[\Override]
    protected function _addForeignKey($details)
    {
        $this->query(
            sprintf(
                'ALTER TABLE %s ADD CONSTRAINT `%s` FOREIGN KEY (`%s`) REFERENCES %s (`%s`) ON DELETE %s ON UPDATE %s', $this->buildTableName($details['table'], $details['schema']), $details['name'], implode('`,`', $details['columns']), $this->buildTableName($details['foreign_table'], $details['foreign_schema']), implode('","', $details['foreign_columns']), $details['on_delete'] == '' ? 'NO ACTION' : $details['on_delete'], $details['on_update'] == '' ? 'NO ACTION' : $details['on_update']
            )
        );
    }

    #[\Override]
    protected function _addIndex($details)
    {
        $this->query(
            sprintf(
                'CREATE %s INDEX `%s` ON %s (`%s`)', $details['unique'] ? 'UNIQUE' : '', $details['name'], $this->buildTableName($details['table'], $details['schema']), implode('`, `', $details['columns'])
            )
        );
    }

    #[\Override]
    protected function _addPrimaryKey($details)
    {
        $this->query(
            sprintf('ALTER TABLE %s ADD PRIMARY KEY (`%s`)', $this->buildTableName($details['table'], $details['schema']), implode('`,`', $details['columns'])
            )
        );
        if (is_array($this->autoIncrementPending)) {
            $this->_addAutoPrimaryKey($this->autoIncrementPending);
            $this->autoIncrementPending = null;
        }
    }

    #[\Override]
    protected function _addSchema($name)
    {
        $this->query(sprintf('CREATE SCHEMA `%s`', $name));
    }

    #[\Override]
    protected function _addTable($details)
    {
        $this->query(sprintf('CREATE TABLE %s (__yentu_placeholder_col INT)', $this->buildTableName($details['name'], $details['schema'])));
        $this->placeholders[$this->buildTableName($details['name'], $details['schema'])] = true;
    }

    #[\Override]
    protected function _addUniqueKey($details)
    {
        $this->query(
            sprintf(
                'ALTER TABLE %s ADD CONSTRAINT `%s` UNIQUE (`%s`)', $this->buildTableName($details['table'], $details['schema']), $details['name'], implode('`,`', $details['columns'])
            )
        );
    }

    #[\Override]
    protected function _addView($details)
    {
        if ($details['schema'] != null) {
            $this->query(sprintf('USE `%s`', $details['schema']));
        }
        $this->query(sprintf('CREATE VIEW %s AS %s', $this->buildTableName($details['name'], $details['schema']), $details['definition']));
        if ($details['schema'] != null) {
            $this->query(sprintf('USE `%s`', $this->getDefaultSchema()));
        }
    }

    private function changeColumn($details)
    {
        $this->query(
            sprintf('ALTER TABLE %s CHANGE `%s` `%s` %s %s %s', $this->buildTableName($details['to']['table'], $details['to']['schema']), $details['from']['name'], $details['to']['name'], $this->convertTypes(
                    $details['to']['type'], self::CONVERT_TO_DRIVER, $details['to']['length'] == '' ? 255 : $details['to']['length']
                ), $details['to']['nulls'] === false ? 'NOT NULL' : 'NULL', $details['to']['default'] === null ?
                    ($details['to']['nulls'] === false ? '' : 'DEFAULT NULL') :
                    "DEFAULT {$details['to']['default']}"
            )
        );
    }

    #[\Override]
    protected function _changeColumnName($details)
    {
        $this->changeColumn($details);
    }

    #[\Override]
    protected function _changeColumnNulls($details)
    {
        $this->changeColumn($details);
    }

    #[\Override]
    protected function _changeColumnDefault($details)
    {
        $this->changeColumn($details);
    }

    #[\Override]
    protected function _changeViewDefinition($details)
    {
        $this->query(sprintf("CREATE OR REPLACE VIEW %s AS %s", $this->buildTableName($details['to']['name'], $details['to']['schema']), $details['to']['definition']));
    }

    #[\Override]
    protected function _changeTableName($details)
    {
        $this->query(
            sprintf(
                "RENAME TABLE %s TO %s", $this->buildTableName($details['from']['name'], $details['from']['schema']), $this->buildTableName($details['to']['name'], $details['to']['schema']
                )
        ));
    }

    #[\Override]
    protected function _dropAutoPrimaryKey($details)
    {
        $description = $this->getDescription();
        $column = ($description['tables'][$details['table']]['columns'][$details['column']]);

        $this->query(
            sprintf('ALTER TABLE %s MODIFY `%s` %s %s', $this->buildTableName($details['table'], $details['schema']), $details['column'], $this->convertTypes(
                    $column['type'], self::CONVERT_TO_DRIVER, $column['length'] == '' ? 255 : $column['length']
                ), $column['nulls'] === false ? 'NOT NULL' : ''
            )
        );
    }

    #[\Override]
    protected function _dropColumn($details)
    {
        $description = $this->getDescription();
        if (count($description['tables'][$details['table']]['columns']) === 0) {
            $this->_addColumn(
                Parameters::wrap(array(
                    'table' => $details['table'],
                    'name' => '__yentu_placeholder_col',
                    'type' => 'integer'
                    ), ['schema', 'length', 'nulls']
                )
            );
        }
        $this->query(
            sprintf(
                'ALTER TABLE %s DROP COLUMN `%s`', $this->buildTableName($details['table'], $details['schema']), $details['name']
            )
        );
    }

    #[\Override]
    protected function _dropForeignKey($details)
    {
        $this->query(
            sprintf(
                'ALTER TABLE %s DROP FOREIGN KEY `%s`', $this->buildTableName($details['table'], $details['schema']), $details['name']
            )
        );
    }

    #[\Override]
    protected function _dropIndex($details)
    {
        $this->query(
            sprintf(
                'ALTER TABLE %s DROP INDEX `%s`', $this->buildTableName($details['table'], $details['schema']), $details['name']
            )
        );
    }

    #[\Override]
    protected function _dropPrimaryKey($details)
    {
        try {
            $this->query(
                sprintf(
                    'ALTER TABLE %s DROP PRIMARY KEY', $this->buildTableName($details['table'], $details['schema']), $details['name']
                )
            );
        } catch (\yentu\exceptions\DatabaseManipulatorException $e) {
            $this->_dropAutoPrimaryKey(array(
                'column' => $details['columns'][0],
                'schema' => $details['schema'],
                'table' => $details['table']
            ));
            $this->_dropPrimaryKey($details);
        }
    }

    #[\Override]
    protected function _dropSchema($name)
    {
        $this->query(sprintf('DROP SCHEMA `%s`', $name));
    }

    #[\Override]
    protected function _dropTable($details)
    {
        $this->query(sprintf('DROP TABLE %s', $this->buildTableName($details['name'], $details['schema'])));
    }

    #[\Override]
    protected function _dropUniqueKey($details)
    {
        $this->query(
            sprintf(
                'ALTER TABLE %s DROP KEY `%s`', $this->buildTableName($details['table'], $details['schema']), $details['name']
            )
        );
    }

    #[\Override]
    protected function _dropView($details)
    {
        $this->query(sprintf('DROP VIEW %s', $this->buildTableName($details['name'], $details['schema'])));
    }

    protected function describe()
    {
        $descriptor = new \yentu\descriptors\Mysql($this);
        return $descriptor->describe();
    }

    protected function getDriverName()
    {
        return 'mysql';
    }

    #[\Override]
    public function quoteIdentifier(string $identifier):string
    {
        return "`$identifier`";
    }

}
