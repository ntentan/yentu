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
        return $this->getDriver()->doesIndexExist($constraint);
    }

    #[\Override]
    protected function addKey($constraint)
    {
        $this->getDriver()->addIndex($constraint);
    }

    #[\Override]
    protected function dropKey($constraint)
    {
        $this->getDriver()->dropIndex($constraint);
    }

    #[\Override]
    protected function getNamePostfix()
    {
        return 'idx';
    }

    #[\Override]
    protected function buildDescription()
    {
        return parent::buildDescription() + ['unique' => $this->unique];
    }
}
