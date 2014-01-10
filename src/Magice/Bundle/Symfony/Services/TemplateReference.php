<?php
namespace Magice\Bundle\Symfony\Services {

    use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference as BaseTemplateReference;

    /**
     * Class TemplateReference
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class TemplateReference extends BaseTemplateReference
    {
        public function __construct($bundle = null, $controller = null, $name = null, $format = null, $engine = null, $scoped = null)
        {
            $this->parameters = array(
                'bundle'     => $bundle,
                'controller' => $controller,
                'name'       => $name,
                'format'     => $format,
                'engine'     => $engine,
                'scoped'     => $scoped
            );
        }
    }
}
