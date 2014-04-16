<?php
namespace Magice\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormError;

class ViewError implements \Countable
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var FormView
     */
    private $form;

    private static $allErrors;

    /**
     * @param ContainerInterface $container
     * @param FormView           $form
     */
    public function __construct(ContainerInterface $container, FormView $form = null)
    {
        $this->container = $container;
        $this->form      = $form;
    }

    /**
     * @param FormView $form
     *
     * @return array
     */
    public function all(FormView $form = null)
    {
        if (static::$allErrors) {
            return static::$allErrors;
        }

        $form = $form ? : $this->form;

        if (empty($form)) {
            return array();
        }

        return self::$allErrors = array_merge($this->getFormErrors($form), $this->getFieldErrors($form));
    }

    /**
     * @param FormView $form
     *
     * @return null|string
     */
    public function allHtml($form = null)
    {
        $all = $this->all($form);

        if (empty($all)) {
            return null;
        }

        $str = '<ul>';
        foreach ($all as $key => $errs) {
            $str .= sprintf('<li class="frm-err-%s">', $key);
            if (count($errs) > 1) {
                $str .= '<ul>';
                foreach ($errs as $err) {
                    $str .= sprintf('<li>%s</li>', $err);
                }
                $str .= '</ul>';
            } else {
                $str .= $errs[0];
            }
            $str .= '</li>';
        }

        $str .= '</ul>';

        return $str;
    }

    /**
     * @param FormView $form
     *
     * @return array
     */
    public function getErrors($form)
    {
        $errors = array();
        $trans  = $this->container->get('translator');

        $errs = isset($form->vars['errors']) ? $form->vars['errors'] : array();

        /**
         * @var FormError $error
         */
        foreach ($errs as $error) {
            $message = $trans->trans($error->getMessage(), array(), $form->vars['translation_domain']);
            array_push($errors, $message);
        }

        return $errors;
    }

    /**
     * @param FormView $form
     *
     * @return array
     */
    public function getFormErrors($form = null)
    {
        $form = $form ? : $this->form;

        if (empty($form)) {
            return array();
        }

        $errors = array();

        if ($err = $this->getErrors($form)) {
            $errors['form'] = $err;
        }

        return $errors;
    }

    /**
     * @param FormView $form
     *
     * @return array
     */
    public function getFieldErrors($form)
    {
        $form = $form ? : $this->form;

        if (empty($form)) {
            return array();
        }

        $errors = array();
        $childs = $form->children;

        /**
         * @var FormView $child
         */
        foreach ($childs as $key => $child) {

            // FormView
            if (empty($child->vars['errors']) && !empty($child->children)) {
                $errors = array_merge($errors, $this->getFieldErrors($child));
            } else {
                if ($err = $this->getErrors($child)) {
                    $errors[$key] = $err;
                }
            }
        }

        return $errors;
    }

    /**
     * @param FormView $form
     *
     * @return array
     */
    public function getErrorFields($form)
    {
        $form = $form ? : $this->form;

        if (empty($form)) {
            return array();
        }

        $errors = array();
        $childs = $form->children;

        foreach ($childs as $key => $child) {
            if ($err = $this->getErrors($child)) {
                $errors[$key] = $key;
            }
        }

        return array_values($errors);
    }

    public function count()
    {
        return count($this->all());
    }
}