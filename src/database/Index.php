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

    protected function doesKeyExist($constraint)
    {
        return $this->getDriver()->doesIndexExist($constraint);
    }

    protected function addKey($constraint)
    {
        $this->getDriver()->addIndex($constraint);
    }

    protected function dropKey($constraint)
    {
        $this->getDriver()->dropIndex($constraint);
    }

    protected function getNamePostfix()
    {
        return 'idx';
    }

    protected function buildDescription()
    {
        return parent::buildDescription() + ['unique' => $this->unique];
    }
}
