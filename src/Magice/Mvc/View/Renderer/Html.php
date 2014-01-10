<?php
namespace Magice\Mvc\View\Renderer {

    use Magice\Filesystem\File;
    use Magice\Mvc\View\Asset\Manager;
    use Magice\Mvc\View\Renderer;
    use Magice\Mvc\View\Sections;
    use Magice\Mvc\View;
    use Magice\UI\Tag;
    use Magice\Utils\Arrays;

    class Html extends Renderer
    {
        const LAYOUT_TAG          = 'jp';
        const LAYOUT_SECTION_ATTR = 'data';
        const LAYOUT_SECTION_NAME = 'data-name';

        protected $headers;
        protected $scripts;
        protected $styles;
        protected $content;
        protected $tags = array();

        /**
         * @var \Twig_Environment $engine ;
         */
        protected $engine;


        public function __construct(View $view, Manager $asset = null)
        {
            parent::__construct($view, $asset);

            $this->engine = $view->getEngine();

            $this->parseTagsLayout();
        }

        /**
         * @param null $location
         *
         * @return string
         */
        public function loadLayout($location = null)
        {
            return (new File($location ? : $this->view->layoutLocation))->getContent();
        }

        protected function parseTagsLayout()
        {
            $this->content = $this->engine->render($this->view->layout, $this->view->params());

            if (preg_match_all('|<jp(.*)></jp>|', $this->content, $matchs)) {

                foreach ($matchs[0] as $tag) {
                    $xml  = simplexml_load_string($tag);
                    $attr = $xml->attributes();
                    $type = (string) $attr->{self::LAYOUT_SECTION_ATTR};
                    $name = (string) $attr->{self::LAYOUT_SECTION_NAME};

                    switch ($type) {
                        case Sections::TAG_SECTION:

                            $sectionContent = $this->view->getSections()->loadSectionContent($name);
                            $attrs          = Arrays::fromTag($tag);

                            unset($attrs[self::LAYOUT_SECTION_ATTR]);
                            unset($attrs[self::LAYOUT_SECTION_NAME]);

                            $this->view->loadModulars($name);

                            if (!empty($attrs) && !empty($sectionContent)) {
                                $sectionContent = sprintf(
                                    '<div %s>%s</div>',
                                    Arrays::toAttrs($attrs),
                                    $sectionContent
                                );
                            }

                            $this->content = str_replace(
                                $tag,
                                $sectionContent,
                                $this->content
                            );
                            break;

                        case Sections::TAG_HEADER:
                            $this->content = str_replace(
                                $tag,
                                $this->header(),
                                $this->content
                            );
                            break;

                        case Sections::TAG_SCRIPT:
                            $this->content = str_replace(
                                $tag,
                                $this->footer(),
                                $this->content
                            );
                            break;
                    }
                }
            }
        }

        protected function header()
        {
            $headers = array();

            if ($this->view->title) {
                $headers[] = Tag::title($this->view->title);
            }

            if (!empty($this->view->metas)) {
                foreach ($this->view->metas as $meta) {
                    $headers[] = Tag::meta($meta);
                }
            }

            $headers = array_merge($headers, $this->asset->output(Manager::ADD_TO_HEADER));

            return join("\n", $headers);
        }

        protected function footer()
        {
            return join("\n", $this->asset->output(Manager::ADD_TO_FOOTER));
        }
    }
}