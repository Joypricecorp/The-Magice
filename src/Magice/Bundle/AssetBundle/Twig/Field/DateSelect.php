<?php
/**
 * This file is part of MagiceAssetBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\AssetBundle\Twig\Field;

use Symfony\Component\Form\FormView;
use Magice\Bundle\AssetBundle\Twig\Form;
use Magice\Bundle\AssetBundle\Twig\FieldInterface;

class DateSelect implements FieldInterface
{
    public static function getType()
    {
        return 'dateselect';
    }

    public static function getField(Form $form, FormView $f)
    {
        $f->setRendered(true);

        $r = (object) $f->vars;

        $errors = '';
        if ($r->submitted && !$form->fieldErrorMsgDisabled && !empty($r->errors)) {
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

        /**
         * @var FormView $day
         * @var FormView $month
         * @var FormView $year
         */
        $day   = $f->children['day'];
        $month = $f->children['month'];
        $year  = $f->children['year'];

        return $form->tpl(
            '<div{attr}>',
            '   <label{label_attr}>{label}{separator}</label>',
            '   {day}',
            '   {month}',
            '   {year}',
            '   {errors}',
            '</div>',
            array(
                'label'      => $form->trans($r->label, $r->translation_domain),
                'label_attr' => $form->getAttrs($r->label_attr),
                'separator'  => $form->labelSeparator,
                'attr'       => $attr,
                'day'        => Select::getSelect($form, $day),
                'month'      => Select::getSelect($form, $month),
                'year'       => Select::getSelect($form, $year, true, true),
                'errors'     => $errors
            )
        );
    }
}