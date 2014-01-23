<?php

/*
 * This file is part of the The Magice Project.
 *
 * @copyright   2012-2014 ツ Joyprice corporation Ltd.
 * @license     http://www.joyprice.org/license
 * @link        http://www.joyprice.org/themagice
 */

namespace Magice\Bundle\Symfony\Compilers {

    use Symfony\Component\DependencyInjection\ContainerBuilder,
        Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

    class Security implements CompilerPassInterface
    {
        public function process(ContainerBuilder $builder)
        {
            $builder->setParameter(
                'security.authentication.form_entry_point.class',
                'Magice\Security\EntryPoint\FormAuthenticationEntryPoint'
            );
        }
    }
}