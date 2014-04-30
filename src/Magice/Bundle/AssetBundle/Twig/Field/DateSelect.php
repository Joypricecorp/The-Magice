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

class DateSelect implements FieldInterface
{
    public static function getType()
    {
        return 'dateselect';
    }

    public static function getField(Form $form, FormView $formView)
    {
        $formView->setRendered();

        $errors = $form->getFieldErrors($formView);
        $attrs  = Attributes::create($form, $formView);

        $attrs->cover['class'] .= ' date-select';

        /**
         * @var FormView $day
         * @var FormView $month
         * @var FormView $year
         */
        $day   = $formView->children['day'];
        $month = $formView->children['month'];
        $year  = $formView->children['year'];

        // use for ng-model
        $day->vars['ng_name']   = $formView->vars['name'] . '.' . $day->vars['name'];
        $month->vars['ng_name'] = $formView->vars['name'] . '.' . $month->vars['name'];
        $year->vars['ng_name']  = $formView->vars['name'] . '.' . $year->vars['name'];

        // use for ng-change
        $day->vars['custom_name']   = $formView->vars['name'] . '_' . $day->vars['name'];
        $month->vars['custom_name'] = $formView->vars['name'] . '_' . $month->vars['name'];
        $year->vars['custom_name']  = $formView->vars['name'] . '_' . $year->vars['name'];

        $year->vars['is_year'] = true;
        $day->vars['attr']     = $month->vars['attr'] = $year->vars['attr'] = $formView->vars['attr'];


        return $form->tpl(
            '<div {attr_cover}>',
            '   <label {attr_label}>{label}{separator}</label>',
            '   <div class="three fields">',
            '       {day}',
            '       {month}',
            '       {year}',
            '   </div>',
            '   {errors}',
            '</div>',
            array(
                'attr_cover' => Arrays::toAttrs($attrs->cover),
                'attr_label' => Arrays::toAttrs($attrs->label),
                'separator'  => $form->labelSeparator,
                'label'      => $formView->vars['label'],
                'errors'     => $errors,
                'day'        => Select::getField($form, $day),
                'month'      => Select::getField($form, $month),
                'year'       => Select::getField($form, $year),
            )
        );
    }
}