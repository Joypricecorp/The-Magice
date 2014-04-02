<?php
/**
 * This file is part of MagiceAssetBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\AssetBundle\Twig\Field;

use Symfony\Component\Form\FormView;
use Magice\Bundle\AssetBundle\Twig\Form;
use Magice\Bundle\AssetBundle\Twig\FieldInterface;

class Text implements FieldInterface
{
    public static function getType()
    {
        return 'text';
    }

    public static function getField(Form $form, FormView $f)
    {
        $f->setRendered(true);

        $r = (object) $f->vars;

        $opts = '';
        if (!empty($r->_opts)) {
            $opts = (array) $r->_opts;
            $opts = ' ' . implode(' ', $opts);
        }

        $errors = '';
        if (!$form->fieldErrorMsgDisabled && !empty($r->errors)) {
            $r->valid = false;
            $errors   = '<ul class="ui red pointing above ui label">';
            /**
             * @var \Symfony\Component\Form\FormError $e
             */
            foreach ($r->errors as $e) {
                $errors .= sprintf('<li>%s</li>', $form->trans($e->getMessage(), 'validators'));
            }
            $errors .= '</ul>';
        }

        return $form->tpl(
            '<div{attr}>',
            '   <label{label_attr}>{label}{separator}</label>',
            '   <div class="ui left labeled input">',
            '       <input id="{id}" name="{name}" placeholder="{placeholder}" type="{type}" value="{value}"',
            '           {size}{read_only}{required}{disabled}',
            '       >',
            '       {asterisk}',
            '       {errors}',
            '   </div>',
            '</div>',
            array(
                'id'          => $r->id,
                'name'        => $r->full_name,
                'value'       => $r->value,
                'label'       => $form->trans($r->label, $r->translation_domain),
                'label_attr'  => $form->getAttrs($r->label_attr),
                'placeholder' => $form->trans($r->label, $r->translation_domain),
                'separator'   => $form->labelSeparator,
                'size'        => $form->isAttr($r, 'size', false),
                'read_only'   => $form->isAttr($r, 'read_only'),
                'required'    => $form->isAttr($r, 'required'),
                'disabled'    => $form->isAttr($r, 'disabled'),
                // if render form widget attr will apply to cover tag
                'attr'        => $form->getAttrs($r->attr, array('class' => 'field'), array($r->valid ? null : 'error', $opts)),
                'type'        => static::getType(),
                'asterisk'    => $r->required ? '<div class="ui corner label"><i class="icon asterisk"></i></div>' : '',
                'errors'      => $errors
            )
        );
    }
}