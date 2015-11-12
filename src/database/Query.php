<?php
namespace yentu\database;

class Query extends DatabaseItem
{
    private $results;
    
    public function __construct($query, $bindData = array()) 
    {
        $this->results = $this->getDriver()->executeQuery(
            array(
                'query' => $query, 
                'bind' => $bindData
            )
        );
    }
    
    protected function buildDescription() 
    {
        return array();
    }
    
    public function reverse($query, $bindData = array())
    {
        $this->getDriver()->reverseQuery(array(
            'query' => $query,
            'bind_data' => $bindData
        ));
        return $this;
    }

    public function commitNew() 
    {
        
    }
}
