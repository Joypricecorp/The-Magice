<?php
namespace Magice\Bundle\Symfony\Services {

    use Symfony\Bundle\TwigBundle\TwigEngine as BaseTwigEngine,
        Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;

    /**
     * Class TwigEngine
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class TwigEngine extends BaseTwigEngine
    {
        // TODO: override render if you want
        /**
         * {@inheritdoc}
         */
        public function render($name, array $parameters = array())
        {
            return parent::render($name, $parameters);
        }
    }
}