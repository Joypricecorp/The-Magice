<?php
namespace Magice\Bundle\Symfony\Services {

    use Symfony\Component\HttpKernel\KernelInterface;
    use Symfony\Component\HttpKernel\Bundle\BundleInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Doctrine\Common\Util\ClassUtils;

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class TemplateGuesser
    {
        /**
         * @var KernelInterface
         */
        protected $kernel;

        /**
         * Constructor.
         *
         * @param KernelInterface $kernel A KernelInterface instance
         */
        public function __construct(KernelInterface $kernel)
        {
            $this->kernel = $kernel;
        }

        /**
         * Guesses and returns the template name to render based on the controller
         * and action names.
         *
         * @param  array  $controller An array storing the controller object and action method
         * @param  string $format     Output format
         * @param  string $engine
         * @param  string $cssScope
         * @param  array  $formats
         *
         * @return TemplateReference         template reference
         * @throws \InvalidArgumentException
         */
        public function guessTemplateName($controller, $format, $engine = 'twig', $cssScope = '', $formats = array())
        {
            $className = class_exists('Doctrine\Common\Util\ClassUtils') ? ClassUtils::getClass($controller[0]) : get_class($controller[0]);

            if (!preg_match('/Controller\\\(.+)Controller$/', $className, $matchController)) {

                if ($className == 'JP\Base\Controller') {
                    $matchController[1] = '';
                } else {
                    throw new \InvalidArgumentException(sprintf(
                        'The "%s" class does not look like a controller class (it must be in a "Controller" sub-namespace and the class name must end with "Controller")',
                        get_class($controller[0])
                    ));
                }
            }
            if (!preg_match('/^(.+)Action$/', $controller[1], $matchAction)) {
                throw new \InvalidArgumentException(sprintf(
                    'The "%s" method does not look like an action method (it does not end with Action)',
                    $controller[1]
                ));
            }

            $bundle = $this->getBundleForClass($className);
            while ($bundleName = $bundle->getName()) {
                if (null === $parentBundleName = $bundle->getParent()) {
                    $bundleName = $bundle->getName();

                    break;
                }

                $bundles = $this->kernel->getBundle($parentBundleName, false);
                $bundle  = array_pop($bundles);
            }

            return new TemplateReference(
                $bundleName,
                $matchController[1],
                $matchAction[1],
                $format,
                $engine,
                $cssScope,
                $formats
            );
        }

        /**
         * Returns the Bundle instance in which the given class name is located.
         *
         * @param  string $class A fully qualified controller class name
         *
         * @return BundleInterface
         * @throws \InvalidArgumentException
         */
        protected function getBundleForClass($class)
        {
            $class   = new \ReflectionClass($class);
            $bundles = $this->kernel->getBundles();

            do {
                $namespace = $class->getNamespaceName();
                foreach ($bundles as $bundle) {
                    if (0 === strpos($namespace, $bundle->getNamespace())) {
                        return $bundle;
                    }
                }
                $reflectionClass = $class->getParentClass();
            } while ($reflectionClass);

            throw new \InvalidArgumentException(sprintf('The "%s" class does not belong to a registered bundle.', $class->getName()));
        }
    }
}