<?php

namespace yentu\drivers;

abstract class Pdo extends \yentu\DatabaseDriver
{
    /**
     *
     * @var \PDO
     */
    protected $pdo;
    
    protected function connect($params) 
    {
        $username = $params['username'];
        $password = $params['password'];
        
        unset($params['username']);
        unset($params['password']);
        unset($params['driver']);
        
        $this->pdo = new \PDO(
            $this->getDriverName() . ":" . $this->expand($params),
            $username,
            $password
        );
    }
    
    protected function query($query)
    {
        $return = array();
        $result = $this->pdo->query($query, \PDO::FETCH_ASSOC);
        
        if($result === false)
        {
            throw new \Exception(array_pop($this->pdo->errorInfo()));
        }

        foreach($result as $row)
        {
            $return[] = $row;
        }

        return $return;
    }
    
    private function expand($params)
    {
        $equated = array();
        foreach($params as $key => $value)
        {
            $equated[] = "$key=$value";
        }
        return implode(';', $equated);
    }
    
    abstract protected function getDriverName();
}
