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

namespace yentu\tests\schema_info;

class SqliteInfo extends \yentu\tests\SchemaInfo
{
    public function foreignKeyExists($table) {
        
    }

    public function columnExists($column) 
    {
        $columns = $this->pdo->query("PRAGMA table_info({$this->table['table']})")->fetchAll(\PDO::FETCH_ASSOC);
        foreach($columns as $tableColumn)
        {
            if($column === $tableColumn['name']) return true;
        }
        return false;
    }

    public function schemaExists($table) {
        
    }

    public function tableExists($table) 
    {
        $count = $this->pdo->query("SELECT count(*) as c FROM sqlite_master WHERE name = '$table'")->fetchAll(\PDO::FETCH_ASSOC);
        return $count[0]['c'] == '1';
    }

}