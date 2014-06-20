<?php
/*
 * This file is part of the The Magice Project.
 *
 * @copyright   2012-2014 ツ Joyprice corporation Ltd.
 * @license     http://www.joyprice.org/license
 * @link        http://www.joyprice.org/themagice
 */

namespace {

    use Magice\Service\Builder,
        Magice\Service\Container;

    /**
     * The Magice Singleton Base
     *
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Magice
    {
        /**
         * @var string Project name
         */
        const NAME = 'The Magice';

        /**
         * @var string Project namespace
         */
        const NAME_SPACE = 'magice';

        /**
         * @var string Project version
         */
        const VERSION = '0.3-alpha';
    }
}
