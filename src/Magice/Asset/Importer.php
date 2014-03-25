<?php
namespace Magice\Asset;

use Magice\Exception\Exception;

class Importer
{
    protected static $libs = array();
    protected static $files = array();
    protected static $tags = array();

    public static function import($type, $keys, $callback = null)
    {
        if (is_string($keys)) {
            $keys = (array) $keys;
        }

        if (!isset(static::$tags[$type])) {
            self::_import($keys);
        }

        $tags = array_key_exists($type, static::$tags) ? static::$tags[$type] : null;

        if (empty($tags)) {
            throw Exception::createMessage("Asset importer cannot found keys: %s, type: %s.", implode(',', $keys), $type);
        }

        unset(static::$tags[$type]);

        return implode("\n", $tags);

    }

    public static function script()
    {
        $keys = func_num_args() > 1 ? func_get_args() : (array) func_get_arg(0);
        return self::import('js', $keys);
    }

    public static function style()
    {
        $keys = func_num_args() > 1 ? func_get_args() : (array) func_get_arg(0);
        return self::import('css', $keys);
    }

    protected static function _import($keys)
    {
        $libs   = static::$libs ? : Configuration::parse();
        $params = $libs['parameters'];
        $async  = $libs['async'];

        foreach ($keys as $key) {
            self::find($key, $libs['libraries']);
        }

        foreach (static::$files as $key => $file) {
            $file = preg_replace_callback(
                '/\{(.*)\}/i',
                function ($match) use ($params) {
                    return isset($params[$match[1]]) ? $params[$match[1]] : null;
                },
                $file
            );

            if (preg_match('/css/', $key)) {
                static::$tags['css'][] = self::tagStyle($file);
            } else {
                static::$tags['js'][] = self::tagScript($file, $async);
            }
        }
    }

    protected static function find($key, $libs)
    {
        $root = self::get($key, $libs);

        if (is_array($root)) {
            $ls = \Magice\Utils\Arrays\Paths::run($root);
            foreach ($ls as $k => $v) {
                if (preg_match('/@/', $k)) {
                    if (!array_key_exists($v, static::$files)) {
                        self::find($v, $libs, static::$files);
                    }
                } else {
                    static::$files[$key . '.' . $k] = $v;
                }
            }
        } else {
            if ($root) {
                static::$files[$key] = $root;
            }
        }
    }

    protected static function get($key, $from)
    {
        $value = $from;
        $path  = explode('.', $key);

        for ($i = 0; $i < count($path); $i++) {
            $key = $path[$i];

            if (!isset($value[$key])) {
                return null;
            }

            if (!is_array($value)) {
                return null;
            }

            $value = $value[$key];
        }

        return $value;
    }

    protected static function tagScript($src, $async = true)
    {
        return sprintf('<script type="text/javascript" src="%s"%s></script>', $src, $async ? ' async' : '');
    }

    protected static function tagStyle($src)
    {
        return sprintf('<link rel="stylesheet" type="text/css" href="%s">', $src);
    }
}