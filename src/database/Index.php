<?php
namespace yentu\database;


class Index extends BasicKey
{
    protected $unique = false;

    public function unique($unique = true)
    {
        $this->unique = $unique;
        return $this;
    }

    #[\Override]
    protected function doesKeyExist($constraint)
    {
        return $this->getChangeLogger()->doesIndexExist($constraint);
    }

    #[\Override]
    protected function addKey($constraint)
    {
        $this->getChangeLogger()->addIndex($constraint);
    }

    #[\Override]
    protected function dropKey($constraint)
    {
        $this->getChangeLogger()->dropIndex($constraint);
    }

    #[\Override]
    protected function getNamePostfix()
    {
        return 'idx';
    }

    #[\Override]
    public function buildDescription()
    {
        return parent::buildDescription() + ['unique' => $this->unique];
    }
}
