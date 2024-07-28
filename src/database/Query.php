<?php
namespace yentu\database;

class Query extends DatabaseItem implements Commitable
{
    private string $query;
    private array $bindData;
    private string $rollbackQuery;
    private array $rollbackBindData;
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
            'query_data' => $this->bindData
        ]; 
    }
    
    public function rollback(string $query, array $bindData = []): DatabaseItem
    {
        $this->queryData['rollback'] = $query;
        $this->queryData['rollback_data'] = $bindData;
        return $this;
    }

    #[\Override]
    public function commitNew()
    {
        $this->getDriver()->executeQuery($this->queryData);        
    }
}
