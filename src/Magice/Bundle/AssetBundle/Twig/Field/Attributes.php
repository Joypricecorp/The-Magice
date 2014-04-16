<?php
/**
 * This file is part of MagiceAssetBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\AssetBundle\Twig\Field;

use Doctrine\Common\Collections\ArrayCollection;
use Magice\Bundle\AssetBundle\Twig\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Attributes extends ArrayCollection
{
    private $_elements;

    public function getElements($n = null)
    {
        if ($this->_elements) {
            return $n ? isset($this->_elements[$n]) ? $this->_elements[$n] : array() : array();
        }

        /**
         * c: top cover
         * l: label
         * f: field cover
         * o: option
         * *: input
         */
        foreach ($this->toArray() as $key => $value) {
            $match = [];
            if (preg_match('/^c:(.*)/', $key, $match)) {
                $this->_elements[1][$match[1]] = $value;
            } elseif (preg_match('/^l:(.*)/', $key, $match)) {
                $this->_elements[2][$match[1]] = $value;
            } elseif (preg_match('/^f:(.*)/', $key, $match)) {
                $this->_elements[3][$match[1]] = $value;
            } elseif (preg_match('/^o:(.*)/', $key, $match)) {
                $this->_elements[4][$match[1]] = $value;
            } else {
                $this->_elements[5][$key] = $value;
            }
        }

        return $n ? isset($this->_elements[$n]) ? $this->_elements[$n] : array() : array();
    }

    public function elementFoptions()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(
            array(
                'class'        => 'ui left labeled input',
                'class_append' => null
            )
        );

        $options = $resolver->resolve($this->getElements(3));

        if ($options['class_append']) {
            $options['class'] .= ' ' . $options['class_append'];
        }

        unset($options['class_append']);

        return $options;
    }

    public function elementOoptions()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(
            array(
                'icon'     => null,
                'asterisk' => null
            )
        );

        $options = $resolver->resolve($this->getElements(4));

        return $options;
    }

    public function elementCoptions()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(
            array(
                'class'        => 'field',
                'class_append' => null
            )
        );

        $options = $resolver->resolve($this->getElements(1));

        if ($options['class_append']) {
            $options['class'] .= ' ' . $options['class_append'];
        }

        unset($options['class_append']);

        return $options;
    }

    public static function create(Form $form, FormView $formView)
    {
        $attrs = $formView->vars['attr'];

        ####################
        # input attributes #
        ####################
        // standard
        $attrs['id']    = $formView->vars['id'];
        $attrs['name']  = $formView->vars['full_name'];
        $attrs['value'] = $formView->vars['value'];

        $form->applyNgModel($formView, $attrs);

        // optionals
        if ($formView->vars['required']) {
            $attrs['required'] = 'required';
        }

        if ($formView->vars['read_only']) {
            $attrs['readOnly'] = 'readOnly';
        }

        if ($formView->vars['disabled']) {
            $attrs['disabled'] = 'disabled';
        }

        if ($formView->vars['size']) {
            $attrs['size'] = $formView->vars['size'];
        }

        #########################################
        # (C) top cover attributes
        #########################################
        if (!$formView->vars['valid']) {
            if (isset($attrs['c:class_append'])) {
                $attrs['c:class_append'] .= ' error';
            } else {
                $attrs['c:class_append'] = 'error';
            }
        }

        #########################################
        # (F) field cover attributes
        #########################################
        if (isset($attrs['o:icon'])) {
            if (isset($attrs['f:class_append'])) {
                $attrs['f:class_append'] .= ' icon';
            } else {
                $attrs['f:class_append'] = 'icon';
            }
        }

        #########################################
        # (O) options attributes
        #########################################
        if (isset($attrs['o:icon'])) {
            // if have icon option convert it to semantic-ui
            $attrs['o:icon'] = sprintf('<i class="%s icon"></i>', $attrs['o:icon']);
        }

        // if field is required make asterisk
        if ($formView->vars['required'] || $attrs['o:asterisk']) {
            $attrs['o:asterisk'] = '<div class="ui corner label"><i class="icon asterisk"></i></div>';
        }

        $attr = new self($attrs);

        return (object) array(
            'input'  => $attr->getElements(5),
            'label'  => array_merge($formView->vars['label_attr'], $attr->getElements(2)),
            'cover'  => $attr->elementCoptions(),
            'field'  => $attr->elementFoptions(),
            'option' => $attr->elementOoptions(),
        );
    }
}