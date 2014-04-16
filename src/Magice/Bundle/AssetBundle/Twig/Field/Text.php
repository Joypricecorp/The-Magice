<?php
/**
 * This file is part of MagiceAssetBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\AssetBundle\Twig\Field;

use Magice\Utils\Arrays;
use Symfony\Component\Form\FormView;
use Magice\Bundle\AssetBundle\Twig\Form;
use Magice\Bundle\AssetBundle\Twig\FieldInterface;

class Text implements FieldInterface
{
    public static function getType()
    {
        return 'text';
    }

    public static function getField(Form $form, FormView $formView)
    {
        $formView->setRendered();

        $errors = '';
        if ($formView->vars['submitted'] && !$form->fieldErrorMsgDisabled && !empty($formView->vars['errors'])) {
            $formView->vars['valid'] = false;

            $errors = '<ul class="ui red pointing above ui label">';

            /**
             * @var \Symfony\Component\Form\FormError $e
             */
            foreach ($formView->vars['errors'] as $e) {
                $errors .= sprintf('<li>%s</li>', $form->trans($e->getMessage(), 'validators'));
            }

            $errors .= '</ul>';
        }

        $attr = Attributes::create($form, $formView);

        $type = $attr->input['type'] = static::getType();

        if ($type === 'text-password') {
            $attr->input['type'] = 'text';
        }

        $return = $form->tpl(
            '<div {attr_cover}>',
            '   <label {attr_label}>{label}{separator}</label>',
            '   <div {attr_field}>',
            '       <input {attr_input}>',
            '       {options}',
            '       {errors}',
            '   </div>',
            '</div>',
            array(
                'label'      => $formView->vars['label'],
                'separator'  => $form->labelSeparator,
                'attr_cover' => Arrays::toAttrs($attr->cover),
                'attr_label' => Arrays::toAttrs($attr->label),
                'attr_field' => Arrays::toAttrs($attr->field),
                'attr_input' => Arrays::toAttrs($attr->input),
                'options'    => implode("\n", $attr->option),
                'errors'     => $errors
            )
        );

        return $return;
    }
}