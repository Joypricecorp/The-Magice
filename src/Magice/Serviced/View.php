<?php
namespace Magice\Serviced {

    use Magice\Service\Serviced;

    /**
     * Class View
     *
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     *
     * @version     1.0
     * @since       1.0
     */

    /**
     * @method static registerModulars(array $mods)
     * @method static addModular($section, $mod)
     * @method static removeModular($name, $section = '')
     * @method static ingoreModular($flag)
     * @method static section($name, array $section)
     * @method static sections(array $sections)
     * @method static content($text)
     * @method static scoped($css)
     * @method static title($str)
     * @method static language($str)
     * @method static meta($name, $content = '', $http_equiv = false)
     */
    class View extends Serviced
    {
        /**
         * @var string The service name
         */
        const NAME = ':mvc.view';
    }
}