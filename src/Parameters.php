<?php
namespace yentu;

class Parameters 
{   
    public static function wrap($parameters, $defaults = []) 
    {
        if(!is_array($parameters)) {
            return $parameters;
        } 
        
        foreach($defaults as $key => $value) {
            if(is_numeric($key) && !isset($parameters[$value])) {
                $parameters[$value] = null;
            } else if(!is_numeric($key) && !isset($parameters[$key])) {
                $parameters[$key] = $value;              
            }
        }        
        return $parameters;
    }

    /**
     * Parses a PDO style DSN string or a configuration array containing a 'dsn' key.
     * Specific values in the configuration array override values extracted from the DSN.
     *
     * @param string|array|null $config
     * @return array
     */
    public static function parseDsn($config): array
    {
        if (is_string($config)) {
            return self::parseDsnString($config);
        }

        if (!is_array($config)) {
            return [];
        }

        if (isset($config['dsn']) && is_string($config['dsn']) && trim($config['dsn']) !== '') {
            $parsedDsn = self::parseDsnString($config['dsn']);
            $result = $parsedDsn;
            foreach ($config as $key => $value) {
                if ($key !== 'dsn' && $value !== null && $value !== '') {
                    $result[$key] = $value;
                }
            }
            return $result;
        }

        unset($config['dsn']);
        return $config;
    }

    /**
     * Parse a PDO style DSN string into key-value pairs.
     *
     * @param string $dsn
     * @return array
     */
    public static function parseDsnString(string $dsn): array
    {
        $dsn = trim($dsn);
        $parsed = [];
        $driver = null;

        $colonPos = strpos($dsn, ':');
        if ($colonPos !== false) {
            $driver = strtolower(substr($dsn, 0, $colonPos));
            $paramsString = substr($dsn, $colonPos + 1);
        } else {
            $paramsString = $dsn;
        }

        if ($driver === 'pgsql' || $driver === 'postgres') {
            $driver = 'postgresql';
        }

        if ($driver !== null && $driver !== '') {
            $parsed['driver'] = $driver;
        }

        if ($driver === 'sqlite') {
            if ($paramsString !== '' && strpos($paramsString, '=') === false) {
                $parsed['file'] = $paramsString;
                return $parsed;
            }
        }

        $parts = explode(';', $paramsString);
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            if (strpos($part, '=') !== false) {
                list($key, $value) = explode('=', $part, 2);
                $key = trim($key);
                $value = trim($value);
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }
                $parsed[$key] = $value;
            }
        }

        if (isset($parsed['database']) && !isset($parsed['dbname'])) {
            $parsed['dbname'] = $parsed['database'];
        }
        if (isset($parsed['username']) && !isset($parsed['user'])) {
            $parsed['user'] = $parsed['username'];
        }
        if (isset($parsed['db']) && !isset($parsed['dbname'])) {
            $parsed['dbname'] = $parsed['db'];
        }
        if ($driver === 'sqlite' && isset($parsed['dbname']) && !isset($parsed['file'])) {
            $parsed['file'] = $parsed['dbname'];
        }

        return $parsed;
    }
}
