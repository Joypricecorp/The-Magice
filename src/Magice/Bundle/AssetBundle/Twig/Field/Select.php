<?php
/**
 * This file is part of MagiceAssetBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\AssetBundle\Twig\Field;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
use Magice\Bundle\AssetBundle\Twig\Form;
use Magice\Bundle\AssetBundle\Twig\FieldInterface;

class Select implements FieldInterface
{
    public static function getType()
    {
        return 'select';
    }

    protected static function renderSelection(Form $form, &$choices, $items, $r, &$selected)
    {
        /**
         * @var ChoiceView $ch
         */
        foreach ($items as $ch) {
            $choices[] = sprintf(
                '<div class="item" data-value="%s">%s</div>',
                $ch->value,
                $form->trans($ch->label, $r->translation_domain)
            );

            if ($ch->value == $r->value) {
                $selected = sprintf('<div class="text">%s</div>', $form->trans($r->empty_value, $r->translation_domain));
            }
        }
    }

    public static function getField(Form $form, FormView $f)
    {
        $f->setRendered(true);

        $form->scriptDeclared['dropdown'] = "$('.ui.dropdown').dropdown();";

        $r = (object) $f->vars;

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

        $error = $r->valid ? null : ' error';

        $attr = $form->getAttrs($r->attr, array('class' => 'field'), array($error));

        // TODO: support multiple
        return $form->tpl(
            '<div{attr}>',
            '   <label{label_attr}>{label}{separator}</label>',
            '   {select}',
            '   {errors}',
            '</div>',
            array(
                'attr'       => $attr,
                'label'      => $form->trans($r->label, $r->translation_domain),
                'label_attr' => $form->getAttrs($r->label_attr),
                'separator'  => $form->labelSeparator,
                'select'     => self::getSelect($form, $f, false),
                'errors'     => $errors
            )
        );
    }

    public static function getSelect(Form $form, FormView $f, $includeAttr = true)
    {
        $f->setRendered(true);

        $form->scriptDeclared['dropdown'] = "$('.ui.dropdown').dropdown();";

        $r = (object) $f->vars;

        $required = true;
        if ($r->required && empty($r->empty_value) && empty($r->empty_value_in_choices)) {
            $required = false;
        }

        $disabled = $form->isAttr($r, 'disabled');

        $opts = '';
        if (!empty($r->_opts)) {
            $opts = (array) $r->_opts;
            $opts = ' ' . implode(' ', $opts);
        }

        if ($includeAttr) {
            $attr = $form->getAttrs(
                $r->attr,
                array(),
                array('ui selection dropdown', $disabled, $opts)
            );
        } else {
            $attr = 'class="ui selection dropdown' . $disabled . $opts . '"';
        }

        $empty_value = '';
        $empty_item  = '';

        if (isset($r->empty_value)) {
            $empty_item = sprintf('<div class="item" data-value="">%s</div>', $form->trans($r->empty_value, $r->translation_domain));

            if ($required && empty($r->value)) {
                $empty_value = sprintf('<div class="text">%s</div>', $form->trans($r->empty_value, $r->translation_domain));
            }
        }

        $choices = array();
        if (!empty($r->preferred_choices)) {
            self::renderSelection($form, $choices, $r->preferred_choices, $r, $empty_value);
        }

        if (!empty($r->choices)) {
            self::renderSelection($form, $choices, $r->choices, $r, $empty_value);
        }

        if (empty($empty_value) && !empty($choices)) {
            $empty_value = str_replace('class="item"', 'class="text"', $choices[0]);
        }

        // sui mast have once select
        if (empty($empty_value)) {
            $empty_value = '<div class="text">...</div>';
        }

        $choices = implode("\n", $choices);

        // TODO: support multiple
        // TODO: change attr['label'] & attr['placeholder'] like TextField
        return $form->tpl(
            '   <div {attr}>',
            '       {empty_value}',
            '       <i class="dropdown icon"></i>',
            '       {asterisk}',
            '       <input name="{name}" id="{id}" type="hidden" value="{value}">',
            '       <div class="menu">',
            '           {empty_item}',
            '           {choices}',
            '       </div>',
            '   </div>',
            array(
                'value'       => $r->value,
                'id'          => $r->id,
                'name'        => $r->full_name,
                'empty_value' => $empty_value,
                'empty_item'  => $empty_item,
                'choices'     => $choices,
                'attr'        => $attr,
                'asterisk'    => $r->required ? '<div class="ui corner label"><i class="icon asterisk"></i></div>' : ''
            )
        );
    }
}