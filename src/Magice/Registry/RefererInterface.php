<?php
namespace Magice\Registry {

    /**
     * Class Referer
     * @package     Magice\Registry
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    interface RefererInterface
    {
        /**
         * Set a registry value.
         *
         * @param   array  $data  The data that refer to set
         * @param   string $path  Registry Path (e.g. joomla.content.showauthor)
         * @param   mixed  $value Value of entry
         *
         * @return  mixed  The value of the that has been set.
         * @since   1.0
         */
        public function set(&$data, $path, $value);

        /**
         * Get a registry value.
         *
         * @param   array  $data    The data that refer to get
         * @param   string $path    Registry path (e.g. joomla.content.showauthor)
         * @param   mixed  $default Optional default value, returned if the internal value is null.
         *
         * @return  mixed  Value of entry or null
         * @since   1.0
         */
        public function get($data, $path, $default = null);
    }
}