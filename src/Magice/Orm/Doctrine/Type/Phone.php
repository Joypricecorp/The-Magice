<?php
namespace Magice\Orm\Doctrine\Type;

use Misd\PhoneNumberBundle\Doctrine\DBAL\Types\PhoneNumberType;

class Phone extends PhoneNumberType {
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