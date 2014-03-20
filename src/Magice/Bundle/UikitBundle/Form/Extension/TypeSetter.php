<?php
namespace Magice\Bundle\UikitBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class TypeSetter extends AbstractTypeExtension
{
    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['original_type'] = $form->getConfig()->getType()->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return "form";
    }
}
