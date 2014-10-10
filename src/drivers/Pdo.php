<?php

namespace yentu\drivers;

use yentu\Yentu;

abstract class Pdo extends \yentu\DatabaseDriver
{
    /**
     *
     * @var \PDO
     */
    private $pdo;
    protected $defaultSchema;
    
    protected function connect($params) 
    {
        $username = $params['user'];
        $password = $params['password'];
        
        unset($params['driver']);
        
        $this->pdo = new \PDO(
            $this->getDriverName() . ":" . $this->expand($params),
            $username,
            $password
        );
    }
    
    public function disconnect()
    {
        $this->pdo = null;
    }
    
    public function getDefaultSchema()
    {
        return $this->defaultSchema;
    }
    
    protected function quote($string)
    {
        return $this->pdo->quote($string);
    }
    
    public function query($query, $bindData = false)
    {
        $return = array();
        
        Yentu::out("\n    > Running Query [$query]", Yentu::OUTPUT_LEVEL_3);
        
        if(is_array($bindData))
        {
            $statement = $this->pdo->prepare($query);
            if($statement->execute($bindData) === false)
            {
                $errorInfo = $this->pdo->errorInfo();
                throw new \Exception($errorInfo[2]);                
            }
            else 
            {
                $return = $statement->fetchAll(\PDO::FETCH_ASSOC);
            }
        }
        else
        {
            $result = $this->pdo->query($query, \PDO::FETCH_ASSOC);
           if($result === false)
            {
                $errorInfo = $this->pdo->errorInfo();
                throw new \yentu\DatabaseDriverException("{$errorInfo[2]}. Query [$query]");                
            }
            else 
            {
                $return = $result->fetchAll();
            }
        }
        
        return $return;
    }
    
    private function expand($params)
    {
        $equated = array();
        foreach($params as $key => $value)
        {
            if($value == '') { continue; }
            $equated[] = "$key=$value";
        }
        return implode(';', $equated);
    }
    
    protected function setupTable($details)
    {
        if(isset($details['columns']))
        {
            foreach($details['columns'] as $column)
            {
                $column['table'] = $details['name'];
                $column['schema'] = $details['schema'];
                $this->_addColumn($column);
            }
        }        
        
        if(isset($details['primary_key']))
        {
            $primaryKey = array(
                'columns' => reset($details['primary_key']),
                'name' => key($details['primary_key']),
                'table' => $details['name'],
                'schema' => $details['schema']
            );
            $this->_addPrimaryKey($primaryKey);
        }
        
        if(isset($details['unique_keys']))
        {
            foreach($details['unique_keys'] as $name => $columns)
            {
                $uniqueKey = array(
                    'name' => $name,
                    'columns' => $columns,
                    'table' => $details['name'],
                    'schema' => $details['schema']
                );
                $this->_addUniqueKey($uniqueKey);
            }
        }
        
        if(isset($details['foreign_keys']))
        {
            foreach($details['foreign_keys'] as $name => $foreignKey)
            {
                $foreignKey['name'] = $name;
                $foreignKey['table'] = $details['name'];
                $foreignKey['schema'] = $details['schema'];
                $this->_addForeignKey($foreignKey);
            }
        }
        
        if(isset($details['indices']))
        {
            foreach($details['indices'] as $name => $index)
            {
                $index = array(
                    'name' => $name,
                    'columns' => $index,
                    'table' => $details['name'],
                    'schema' => $details['schema']
                );
            }
        }
        
    }
    
    abstract protected function getDriverName();
}
