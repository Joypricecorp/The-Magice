<?php
namespace Magice\Utils {

    use Joomla\Utilities\ArrayHelper;
    use Magice\Utils\Arrays\Explode;
    use Magice\Utils\Arrays\Plot;
    use Magice\Utils\Arrays\Paths;

    class Arrays
    {
        /**
         * Explode tree to node structer
         *
         * @param array  $array
         * @param string $delimiter
         * @param bool   $baseval
         *
         * @return array
         */
        public static function explode(array $array, $delimiter = '_', $baseval = false)
        {
            return Explode::run($array, $delimiter, $baseval);
        }

        /**
         * Print the tree
         *
         * @param array $array
         * @param int   $indent
         * @param bool  $mother_run
         */
        public static function plot(array $array, $indent = 0, $mother_run = true)
        {
            Plot::run($array, $indent, $mother_run);
        }

        /**
         * Export array to path
         *
         * @param array  $array
         * @param string $separate
         *
         * @return array
         */
        public static function paths(array $array, $separate = '.')
        {
            return Paths::run($array, $separate);
        }

        /**
         * Method to determine if an array is an associative array.
         *
         * @param   array $array An array to test.
         *
         * @return  boolean  True if the array is an associative array.
         * @since   1.0
         */
        public static function isAssoc($array)
        {
            if (is_array($array)) {
                foreach (array_keys($array) as $k => $v) {
                    if ($k !== $v) {
                        return true;
                    }
                }
            }

            return false;
        }

        public static function toAttrs(array $array)
        {
            return join(' ', array_map(function ($sKey) use ($array) {
                if (is_bool($array[$sKey])) {
                    return $array[$sKey] ? $sKey : '';
                }

                return $sKey . '="' . $array[$sKey] . '"';
            }, array_keys($array)));
        }

        public static function fromTag($tag)
        {
            $tag = $tag instanceof \SimpleXMLElement ? $tag : simplexml_load_string($tag);

            $attrs = array();
            foreach ($tag->attributes() as $attr) {
                /**
                 * @var \SimpleXMLElement $attr
                 */
                $attrs[$attr->getName()] = (string) $attr;
            }

            return $attrs;
        }
    }
}