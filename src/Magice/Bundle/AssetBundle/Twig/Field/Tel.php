<?php
/**
 * This file is part of MagiceAssetBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\AssetBundle\Twig\Field;

use Symfony\Component\Form\FormView;
use Magice\Bundle\AssetBundle\Twig\Form;
use Magice\Bundle\AssetBundle\Twig\FieldInterface;

class Tel extends Text
{
    public static function getType()
    {
        // use https://github.com/misd-service-development/phone-number-bundle
        return 'tel';
    }

    public static function getField(Form $form, FormView $f)
    {
        return parent::getField($form, $f);
    }
}