<?php
/**
 * This file is part of MagiceAssetBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\AssetBundle\Twig;

use Magice\Form\ViewError;
use Magice\Utils\Arrays;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Form as SymForm;

class Form extends \Twig_Extension implements ContainerAwareInterface
{
    public $fieldErrorMsgDisabled = false;
    public $labelSeparator = '';
    public $scriptDeclared = [];
    public $error = false;
    public $warning = false;
    public $ngModelDataPrefix = '';
    public $useNgModel = false;
    public $uiFieldSize = null;

    /**
     * @var Container
     */
    protected $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Returns the name of the extension.
     * @return string The extension name
     */
    public function getName()
    {
        return 'magice_asset_ui_form';
    }

    public function getFunctions()
    {
        $self = array('is_safe' => array('all'));
        return array(
            new \Twig_SimpleFunction('ui_form_field_error_msg_disabled', array($this, 'ui_form_field_error_msg_disabled'), $self),
            new \Twig_SimpleFunction('ui_form_warning_error', array($this, 'ui_form_warning_error'), $self),
            new \Twig_SimpleFunction('ui_form_separator', array($this, 'ui_form_separator'), $self),
            new \Twig_SimpleFunction('ui_form_scripts', array($this, 'ui_form_scripts'), $self),
            new \Twig_SimpleFunction('ui_form_errors', array($this, 'ui_form_errors'), $self),
            new \Twig_SimpleFunction('ui_form_widget', array($this, 'ui_form_widget'), $self),
            new \Twig_SimpleFunction('ui_form_select', array($this, 'ui_form_select'), $self),
            new \Twig_SimpleFunction('ui_form_date', array($this, 'ui_form_date'), $self),
            new \Twig_SimpleFunction('ui_form_email', array($this, 'ui_form_email'), $self),
            new \Twig_SimpleFunction('ui_form_url', array($this, 'ui_form_url'), $self),
            new \Twig_SimpleFunction('ui_form_password', array($this, 'ui_form_password'), $self),
            new \Twig_SimpleFunction('ui_form_hiddens', array($this, 'ui_form_hiddens'), $self),
            new \Twig_SimpleFunction('ui_form_ng', array($this, 'ui_form_ng'), $self),
            new \Twig_SimpleFunction('ui_form_size', array($this, 'ui_form_size'), $self),
        );
    }

    public function tpl()
    {
        $args  = func_get_args();
        $param = array_pop($args);

        $tpl = implode("\n", $args);

        return preg_replace_callback(
            '/{(.*)}/U',
            function ($match) use ($param) {
                return isset($param[$match[1]]) ? $param[$match[1]] : null;
            },
            $tpl
        );
    }

    public function trans($msg, $domain)
    {
        return $this->container->get('translator')->trans($msg, array(), $domain);
    }

    public function locale()
    {
        return $this->container->getParameter('locale');
    }

    public function isAttr($r, $attr, $html5 = true)
    {
        $value = $r->$attr;

        $attr = str_replace('_', '', $attr);

        if ($html5) {
            return empty($value) ? '' : ' ' . $attr;
        } else {
            return empty($value) ? '' : (' ' . $attr . '="' . $value . '"');
        }
    }

    public function getAttrs($attr, $def = array(), $appenedCls = array())
    {
        $attr = array_replace($def, $attr);

        if (!empty($appenedCls)) {
            $cls = implode(' ', $appenedCls);
            if (isset($attr['class'])) {
                $attr['class'] .= ' ' . $cls;
            } else {
                $attr['class'] = $cls;
            }
        }

        if (empty($attr)) {
            return '';
        }

        return ' ' . Arrays::toAttrs($attr);
    }

    public function ui_form_scripts()
    {
        return implode("\n", $this->scriptDeclared);
    }

    public function ui_form_separator($str)
    {
        $this->labelSeparator = $str;
    }

    /**
     * @param FormView $formView
     * @param string   $dataPrefix
     * @param bool     $isChild
     *
     * @return string|null
     */
    public function ui_form_ng(FormView $formView, $dataPrefix = null, $isChild = false)
    {
        $this->useNgModel = true;

        $this->ngModelDataPrefix = $dataPrefix;

        if ($isChild) {
            return null;
        }

        return sprintf('<span ng-model="$form.name" ng-init="$form.name=\'%s\'"></span>', $formView->vars['name']);
    }

    /**
     * @see http://semantic-ui.com/elements/input.html
     *
     * @param $size
     */
    public function ui_form_size($size)
    {
        $this->uiFieldSize = $size;
    }

    public function ui_form_field_error_msg_disabled($flag)
    {
        $this->fieldErrorMsgDisabled = $flag;
    }

    public function applyNgModel(FormView $formView, &$attrs)
    {
        if ($this->useNgModel) {
            $name = $formView->vars['name'];

            // password fields
            if ($formView->vars['block_prefixes'][2] == 'password') {
                $name = $formView->parent->vars['name'] . '.' . $name;
            }

            if ($this->ngModelDataPrefix) {
                $name = $this->ngModelDataPrefix . '.' . $name;
            }

            $attrs['ng-model'] = $name;

            if (!empty($formView->vars['type']) && ($formView->vars['type'] === 'number' || $formView->vars['type'] === 'integer')) {
                $attrs['ng-init'] = $name . "=" . $formView->vars['value'];
            } else {
                $attrs['ng-init'] = $name . "='" . $formView->vars['value'] . "'";
            }
        }
    }

    protected function getField($type, FormView $form, $attr = array())
    {
        if (empty($attr)) {
            $form->vars['label'] = $this->trans($form->vars['label'], $form->vars['translation_domain']);
        } else {

            // if config label from view you must translate it before with xxx|trans
            if (isset($attr['label'])) {
                $form->vars['label'] = $attr['label'];
                unset($attr['label']);
            } else {
                $form->vars['label'] = $this->trans($form->vars['label'], $form->vars['translation_domain']);
            }

            // prevent build error on not submit
            // fields due to ->valid status to indicate error
            if (!$form->vars['submitted']) {
                $form->vars['valid'] = true;
            }

            $form->vars['attr'] = array_replace($form->vars['attr'], $attr);
        }

        $class = sprintf('\Magice\Bundle\AssetBundle\Twig\Field\%s::getField', $type);
        return call_user_func_array($class, array($this, $form));
    }

    public function ui_form_widget(FormView $form, $attr = array())
    {
        $r = (object) $form->vars;

        if (isset($r->date_pattern)) {
            return $this->ui_form_date_select($form, $attr);
        }

        if (isset($r->type) && $r->type == 'date') {
            return $this->ui_form_date($form, $attr);
        }

        if (isset($r->expanded)) {
            return $this->ui_form_select($form, $attr);
        }

        if ($r->block_prefixes[1] == 'repeated') {
            return $this->ui_form_passwords($form, $attr);
        }

        if ($r->block_prefixes[1] == 'submit') {
            return $this->ui_form_button($form, $attr, 'submit');
        }

        if ($r->block_prefixes[1] == 'button') {
            return $this->ui_form_button($form, $attr);
        }

        if ((!empty($r->type) && $r->type === 'number') || $r->block_prefixes[1] == 'integer') {
            return $this->ui_form_number($form, $attr);
        }

        if ($r->block_prefixes[2] == 'email') {
            return $this->ui_form_email($form, $attr);
        }

        // https://github.com/misd-service-development/phone-number-bundle
        if ((isset($r->type) && $r->type == 'tel') || $r->block_prefixes[2] == 'tel') {
            return $this->ui_form_tel($form, $attr);
        }

        return $this->getField('Text', $form, $attr);
    }

    public function ui_form_select(FormView $form, $attr = array())
    {
        return $this->getField('Select', $form, $attr);
    }

    public function ui_form_hiddens(FormView $form)
    {
        $str = '';
        /**
         * @var FormView $field
         */
        foreach ($form->children as $field) {
            if (!$field->isRendered()) {
                $str .= $this->ui_form_hidden($field);
            }
        }

        return $str;
    }

    public function ui_form_hidden(FormView $formView)
    {
        $formView->setRendered();

        $attrs['id']    = $formView->vars['id'];
        $attrs['name']  = $formView->vars['full_name'];
        $attrs['value'] = $formView->vars['value'];

        $this->applyNgModel($formView, $attrs);

        return sprintf('<input type="hidden" %s>', Arrays::toAttrs($attrs));
    }

    public function ui_form_date(FormView $form, $attr = array())
    {
        return $this->getField('Date', $form, $attr);
    }

    public function ui_form_date_select(FormView $form, $attr = array())
    {
        return $this->getField('DateSelect', $form, $attr);
    }

    public function ui_form_email(FormView $form, $attr = array())
    {
        return $this->getField('Email', $form, $attr);
    }

    public function ui_form_number(FormView $form, $attr = array())
    {
        return $this->getField('Number', $form, $attr);
    }

    public function ui_form_url(FormView $form, $attr = array())
    {
        return $this->getField('Url', $form, $attr);
    }

    public function ui_form_password(FormView $form, $attr = array())
    {
        return $this->getField('Password', $form, $attr);
    }

    public function ui_form_tel(FormView $form, $attr = array())
    {
        return $this->getField('Tel', $form, $attr);
    }

    public function ui_form_passwords(FormView $form, $attr = array())
    {
        $first  = $form->children['first'];
        $second = $form->children['second'];

        $attr1 = $attr;
        $attr2 = $attr;

        if (isset($attr['placeholder'])) {
            $attr1['placeholder'] = $attr['placeholder'][0];
            $attr2['placeholder'] = $attr['placeholder'][1];
        }

        $first->vars['valid']  = $form->vars['valid'];
        $second->vars['valid'] = $form->vars['valid'];

        return
            $this->ui_form_password($first, $attr1)
            . $this->ui_form_password($second, $attr2);
    }

    public function ui_form_button(FormView $form, $attr = array(), $type = 'button')
    {
        $attr['type'] = $type;

        if (is_string($attr)) {
            $attr = array('o:style' => $attr);
        }

        return $this->getField('Button', $form, $attr);
    }

    public function ui_form_errors($form, array $otherError = null)
    {
        // by default if call global form error
        // we will disabled field error message level
        // however, you can set it on with reset it above this call
        $this->fieldErrorMsgDisabled = true;

        $msg = null;
        $t   = $this->container->get('translator');

        if (!empty($otherError)) {

            foreach ($otherError as $err) {

                if ($err instanceof \Exception) {
                    $msg .= sprintf('<li>%s</li>', $t->trans($err->getMessage()));
                }

                if ($err instanceof FormErrorMessageInterface) {
                    if ($str = (string) $err->getErrorMessage()) {
                        $msg .= sprintf('<li>%s</li>', $t->trans($str));
                    }
                }

                if (is_string($err)) {
                    $msg .= sprintf('<li>%s</li>', $t->trans($err));
                }
            }

            if ($msg) {
                $this->error = true;

                $msg = $this->tpl(
                    '<div class="ui error message">',
                    '    <div class="header">{header}</div>',
                    '    <ul>{msg}</ul>',
                    '</div>',
                    array(
                        'header' => $t->trans("โอ๊ะโอ๋!! มีบางอย่างไม่ถูกต้อง"),
                        'msg'    => $msg
                    )
                );
            }
        }

        $error = new ViewError($this->container, $form);
        $warn  = count($error);

        if ($warn || !empty($msg)) {
            if ($warn) {
                $this->warning = true;
                $warn          = $this->tpl(
                    '<div class="ui warning message">',
                    '    <div class="header">{header}</div>',
                    '    {msg}',
                    '</div>',
                    array(
                        'header' => $t->trans("โอ๊ะโอ๋!! คุณกรอกข้อมูลไม่ถูกต้อง"),
                        'msg'    => $error->allHtml()
                    )
                );
                return $warn . "\n" . $msg;
            }

            return $msg;

        } else {
            return null;
        }
    }

    /**
     * @note this must call after ui_form_errors
     */
    public function ui_form_warning_error()
    {
        return trim(sprintf('%s %s', $this->error ? ' error' : '', $this->warning ? ' warning' : ''));
    }

    public function getFieldErrors(FormView $formView)
    {
        $errors = '';
        if ($formView->vars['submitted'] && !$this->fieldErrorMsgDisabled && !empty($formView->vars['errors'])) {
            $formView->vars['valid'] = false;

            $errors = '<ul class="ui red pointing above ui label">';

            /**
             * @var \Symfony\Component\Form\FormError $e
             */
            foreach ($formView->vars['errors'] as $e) {
                $errors .= sprintf('<li>%s</li>', $this->trans($e->getMessage(), 'validators'));
            }

            $errors .= '</ul>';
        }

        return $errors;
    }

}