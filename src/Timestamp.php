<?php
namespace yentu;

class Timestamp
{
    public static function get()
    {
        return date('YmdHis', time());
    }
}

