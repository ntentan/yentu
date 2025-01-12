<?php

namespace yentu\exceptions;

use yentu\Yentu;

/**
 * Reports syntax errors in migration scripts with an attempt to show the actual line where error ocurred.
 */
class SyntaxErrorException extends YentuException {

    public function __construct(string $message, string $homePath) 
    {

        parent::__construct($message);
        foreach ($this->getTrace() as $item) {
            if (realpath($homePath . DIRECTORY_SEPARATOR . "migrations") === dirname($item['file'] ?? '')) {
                $this->message = "On line {$item['line']} of {$item['file']}: {$this->message}";
                break;
            }
        }
    }

}
