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
    
    abstract protected function getDriverName();
}
