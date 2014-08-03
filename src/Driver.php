<?php
namespace yentu;

abstract class Driver{
    abstract public function connect();
    abstract public function query();
    abstract public function describe();
}
