<?php
/**
 * This file is part of MagiceAssetBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\AssetBundle\Twig\Field;

use Magice\Utils\Arrays;
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

    public static function getField(Form $form, FormView $formView)
    {
        $formView->setRendered();

        $choices = self::choices($form, $formView);
        $errors  = $form->getFieldErrors($formView);
        $attrs   = Attributes::create($form, $formView);

        if ($form->useNgModel) {
            $attrs->input['ng-change'] = $formView->vars['name'] . '_change()';
        }

        $return = $form->tpl(
            '<div {attr_cover}>',
            '   <label {attr_label} for="{id}">{label}{separator}</label>',
            '   <div {attr_field}>',
            '       <select {attr_input}>{choices}</select>',
            '       {options}',
            '       {errors}',
            '   </div>',
            '</div>',
            array(
                'attr_cover' => Arrays::toAttrs($attrs->cover),
                'attr_label' => Arrays::toAttrs($attrs->label),
                'attr_field' => Arrays::toAttrs($attrs->field),
                'attr_input' => Arrays::toAttrs($attrs->input),
                'choices'    => $choices,
                'id'         => $attrs->input['id'],
                'label'      => $formView->vars['label'],
                'separator'  => $form->labelSeparator,
                'options'    => implode("\n", $attrs->option),
                'errors'     => $errors
            )
        );

        return $return;
    }

    /**
     * @param Form     $form
     * @param FormView $formView
     *
     * @return string
     */
    private static function choices(Form $form, FormView $formView)
    {
        if ($formView->vars['required']
            and empty($formView->vars['empty_value'])
            and empty($formView->vars['empty_value_in_choices'])
            and empty($formView->vars['multiple'])
        ) {
            $formView->vars['required'] = false;
        }

        $choices = '';

        if (!empty($formView->vars['empty_value'])) {
            $choices .= sprintf(
                '<option value=""%s>%s</option>',
                $formView->vars['required'] && empty($formView->vars['value']) ? ' selected="selected"' : '',
                $form->trans($formView->vars['empty_value'], $formView->vars['translation_domain'])
            );
        }

        if (!empty($formView->vars['preferred_choices'])) {
            $choices .= self::options($form, $formView, $formView->vars['preferred_choices']);
        }

        $choices .= self::options($form, $formView, $formView->vars['choices']);

        return $choices;
    }

    /**
     * @param Form                                                     $form
     * @param FormView                                                 $formView
     * @param \Symfony\Component\Form\Extension\Core\View\ChoiceView[] $choices
     *
     * @return string
     */
    private static function options(Form $form, FormView $formView, array $choices)
    {
        $options = '';
        foreach ($choices as $group => $ch) {
            if ($ch instanceof \Traversable) {
                $options .= sprintf(
                    '<optgroup label="%s">%s</optgroup>',
                    $form->trans($group, $formView->vars['translation_domain']),
                    self::options($form, $formView, $ch)
                );
            } else {
                $options .= sprintf(
                    '<option value="%s"%s>%s</option>',
                    $ch->value,
                    $ch->value == $formView->vars['value'] ? ' selected="selected"' : '',
                    $form->trans($ch->label, $formView->vars['translation_domain'])
                );
            }
        }

        return $options;
    }
}