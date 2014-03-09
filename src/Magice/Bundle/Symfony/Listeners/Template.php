<?php
namespace Magice\Bundle\Symfony\Listeners {

    use Magice\Bundle\Symfony\Services\TemplateReference;
    use Symfony\Component\HttpFoundation\JsonResponse,
        Symfony\Component\HttpFoundation\StreamedResponse,
        Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent,
        Symfony\Component\HttpKernel\Event\FilterControllerEvent;
    use Sensio\Bundle\FrameworkExtraBundle\EventListener\TemplateListener;

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Template extends TemplateListener
    {
        /**
         * Guesses the template name to render and its variables and adds them to
         * the request object.
         *
         * @param FilterControllerEvent $event A FilterControllerEvent instance
         */
        public function onKernelController(FilterControllerEvent $event)
        {
            if (!is_array($controller = $event->getController())) {
                return;
            }

            $request = $event->getRequest();

            /**
             * @var \Magice\Bundle\Symfony\AutoConfiguration\Template $configuration
             */
            if (!$configuration = $request->attributes->get('_template')) {
                return;
            }

            $formatDefault = $configuration instanceof TemplateReference ? $configuration->getFormat() : null;
            $format        = $request->query->get('_format', $formatDefault);
            $scoped = $formatDefault = $configuration instanceof TemplateReference ? $configuration->getScoped() : null;
            $formats = $formatDefault = $configuration instanceof TemplateReference ? $configuration->getFormats() : null;

            // Ensure output format & _format is currect
            $request->setRequestFormat($format);
            $request->query->set('_format', $format);

            if (!$configuration->getTemplate()) {
                $guesser = $this->container->get('sensio_framework_extra.view.guesser');
                $configuration->setTemplate(
                    $guesser->guessTemplateName(
                        $controller,
                        $format,
                        $configuration->getEngine(),
                        $scoped,
                        $formats
                    )
                );
            }

            $request->attributes->set('_template', $configuration->getTemplate());
            $request->attributes->set('_template_vars', $configuration->getVars());
            $request->attributes->set('_template_streamable', $configuration->isStreamable());

            // all controller method arguments
            if (!$configuration->getVars()) {
                $r = new \ReflectionObject($controller[0]);

                $vars = array();
                foreach ($r->getMethod($controller[1])->getParameters() as $param) {
                    $vars[] = $param->getName();
                }

                $request->attributes->set('_template_default_vars', $vars);
            }
        }

        /**
         * @override
         * Renders the template and initializes a new response object with the
         * rendered template content.
         *
         * @param GetResponseForControllerResultEvent $event A GetResponseForControllerResultEvent instance
         *
         * @return mixed
         * @throws \RuntimeException
         */
        public function onKernelView(GetResponseForControllerResultEvent $event)
        {
            $request    = $event->getRequest();
            $parameters = $event->getControllerResult();
            $templating = $this->container->get('templating');

            if (null === $parameters) {
                if (!$vars = $request->attributes->get('_template_vars')) {
                    if (!$vars = $request->attributes->get('_template_default_vars')) {
                        return null;
                    }
                }

                $parameters = array();
                foreach ($vars as $var) {
                    $parameters[$var] = $request->attributes->get($var);
                }
            }

            /**
             * @var TemplateReference $template
             */
            $template = $request->attributes->get('_template');

            if ($template) {
                $format  = $template->get('format');
                $formats = (array) $template->get('formats');

                if (!empty($formats) && !in_array($format, $formats)) {
                    throw new \RuntimeException(sprintf(
                        'Not support output format ("%s"). Output formats are supported: [%s].',
                        $format,
                        implode(',', $formats)
                    ));
                }
            } else {
                $format = $request->getRequestFormat();
            }

            // Boon #1
            switch ($format) {

                case 'json':
                    $event->setResponse(new JsonResponse($parameters));

                    return null;

                case 'jsonp':
                    $response = new JsonResponse($parameters);
                    $response->setCallback(
                        $this->container->getParameter(
                            $request->query->has('_cdata')
                                ? 'magice.output.cdata_callback'
                                : 'magice.output.jsonp_callback'
                        )
                    );

                    $event->setResponse($response);

                    return null;
            }
            // Boon #1-end

            if (!is_array($parameters)) {
                return $parameters;
            }

            if (!$template) {
                return $parameters;
            }

            if (!$request->attributes->get('_template_streamable')) {

                // BOON #2 add this to tell templating know xx.rawhtml.twig as xx.html.twig
                if (in_array($template->get('format'), array('raw', 'rawhtml'))) {
                    $template->set('format', 'html');
                }
                // BOON #2-end

                $event->setResponse($templating->renderResponse($template, $parameters));
            } else {
                $callback = function () use ($templating, $template, $parameters) {
                    return $templating->stream($template, $parameters);
                };

                $event->setResponse(new StreamedResponse($callback));
            }

            return null;
        }
    }
}