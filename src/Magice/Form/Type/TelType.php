<?php
namespace Magice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TelType extends AbstractType
{
    public function getName()
    {
        return 'tel';
    }

    public function getParent()
    {
        return 'text';
    }
}