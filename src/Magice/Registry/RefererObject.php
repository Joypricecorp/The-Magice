<?php
namespace Magice\Registry {

    /**
     * Class RefererObject
     *
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class RefererObject implements RefererInterface
    {
        /**
         * {@inheritDoc}
         * @api
         */
        public function set(&$data, $path, $value)
        {
            // if key exist with . (dot)
            if (isset($data[$path])) {
                return $data[$path] = $value;
            }

            $result = null;

            /**
             * Explode the registry path into an array and remove empty
             * nodes that occur as a result of a double dot. ex: joomla..test
             * Finally, re-key the array so they are sequential.
             */
            $nodes = array_values(array_filter(explode('.', $path), 'strlen'));

            if ($nodes) {
                // Initialize the current node to be the registry root.
                $node = $data;

                // Traverse the registry to find the correct node for the result.
                for ($i = 0, $n = count($nodes) - 1; $i < $n; $i++) {
                    if (!isset($node->$nodes[$i]) && ($i != $n)) {
                        $node->$nodes[$i] = new \stdClass;
                    }

                    $node = $node->$nodes[$i];
                }

                // Get the old value if exists so we can return it
                $result = $node->$nodes[$i] = $value;
            }

            return $result;
        }

        /**
         * {@inheritDoc}
         * @api
         */
        public function get($data, $path, $default = null)
        {
            // if key exist with . (dot)
            if (isset($data->$path)) {
                return $data->$path !== $default ? $data->$path : $default;
            }

            $result = $default;

            // Explode the registry path into an array
            $nodes = explode('.', $path);

            // Initialize the current node to be the registry root.
            $node  = $data;
            $found = false;

            // Traverse the registry to find the correct node for the result.
            foreach ($nodes as $n) {
                if (isset($node->$n)) {
                    $node  = $node->$n;
                    $found = true;
                } else {
                    $found = false;
                    break;
                }
            }

            if ($found && $node !== $result) {
                $result = $node;
            }

            return $result;
        }
    }
}