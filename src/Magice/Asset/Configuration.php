<?php
namespace Magice\Asset;

class Configuration
{
    const DEV_MAST = '*';

    protected static $isDev;
    protected static $file;

    public static function setConfigurationFile($file)
    {
        self::$file = $file;
    }

    public static function setMode($flag)
    {
        self::$isDev = $flag;
    }

    public static function parse()
    {
        try {
            return json_decode(file_get_contents(self::$file), true);
        } catch (\RuntimeException $e) {
            throw $e;
        }
    }
}