<?php
namespace Magice\Orm\Doctrine\Types\Phone;

use Misd\PhoneNumberBundle\Doctrine\DBAL\Types\PhoneNumberType;

class Type extends PhoneNumberType
{
    /**
     * Phone number type name.
     */
    const NAME = 'phone';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}