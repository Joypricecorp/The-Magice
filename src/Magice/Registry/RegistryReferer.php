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
    class RegistryReferer implements RegistryInterface, \JsonSerializable
    {
        /**
         * @var array|object $data The refered data
         */
        protected $data;

        /**
         * @var RefererInterface
         */
        protected $handler;

        /**
         * Constructor
         *
         * @param array|object $data The refered data to handle
         * @throws \InvalidArgumentException
         */
        function __construct(&$data)
        {
            $this->data =& $data;

            if (is_array($data)) {
                $this->handler = new RefererArray();
                return null;
            }

            if (is_object($data)) {
                $this->handler = new RefererObject();
                return null;
            }

            throw new \InvalidArgumentException('Accept only Array or Object data type.');
        }

        /**
         * {@inheritDoc}
         * @api
         */
        function set($path, $value)
        {
            return $this->handler->set($this->data, $path, $value);
        }

        /**
         * {@inheritDoc}
         * @api
         */
        function get($path, $default = null)
        {
            return $this->handler->get($this->data, $path, $default);
        }

        /**
         * Return data which should be serialized by json_encode().
         * @return  mixed
         */
        public function jsonSerialize()
        {
            return $this->data;
        }
    }
}