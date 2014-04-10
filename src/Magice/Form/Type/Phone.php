<?php
namespace Magice\Form\Type;

use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;

class Phone extends PhoneNumberType
{
    public function getName()
    {
        return 'phone';
    }
}