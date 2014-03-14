<?php
namespace Magice\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;

class Error
{
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Form $form
     *
     * @return array
     */
    public function all(Form $form)
    {
        return array_merge($this->getFormErrors($form), $this->getFieldErrors($form));
    }

    public function allHtml(Form $form)
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
     * @param Form $form
     *
     * @return array
     */
    public function getErrors(Form $form)
    {
        $errors = array();

        foreach ($form->getErrors() as $error) {
            $message = $this->container->get('translator')->trans($error->getMessage(), array(), 'validators');
            array_push($errors, $message);
        }

        return $errors;
    }

    public function getFormErrors(Form $form)
    {
        $errors = array();

        if ($err = $this->getErrors($form)) {
            $errors['form'] = $err;
        }

        return $errors;
    }

    public function getFieldErrors(Form $form)
    {
        $errors = array();

        foreach ($form->all() as $key => $child) {
            if ($err = $this->getErrors($child)) {
                $errors[$key] = $err;
            }
        }

        return $errors;
    }
}