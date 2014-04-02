<?php
/**
 * This file is part of MagiceAssetBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\AssetBundle\Twig;

use Symfony\Component\Form\FormView;

interface FieldInterface
{
    public static function getField(Form $form, FormView $formView);

    public static function getType();
}