<?php
namespace Magice\Bundle\Symfony\Provider {

    use Magice\Service\Builder;
    use Symfony\Component\DependencyInjection\Reference;

    /**
     * Class Events
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Events
    {
        /**
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * Response: onKernelResponse
         * Id: magice.event.response
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * class: Magice\Bundle\Symfony\Listeners\Response
         *      arguments:
         *          - "@twig"
         *      tags:
         *          - {name: kernel.event_subscribe}
         */
        public static function Response(Builder $builder)
        {
            $builder
                ->register('magice.event.response', 'Magice\Bundle\Symfony\Listeners\Response')
                ->addArgument(new Reference('twig'))
                ->addArgument(new Reference('magice.service.container'))
                ->addTag('kernel.event_subscriber');
        }
    }
}