<?php

/* 
 * The MIT License
 *
 * Copyright 2015 ekow.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace yentu\database;

class Begin extends DatabaseItem
{
    private $defaultSchema;
    
    public function __construct($defaultSchema)
    {
        $this->defaultSchema = new Schema($defaultSchema);
    }
    
    public function table($name) 
    {
        return $this->create('table', $name, $this);
    }
    
    public function schema($name)
    {
        return $this->create('schema', $name, $this);
    }
    
    public function view($name)
    {
        return $this->create('view', $name, $this);
    }    
    
    public function getName()
    {
        return $this->defaultSchema->getName();
    }

    protected function buildDescription() {}

    public function commitNew() {}
    
    public function end() {
        DatabaseItem::purge();
    }
    
    public function query($query, $bindData = array())
    {
        //return new Query($query, $bindData);
        return $this->create('query', $query, $bindData);
    }    
}
