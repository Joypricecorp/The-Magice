<?php
namespace Magice\Form;

use Magice\Exception\Exception;
use Magice\Utils\String;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class Type extends AbstractType
{
    public function setDefaults(OptionsResolverInterface $resolver, $options)
    {
        $maps = array();

        if (is_string($options)) {
            $options = array('data_class' => $options);
        }

        if (!is_array($options)) {
            throw new Exception("The default options must be array.");
        }

        if (
            (!isset($options['error_mapping']))
            && (isset($options['data_class']) && ($class = $options['data_class']))
        ) {
            // if you are named form's fields not eque with table's fields (entity,class data)
            // this will help you auto mapping fields between class and form
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