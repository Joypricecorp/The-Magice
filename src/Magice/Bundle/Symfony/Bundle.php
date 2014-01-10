<?php
namespace Magice\Bundle\Symfony {

    use Magice,
        Magice\Service\Builder,
        Magice\Service\Container,
        Magice\Registry\RegistryRefererReadOnly;
    use Symfony\Component\DependencyInjection\ContainerBuilder,
        Symfony\Component\HttpKernel\Bundle\Bundle as SymfonyBundle;

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Bundle extends SymfonyBundle
    {
        protected $name = 'Magice';

        function boot()
        {
            // setup container on start time
            Magice::boot($this->container->get('magice.service.container'));
        }

        /**
         * @param ContainerBuilder $builder
         *
         * @NOTE THIS'S METHOD RUN ONLY ON BUILD THE SYSTEM PROCESS.
         */
        public function build(ContainerBuilder $builder)
        {
            parent::build($builder);

            // setup builder on build time
            Magice::build($builder);

            $builder->registerExtension(new Extension());            
            $builder->addCompilerPass(new Compilers\Template());
            $builder->addCompilerPass(new Compilers\Twig());
            $builder->addCompilerPass(new Compilers\Session());
            // Not Now! We cannot inject service container to Enitity
            //$builder->addCompilerPass(new Compilers\Doctrine());

        }
    }
}