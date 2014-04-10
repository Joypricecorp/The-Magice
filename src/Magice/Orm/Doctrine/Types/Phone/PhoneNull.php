<?php
namespace Magice\Orm\Doctrine\Types\Phone;

use Magice\Object\NullAbstract;

/**
 * @method null hasCountryCode()
 * @method null getCountryCode()
 * @method null hasNationalNumber()
 * @method null getNationalNumber()
 * @method null isItalianLeadingZero()
 * @method null hasRawInput()
 * @method null getRawInput()
 * @method null hasCountryCodeSource()
 * @method null getCountryCodeSource()
 * @method null hasPreferredDomesticCarrierCode()
 * @method null getPreferredDomesticCarrierCode()
 * @method null hasNumberOfLeadingZeros()
 * @method null getNumberOfLeadingZeros()
 */
class PhoneNull extends NullAbstract
{
    public function __toString()
    {
        return '';
    }
}