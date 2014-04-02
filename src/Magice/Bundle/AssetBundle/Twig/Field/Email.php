<?php
/**
 * This file is part of MagiceAssetBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\AssetBundle\Twig\Field;

use Symfony\Component\Form\FormView;
use Magice\Bundle\AssetBundle\Twig\Form;
use Magice\Bundle\AssetBundle\Twig\FieldInterface;

class Email extends Text
{
    public static function getType()
    {
        return 'email';
    }

    public static function getField(Form $form, FormView $f)
    {
        return parent::getField($form, $f);
    }
}