<?php
namespace Magice\Asset;

use Magice\Exception\Exception;

class Importer
{
    protected static $libs = array();
    protected static $files = array();
    protected static $tags = array();
    protected static $params = array();

    public static function import($type, $keys = null, $callback = null)
    {
        // TODO: split manage isType with other block
        $isType = in_array(strtolower($type), array('js', 'css'));

        if ($isType) {
            if (is_string($keys)) {
                $keys = (array) $keys;
            }
        } else {
            if (is_string($type)) {
                $keys = (array) $type;
            }
        }

        if (!isset(static::$tags[$type])) {
            self::_import($keys);
        }

        if ($isType) {
            $tags = array_key_exists($type, static::$tags) ? static::$tags[$type] : null;

            if (empty($tags)) {
                throw Exception::createMessage("Asset importer cannot found keys: %s, type: %s.", implode(',', $keys), $type);
            }

            unset(static::$tags[$type]);
        } else {
            $tags = static::$tags;
        }

        if ($isType) {
            return implode("\n", $tags);
        } else {
            return
                (empty($tags['css']) ? '' : implode("\n", $tags['css'])) .
                (empty($tags['js']) ? '' : implode("\n", $tags['js']));
        }

    }

    public static function asset($keys)
    {
        return self::import($keys);
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

    public static function setParameter($key, $value)
    {
        static::$params[$key] = $value;
    }

    protected static function _import($keys)
    {
        $libs  = static::$libs ? : Configuration::parse();
        $async = $libs['async'];

        if (empty($libs)) {
            throw Exception::create(
                "Somthing wrong 'assets.json' empty. Usually json standard file not allow comment block. Have you any comment block in your assets.json?"
            );
        }

        // @NOTE: $params use in use($params)
        $params = static::$params = array_merge(static::$params, (array) $libs['parameters']);

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
                    $v = is_string($v) ? ((array) $v) : $v;
                    foreach ($v as $vv) {
                        if (!array_key_exists($vv, static::$files)) {
                            self::find($vv, $libs, static::$files);
                        }
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
        $start = '';
        $end   = '';

        if (preg_match('/\[if(.*)\]/', $src, $match)) {
            $start = '<!--' . $match[0] . '>';
            $end   = '<![endif]-->';
            $src   = str_replace($match[0], '', $src);
        }

        return sprintf('%s<script type="text/javascript" src="%s"%s></script>%s', $start, trim($src), $async ? ' async' : '', $end);
    }

    protected static function tagStyle($src)
    {
        return sprintf('<link rel="stylesheet" type="text/css" href="%s">', trim($src));
    }
}