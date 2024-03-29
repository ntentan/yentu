<?php
namespace yentu;

class CodeWriter
{
    private $lines = "";
    private $indentation = 0;
    
    public function add($line = '')
    {
        $this->lines .= str_repeat(' ', $this->indentation) . "$line\n";
    }
    
    public function addNoLn($line = '')
    {
        $this->lines .= str_repeat(' ', $this->indentation) . "$line";
    }
    
    public function addNoIndent($line = '')
    {
        $this->lines .= $line;
    }
    
    public function ln()
    {
        $this->lines .= "\n";
    }

    
    public function addIndent()
    {
        $this->indentation += 4;
    }
    
    public function decreaseIndent()
    {
        $this->indentation -= 4;
    }
    
    public function __toString()
    {
        $timestamp = $this->getTimestamp();
        $header = <<< HEAD
<?php
/**
 * Generated by yentu on $timestamp
 */

HEAD;
        return $header . $this->lines;
    }
    
    public function getTimestamp()
    {
        return date('jS F, Y H:i:s T');
    }
}

