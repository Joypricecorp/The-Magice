<?php
namespace Magice\Mvc {

    use Magice\Mvc\View\Asset\Manager;
    use Magice\Mvc\View\Renderer;
    use Magice\Mvc\View\Section;
    use Magice\Mvc\View\Sections;
    use Magice\Service\ContainerBasicInterface;
    use Magice\Service\ContainerAwareInterface;
    use Magice\Serviced\Param;
    use Magice\Utils\String;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpKernel\HttpKernelInterface;

    /**
     * Class View
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class View implements ContainerAwareInterface
    {
        /**
         * @var Manager $asset
         */
        public $asset;
        public $layout;
        public $layoutLocation;
        public $format;
        public $title;
        public $language;
        public $charset = 'UTF-8';
        public $metas = array();
        public $engine = 'twig';
        public $scoped = '';

        /**
         * @var ContainerBasicInterface $container
         */
        protected $container;
        protected $sections;
        protected $modules = array();
        protected $cssScope = array();
        protected $ingoreModular = false;


        public function __construct($layout, Manager $asset = null)
        {
            $this->layout = $layout;
            $this->asset  = $asset;

            $this->sections = new Sections();
        }

        protected function initLayoutLocation()
        {
            $src = Param::get('kernel.src');

            // add new namespace for twig-loader
            if (strpos($this->layout, '@') === 0) {
                $locate = str_replace('@', '', $this->layout);

                $this->layoutLocation = sprintf('%s/%s', $src, $locate);

                list($namespace) = explode('/', $locate, 2);
                $this->container->get('twig.loader')->addPath($src . '/' . $namespace, $namespace);
            } else {
                $this->layoutLocation = $this->layout;
            }
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

        /**
         * @param Manager $am
         */
        public function setAsset(Manager $am)
        {
            $this->asset = $am;
        }

        /**
         * @param array $modules
         */
        public function registerModulars(array $modules)
        {
            $this->modules = $modules;
        }

        public function loadModulars($forSection = null)
        {
            if ($this->ingoreModular) {
                return;
            }

            foreach ($this->modules as $section => $mods) {
                foreach ($mods as $mod) {

                    if (!empty($forSection) && $section != $forSection) {
                        continue;
                    }

                    $this->section($section, array(
                        'name'     => $mod['name'],
                        'position' => isset($mod['position']) ? : 0,
                        'content'  => $this->modular($mod['target'])
                    ));
                }
            }
        }

        public function addModular($section, $module)
        {
            if (is_string($module)) {
                $module = array(
                    'name'   => String::underscore($module),
                    'target' => $module
                );
            }

            if (!array_key_exists('name', $module)) {
                $module['name'] = String::underscore($module['target']);
            }

            if (!array_key_exists($section, $this->modules)) {
                $this->modules[$section] = array();
            }

            $this->modules[$section][$module['name']] = $module;

            return $this;
        }

        public function ingoreModular($flag)
        {
            $this->ingoreModular = $flag;
        }

        public function removeModular($name, $section = '')
        {
            if ($section && array_key_exists($section, $this->modules)) {
                unset($this->modules[$section][$name]);
            } else {
                foreach ($this->modules as $section => $mods) {
                    foreach ($mods as $key => $mod) {
                        if ($key == $name) {
                            unset($this->modules[$section][$key]);
                            break;
                        }
                    }
                }
            }
        }

        public function getEngine()
        {
            return $this->container->get($this->engine);
        }

        /**
         * @return Sections
         */
        public function getSections()
        {
            return $this->sections;
        }

        public function prepareResponse(Response $response, $format)
        {
            $this->initLayoutLocation();

            $this->format = $format;

            $this->content($response->getContent());

            switch (strtolower($format)) {
                case 'json':
                case 'jsonp':
                    // Nothing to do
                    break;

                case 'raw':
                    $response->setContent((new Renderer\Raw($this))->render());
                    break;

                default:

                    /**
                     * @var \Symfony\Component\Templating\TemplateReferenceInterface $template
                     */
                    if (!$template = $this->container->get('request')->get('_template')) {
                        break;
                    }

                    // add css page scope (get @Auto\Template(scoped="css-name"))
                    if ($scoped = $template->get('scoped')) {
                        $this->scoped($scoped);
                    }

                    $response->setContent((new Renderer\Html($this))->render());
                    break;
            }
        }

        /**
         * Forwards the request to another controller.
         *
         * @param string $controller The controller name (a string like BlogBundle:Post:index)
         * @param array  $path       An array of path parameters
         * @param array  $query      An array of query parameters
         *
         * @return Response A Response instance
         */
        public function modular($controller, array $path = array(), array $query = array())
        {
            $path['_controller'] = $controller;
            $path['_modular']    = 1;

            $subRequest = $this->container->get('request')->duplicate($query, null, $path);

            return $this
                ->container
                ->get('http_kernel')
                ->handle($subRequest, HttpKernelInterface::SUB_REQUEST)
                ->getContent();
        }

        public function escape($output)
        {
            return $output;
        }

        /**
         * Get/Set section by name
         *
         * @param string $name
         * @param array  $section
         *
         * @return mixed|null
         */
        public function section($name, array $section = null)
        {
            if ($section === null) {
                return $this->sections->get($name);
            }

            $this->sections->set($name, new Section($section));

            return null;
        }

        public function resetSection()
        {

        }

        /**
         * Set content section
         *
         * @param string|array $content
         */
        public function content($content = null)
        {
            if (!is_array($content)) {
                $content = array(
                    'content' => $content
                );
            }

            if (empty($content['name'])) {
                $content['name'] = 'default';
            }

            if (empty($content['name'])) {
                $content['position'] = 0;
            }

            $this->section(Sections::TAG_SECTION_CONTENT, $content);
        }

        /**
         * Set document title
         *
         * @param string $title
         */
        public function title($title)
        {
            $this->title = $title;
        }

        /**
         * Set language code
         *
         * @param string $code
         */
        public function language($code)
        {
            $this->language = $code;
        }

        /**
         * Set charset
         *
         * @param string $charset
         */
        public function charset($charset)
        {
            $this->charset = $charset;
        }

        /**
         * Set a html meta tag
         *
         * @param    string|array $name    multiple inputs or name/http-equiv value
         * @param    string       $content value
         * @param    string       $type    name or http-equiv
         */
        public function meta($name, $content = '', $type = 'name')
        {
            if (!is_array($name)) {
                $this->metas[] = array($type => $name, 'content' => $content);
            } elseif (is_array($name)) {
                $this->metas[] = $name;
            }
        }

        public function scoped($css)
        {
            $this->cssScope[$css] = $css;
            $this->scoped         = implode(' ', $this->cssScope);
        }

        public function removeScoped($css)
        {
            if (array_key_exists($css, $this->cssScope)) {
                unset($this->cssScope[$css]);
                $this->scoped = implode(' ', $this->cssScope);
            }
        }

        public function params()
        {
            return get_object_vars($this);
        }
    }
}