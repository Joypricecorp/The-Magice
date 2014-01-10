<?php
namespace Magice\Registry {

    /**
     * Class RegistryReferer
     * @package     Magice\Registry
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class RegistryReadOnly extends Registry
    {

        /**
         * {@inheritDoc}
         * @api
         */
        function set($path, $value)
        {
            throw new \LogicException('Impossible to call set() on a Read-Only Registry.');
        }
    }
}