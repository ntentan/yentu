<?php
namespace yentu\database;

class Query extends DatabaseItem implements Commitable
{
    private string $query;
    private array $bindData;
    private array $queryData;

    
    public function __construct(string $query, array $bindData = []) 
    {
        $this->query = $query;
        $this->bindData = $bindData;
    }
    
    #[\Override]
    public function init()
    {
        $this->queryData = [
            'query' => $this->query,
            'bind' => $this->bindData
        ]; 
        $this->new = true;
    }
    
    public function rollback(string $query, array $bindData = []): DatabaseItem
    {
        $this->queryData['rollback_query'] = $query;
        $this->queryData['rollback_bind'] = $bindData;
        $this->new = true;
        return $this;
    }

    #[\Override]
    public function commitNew()
    {
        $this->getDriver()->executeQuery($this->queryData);        
    }
}
