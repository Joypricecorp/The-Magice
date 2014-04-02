<?php
/**
 * This file is part of MagiceAssetBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\AssetBundle\Twig\Field;

use Symfony\Component\Form\FormView;
use Magice\Bundle\AssetBundle\Twig\Form;
use Magice\Bundle\AssetBundle\Twig\FieldInterface;

class Button implements FieldInterface
{
    protected static $type;

    public static function getType()
    {
        return static::$type;
    }

    public static function getField(Form $form, FormView $f)
    {
        $f->setRendered(true);
        $r = (object) $f->vars;

        $icon = '';
        $cls  = array();

        self::$type = $r->_opts['type'];

        if (!empty($r->_opts['labeled'])) {
            $cls[] = 'labeled';
            if (isset($r->_opts['labeled']['right'])) {
                $cls[] = 'right';
            }
        }

        if (!empty($r->_opts['icon'])) {
            $cls[] = 'icon';
            $icon  = sprintf('<i class="%s icon"></i>', $r->_opts['icon']);
        }

        if (!empty($r->_opts['style'])) {
            $cls[] = $r->_opts['style'];
        }

        if ($r->disabled) {
            $r->attr['disabled'] = 'disabled';
        }

        return $form->tpl(
            '<button{attr}>{icon} {label}</button>',
            array(
                'icon'  => $icon,
                'label' => $form->trans($r->label, $r->translation_domain),
                'attr'  => $form->getAttrs(
                        $r->attr,
                        array(
                            'class'    => 'ui button',
                            'id'       => $r->id,
                            'name'     => $r->full_name,
                            'value'    => $r->value,
                            'type'     => static::getType()
                        ),
                        $cls
                    ),
                'type'  => static::getType()
            )
        );
    }
}