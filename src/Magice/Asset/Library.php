<?php
namespace Magice\Asset {

    /**
     * Class Library
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     * @deprecated
     */
    class Library
    {
        public $dev;
        public $sources;
        public $path;

        public function __construct($path, $dev = true)
        {
            $this->dev  = $dev;
            $this->path = $path;
        }
    }
}