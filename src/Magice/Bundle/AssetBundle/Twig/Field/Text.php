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

        $opts          = '';
        $pre_icon_flag = '';
        $pre_icon_tag  = '';

        if (!empty($r->_opts)) {
            $opts = (array) $r->_opts;

            if (isset($opts['icon'])) {
                $pre_icon_flag = ' icon';
                $pre_icon_tag  = sprintf('<i class="%s icon"></i>', $opts['icon']);
                unset($opts['icon']);
            }

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

        if (isset($r->attr['label'])) {
            $r->label = $r->attr['label'];
            unset($r->attr['label']);
        }

        $placeholder = $r->label;
        if (isset($r->attr['placeholder'])) {
            $placeholder = $r->attr['placeholder'];
            unset($r->attr['placeholder']);
        }

        $pattern = null;
        if (isset($r->attr['pattern'])) {
            $pattern = ' pattern="' . $r->attr['pattern'] . '"';
            unset($r->attr['pattern']);
        }

        $auto = 'on';
        if (($type = static::getType()) == 'text-password') {
            $type = 'text';
            $auto = 'off';
        }

        return $form->tpl(
            '<div{attr}>',
            '   <label{label_attr}>{label}{separator}</label>',
            '   <div class="ui left labeled input{pre_icon_flag}">',
            '       <input id="{id}" name="{name}" placeholder="{placeholder}" type="{type}" value="{value}" autocomplete="{autocomplete}"{size}{read_only}{required}{disabled}{pattern}>',
            '       {pre_icon_tag}',
            '       {asterisk}',
            '       {errors}',
            '   </div>',
            '</div>',
            array(
                'id'            => $r->id,
                'name'          => $r->full_name,
                'value'         => $r->value,
                'label'         => $r->label,
                'label_attr'    => $form->getAttrs($r->label_attr),
                'placeholder'   => $placeholder ? : '',
                'separator'     => $form->labelSeparator,
                'size'          => $form->isAttr($r, 'size', false),
                'read_only'     => $form->isAttr($r, 'read_only'),
                'required'      => $form->isAttr($r, 'required'),
                'disabled'      => $form->isAttr($r, 'disabled'),
                // if render form widget attr will apply to cover tag
                'attr'          => $form->getAttrs($r->attr, array('class' => 'field'), array($r->valid ? null : 'error', $opts)),
                'type'          => $type,
                'autocomplete'  => $auto,
                'pattern'       => $pattern,
                'pre_icon_flag' => $pre_icon_flag,
                'pre_icon_tag'  => $pre_icon_tag,
                'asterisk'      => $r->required ? '<div class="ui corner label"><i class="icon asterisk"></i></div>' : '',
                'errors'        => $errors
            )
        );
    }
}