<?php
namespace yentu;

class Yentu
{
    const OUTPUT_LEVEL_0 = 0;
    const OUTPUT_LEVEL_1 = 1;
    const OUTPUT_LEVEL_2 = 2;
    const OUTPUT_LEVEL_3 = 3;
    
    private static $home = './yentu';
    private static $level = Yentu::OUTPUT_LEVEL_1;
    private static $streamResource;
    private static $streamUrl = 'php://stdout';


    public static function setDefaultHome($home)
    {
        self::$home = $home;
    }    
    
    public static function getPath($path)
    {
        return self::$home . "/$path";
    }
    
    public static function setStreamUrl($url)
    {
        self::$streamUrl = $url;
    }

    /**
     * 
     * @param type $string
     */
    public static function out($string, $level = Yentu::OUTPUT_LEVEL_1)
    {
        if(!is_resource(self::$streamResource))
        {
            self::$streamResource = fopen(self::$streamUrl, 'w');
        }
        if($level <= self::$level)
        {
            fputs(self::$streamResource, $string);
            fflush(self::$streamResource);
        }
    }
}

