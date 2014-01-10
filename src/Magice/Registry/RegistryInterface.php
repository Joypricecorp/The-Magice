<?php
namespace Magice\Registry {

    /**
     * Class RegistryInterface
     * @package     Magice\Registry
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    interface RegistryInterface
    {
        /**
         * Set a registry value.
         *
         * @param   string $path  Registry Path (e.g. joomla.content.showauthor)
         * @param   mixed  $value Value of entry
         *
         * @return  mixed  The value of the that has been set.
         */
        public function set($path, $value);

        /**
         * Get a registry value.
         *
         * @param   string $path    Registry path (e.g. joomla.content.showauthor)
         * @param   mixed  $default Optional default value, returned if the internal value is null.
         *
         * @return  mixed  Value of entry or null
         */
        public function get($path, $default = null);
    }
}