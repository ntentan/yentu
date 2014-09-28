<?php
namespace yentu;

class SyntaxErrorException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
        foreach($this->getTrace() as $item)
        {
            if(realpath(Yentu::getPath('migrations')) === dirname($item['file']))
            {
                $this->message .= " on line {$item['line']} of {$item['file']}";
                break;
            }
        }
    }
}