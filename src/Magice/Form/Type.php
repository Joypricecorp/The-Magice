<?php
namespace Magice\Form;

use Magice\Utils\String;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class Type extends AbstractType
{
    public function setDefaults(OptionsResolverInterface $resolver, array $options)
    {
        $maps = array();

        if (
            (isset($options['auto_error_mapping']) && $options['auto_error_mapping'] !== false)
            && (!isset($options['error_mapping']))
            && isset($options['data_class']) && ($class = $options['data_class'])
        ) {
            foreach ((new \ReflectionClass($class))->getProperties() as $p) {
                $maps[$p->getName()] = String::underscore($p->getName());
            }
        }

        $resolver->setDefaults(
            array_replace(
                array(
                    'data_class'        => isset($class) ? $class : null,
                    'csrf_protection'   => true,
                    'csrf_field_name'   => '_token',
                    // a unique key to help generate the secret token
                    'intention'         => $this->getName(),
                    'validation_groups' => null, //array('registration'),
                    'error_bubbling'    => true,
                    'error_mapping'     => $maps
                ),
                $options
            )
        );
    }
}