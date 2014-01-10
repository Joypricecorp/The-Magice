<?php
namespace Magice\Bundle\Symfony\Services {

    use Magice\Service\ContainerBasicInterface;
    use Magice\Service\ContainerAwareInterface;
    use Magice\Serviced\Msg;
    use Magice\Serviced\Param;

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Twig extends \Twig_Extension implements ContainerAwareInterface
    {
        protected $container;

        public function getFunctions()
        {
            return array(
                /**
                 * console dump
                 * @use cd(var)
                 */
                'cd'    => new \Twig_SimpleFunction('cd', function ($var) {
                        \Kint::dump($var);
                    }),
                /**
                 * console dump
                 * @use cd(var)
                 */
                'cs'    => new \Twig_SimpleFunction('cs', function ($var) {
                        echo '<pre>';
                        print_r($var);
                        echo '</pre>';
                    }),
                /**
                 * die application
                 * @use exit($mst=null)
                 */
                'exit'  => new \Twig_SimpleFunction('can', function ($var = null) {
                        exit($var);
                    }),
                /**
                 * get parameter from parameter bag container
                 * @use param(param-name) or param(:param) : = magice.
                 */
                'Param' => new \Twig_SimpleFunction('Param', function ($var) {

                        if (strpos($var, ':') === 0) {
                            $var = str_replace(':', 'magice.', $var);
                        }

                        return Param::get($var);
                    }),
                /**
                 * get flash message
                 * @use Msg(var-name)
                 */
                'Msg' => new \Twig_SimpleFunction('Msg', function ($var) {

                        return Msg::get($var);
                    }),
                /**
                 * check user role(s) that right to act
                 * @use role('admin')
                 */
                'Role'  => new \Twig_SimpleFunction('Role', function ($var) {
                        // todo
                    }),
                /**
                 * check user permission(s) that right to act
                 * @use can('edit')
                 */
                'Can'   => new \Twig_SimpleFunction('Can', function ($var) {
                        // todo acl/rbac
                    }),

                '_' => new \Twig_SimpleFunction('_', function ($str) {
                        return $str; // TODO: translate service
                    })
            );
        }

        /**
         * get extesion name
         * @return string
         */
        public function getName()
        {
            return 'magice.service.twig.extension';
        }

        /**
         * set Container
         *
         * @param ContainerBasicInterface $container
         *
         * @return ContainerBasicInterface
         */
        public function setContainer(ContainerBasicInterface $container)
        {
            $this->container = $container;
        }
    }

}