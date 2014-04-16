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

    public static function getField(Form $form, FormView $formView)
    {
        $formView->setRendered(true);
        $r = (object) $formView->vars;

        $icon = '';
        $cls  = array();

        self::$type = $r->attr['type'];

        if (!empty($r->attr['o:labeled'])) {
            $cls[] = 'labeled';
            $cls[] = $r->attr['o:labeled']; // left,right is values
            unset($r->attr['o:labeled']);
        }

        if (!empty($r->attr['o:icon'])) {
            $cls[] = 'icon';
            $icon  = sprintf('<i class="%s icon"></i>', $r->attr['o:icon']);
            unset($r->attr['o:icon']);
        }

        if (!empty($r->attr['o:style'])) {
            $cls[] = $r->attr['o:style'];
            unset($r->attr['o:style']);
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
                            'class' => 'ui button',
                            'id'    => $r->id,
                            'name'  => $r->full_name,
                            'value' => $r->value,
                            'type'  => static::getType()
                        ),
                        $cls
                    ),
                'type'  => static::getType()
            )
        );
    }
}