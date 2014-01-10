<?php
namespace Magice\Mvc\View\Renderer {

    use Magice\Mvc\View\Renderer;
    use Magice\Mvc\View\Asset\Manager;
    use Magice\Mvc\View\Sections;
    use Magice\Mvc\View;

    /**
     * Class Raw
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Raw extends Renderer
    {
        /**
         * @param View    $view
         * @param Manager $asset
         */
        public function __construct(View $view, Manager $asset = null)
        {
            $this->content = $view
                ->getSections()
                ->loadSectionContent(Sections::TAG_SECTION_CONTENT);
        }
    }
}