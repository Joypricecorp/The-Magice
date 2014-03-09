<?php
namespace Magice\Bundle\Symfony\Listeners {

    use Magice\Service\Container;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
    use Symfony\Component\HttpKernel\KernelEvents;
    use Symfony\Component\HttpKernel\HttpKernelInterface;

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Response implements EventSubscriberInterface
    {
        /**
         * @var \Twig_Environment $twig
         */
        protected $twig;

        /**
         * @var Container $container
         */
        protected $container;

        /**
         * @param \Twig_Environment $twig
         * @param Container         $container
         */
        public function __construct(\Twig_Environment $twig, Container $container)
        {
            $this->twig      = $twig;
            $this->container = $container;
        }

        /**
         * @param FilterResponseEvent $event
         */
        public function onKernelResponse(FilterResponseEvent $event)
        {
            /**
             * @var \Magice\Mvc\View $view
             */
            $view = $this->container->get(':mvc.view');

            // TODO: check support _controller class inteadof use JP
            if (strpos($event->getRequest()->attributes->get('_controller'), 'JP') === 0
                && !($event->getRequest()->attributes->get('_modular'))
                && $event->getRequestType() == HttpKernelInterface::MASTER_REQUEST
            ) {
                $view->prepareResponse($event->getResponse(), $event->getRequest()->getRequestFormat());
            }
        }

        public static function getSubscribedEvents()
        {
            return array(
                KernelEvents::RESPONSE => array('onKernelResponse')
            );
        }
    }
}