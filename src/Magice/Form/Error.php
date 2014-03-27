<?php
namespace Magice\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormError;

class Error implements \Countable
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var FormInterface|FormView
     */
    private $form;

    /**
     * @param ContainerInterface     $container
     * @param FormInterface|FormView $form
     */
    public function __construct(ContainerInterface $container, $form = null)
    {
        $this->container = $container;
        $this->form      = $form;
    }

    /**
     * @param FormInterface|FormView $form
     *
     * @return array
     */
    public function all($form = null)
    {
        $form = $form ? : $this->form;

        if (empty($form)) {
            return array();
        }

        return array_merge($this->getFormErrors($form), $this->getFieldErrors($form));
    }

    /**
     * @param FormInterface|FormView $form
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
     * @param FormInterface|FormView $form
     *
     * @return array
     */
    public function getErrors($form)
    {
        $errors = array();
        $trans  = $this->container->get('translator');

        if ($form instanceof FormInterface) {
            foreach ($form->getErrors() as $error) {
                $message = $trans->trans($error->getMessage(), array(), 'validators');
                array_push($errors, $message);
            }
        }

        if ($form instanceof FormView) {
            $errs = isset($form->vars['errors']) ? $form->vars['errors'] : array();

            /**
             * @var FormError $error
             */
            foreach ($errs as $error) {
                $message = $trans->trans($error->getMessage(), array(), $form->vars['translation_domain']);
                array_push($errors, $message);
            }
        }

        return $errors;
    }

    /**
     * @param FormInterface|FormView $form
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
     * @param FormInterface|FormView $form
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
        $childs = $form instanceof FormView ? $form->children : $form->all();

        foreach ($childs as $key => $child) {
            if ($err = $this->getErrors($child)) {
                $errors[$key] = $err;
            }
        }

        return $errors;
    }

    /**
     * @param FormInterface|FormView $form
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
        $childs = $form instanceof FormView ? $form->children : $form->all();

        foreach ($childs as $key => $child) {
            if ($err = $this->getErrors($child)) {
                $errors[$key] = $key;
            }
        }

        return array_values($errors);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->all());
    }
}