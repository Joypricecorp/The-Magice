<?php
namespace Magice\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;

class Error implements \Countable
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    private $form;

    /**
     * @param ContainerInterface $container
     * @param FormInterface      $form
     */
    public function __construct(ContainerInterface $container, FormInterface $form = null)
    {
        $this->container = $container;
        $this->form      = $form;
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    public function all(FormInterface $form = null)
    {
        $form = $form ? : $this->form;

        if (empty($form)) {
            return array();
        }

        return array_merge($this->getFormErrors($form), $this->getFieldErrors($form));
    }

    /**
     * @param FormInterface $form
     *
     * @return null|string
     */
    public function allHtml(FormInterface $form = null)
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
     * @param FormInterface $form
     *
     * @return array
     */
    public function getErrors(FormInterface $form)
    {
        $errors = array();

        foreach ($form->getErrors() as $error) {
            $message = $this->container->get('translator')->trans($error->getMessage(), array(), 'validators');
            array_push($errors, $message);
        }

        return $errors;
    }

    public function getFormErrors(FormInterface $form = null)
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
     * @param FormInterface $form
     *
     * @return array
     */
    public function getFieldErrors(FormInterface $form)
    {
        $form = $form ? : $this->form;

        if (empty($form)) {
            return array();
        }

        $errors = array();

        foreach ($form->all() as $key => $child) {
            if ($err = $this->getErrors($child)) {
                $errors[$key] = $err;
            }
        }

        return $errors;
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