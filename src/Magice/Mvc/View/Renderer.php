<?php
namespace Magice\Mvc\View {

    use Magice\Mvc\View;
    use Magice\Mvc\View\Asset\Manager;

    abstract class Renderer
    {
        /**
         * @var View $view
         */
        protected $view;

        /**
         * @var Manager $asset
         */
        protected $asset;

        protected $content;

        public function __construct(View $view, Manager $asset = null)
        {
            $this->view  = $view;
            $this->asset = $asset ?: $view->asset;
        }

        public function render()
        {
            return $this->content;
        }
    }
}