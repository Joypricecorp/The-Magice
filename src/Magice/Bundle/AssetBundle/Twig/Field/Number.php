<?php
/**
 * This file is part of MagiceAssetBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\AssetBundle\Twig\Field;

use Symfony\Component\Form\FormView;
use Magice\Bundle\AssetBundle\Twig\Form;
use Magice\Bundle\AssetBundle\Twig\FieldInterface;

class Number extends Text
{
    public static function getType()
    {
        return 'number';
    }

    public static function getField(Form $form, FormView $f)
    {
        return parent::getField($form, $f);
    }
}