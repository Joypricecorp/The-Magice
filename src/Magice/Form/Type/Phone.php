<?php
namespace Magice\Form\Type;

use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Magice\Form\DataTransformer\PhoneNumberToStringTransformer;

class Phone extends PhoneNumberType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(
            new PhoneNumberToStringTransformer($options['default_region'], $options['format'])
        );
    }

    public function getName()
    {
        return 'phone';
    }
}