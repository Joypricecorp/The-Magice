<?php
namespace Magice\Utils {

    /**
     * Class String
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class String
    {
        /**
         * Camel to underscore.
         *
         * @param string $id The string to underscore
         *
         * @return string The underscored string
         */
        public static function underscore($id)
        {
            return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1_\\2', '\\1_\\2'), strtr($id, '_', '.')));
        }

        /**
         * Camelizes a string.
         *
         * @param string $id A string to camelize
         *
         * @return string The camelized string
         */
        public static function camelize($id)
        {
            return strtr(ucwords(strtr($id, array('_' => ' ', '.' => '_ ', '-' => ' '))), array(' ' => ''));
        }

        public static function dotToCamelize($id, $separate = '\\')
        {
            return strtr(ucwords(strtr($id, array('_' => ' ', '.' => $separate . ' ', '-' => ' '))), array(' ' => ''));
        }

        public static function isJson($string)
        {
            if (!is_string($string)) {
                return false;
            }

            try {
                // try to decode string
                json_decode($string);
            } catch (\ErrorException $e) {
                // exception has been caught which means argument wasn't a string and thus is definitely no json.
                return false;
            }

            // check if error occured
            return (json_last_error() == JSON_ERROR_NONE);
        }
    }
}