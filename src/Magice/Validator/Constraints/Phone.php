<?php
namespace Magice\Validator\Constraints;

use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;

class Phone extends PhoneNumber
{
    public function validatedBy()
    {
        return 'Phone';
    }
}