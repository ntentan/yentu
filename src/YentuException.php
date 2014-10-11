<?php
namespace yentu;

class YentuException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
        foreach($this->getTrace() as $item)
        {
            if(realpath(Yentu::getPath('migrations')) === dirname($item['file']))
            {
                $this->message .= ". Exception was thrown by action on line {$item['line']} of {$item['file']}";
                break;
            }
        }
    }
}