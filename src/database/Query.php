<?php
namespace yentu\database;

class Query extends DatabaseItem
{
    private $results;
    private $query;
    private $bindData;
    
    public function __construct($query, $bindData = array()) 
    {
        $this->query = $query;
        $this->bindData = $bindData;
    }
    
    #[\Override]
    public function init()
    {
        $this->results = $this->getDriver()->executeQuery(
            array(
                'query' => $this->query, 
                'bind' => $this->bindData
            )
        );
    }
    
    #[\Override]
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
}
