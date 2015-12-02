<?php

/* 
 * The MIT License
 *
 * Copyright 2015 James Ekow Abaka Ainooson.
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

namespace yentu;

/**
 * Timer class contains routines for timing yentu migrations.
 */
class Timer
{
    /**
     * The stored start time.
     * @var double 
     */
    private $startTime;
    
    /**
     * Singleton instance.
     * @var Timer 
     */
    private static $instance;
    
    /**
     * Set the singleton instance.
     * @param Timer $instance
     */
    public static function setInstance($instance)
    {
        self::$instance = $instance;
    }
    
    /**
     * Gets an instance of the Timer.
     * @return \yentu\Timer
     */
    private static function instance()
    {
        if(self::$instance === null)
        {
            self::$instance = new Timer();
        }
        return self::$instance;
    }
    
    /**
     * Start the timer.
     */
    public function startInstance()
    {
        $this->startTime = microtime(true);
    }
    
    /**
     * Stop the timer and return the time elapsed since it was started.
     * @return double
     */
    public function stopInstance()
    {
        return microtime(true) - $this->startTime;
    }
    
    /**
     * Print the time elasped in a pretty format.
     * 
     * @param double $time
     * @return string
     */
    public static function pretty($time)
    {
        $millis = ($time - floor($time)) * 1000;
        $secs = round($time, 2);
        return $secs > 0 ? "{$secs} seconds" : "{$millis} milliseconds";
    }
    
    /**
     * Start the embedded timer instance.
     */
    public static function start()
    {
        self::instance()->startInstance();
    }
    
    /**
     * Stop the embeded timer instance and return the time elasped.
     * @return double
     */
    public static function stop()
    {
        return self::instance()->stopInstance();
    }
}
