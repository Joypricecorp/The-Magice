<?php
/**
 * This file is part of MagiceAssetBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\AssetBundle\Twig;

use Magice\Form\Error;
use Magice\Utils\Arrays;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormView;

class Form extends \Twig_Extension implements ContainerAwareInterface
{
    public $fieldErrorMsgDisabled = false;
    public $labelSeparator = '';
    public $scriptDeclared = [];
    public $error = false;
    public $warning = false;
    public static $ngModelDataPrefix = 'data.';

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

    public function locale() {
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

    public function ui_form_field_error_msg_disabled($flag)
    {
        $this->fieldErrorMsgDisabled = $flag;
    }

    protected function getField($type, FormView $form, $attr = array(), $opts = array())
    {
        if (!empty($attr)) {

            if (isset($attr['label'])) {
                $form->vars['label'] = $attr['label'];
                unset($attr['label']);
            }

            $form->vars['attr'] = array_replace($form->vars['attr'], $attr);
        }

        if (!empty($opts)) {
            $form->vars['_opts'] = $opts;
        }

        $class = sprintf('\Magice\Bundle\AssetBundle\Twig\Field\%s::getField', $type);
        return call_user_func_array($class, array($this, $form));
    }

    public function ui_form_widget(FormView $form, $attr = array(), $opts = array())
    {
        $r = (object) $form->vars;

        if (isset($r->date_pattern)) {
            return $this->ui_form_date_select($form, $attr, $opts);
        }

        if (isset($r->type) && $r->type == 'date') {
            return $this->ui_form_date($form, $attr, $opts);
        }

        if (isset($r->expanded)) {
            return $this->ui_form_select($form, $attr, $opts);
        }

        if ($r->block_prefixes[1] == 'repeated') {
            return $this->ui_form_passwords($form, $attr, $opts);
        }

        if ($r->block_prefixes[1] == 'submit') {
            return $this->ui_form_button($form, $attr, $opts, 'submit');
        }

        if ($r->block_prefixes[1] == 'button') {
            return $this->ui_form_button($form, $attr, $opts);
        }

        if (!empty($attr)) {
            $form->vars['attr'] = array_replace($form->vars['attr'], $attr);
        }

        if ($r->block_prefixes[1] == 'integer') {
            return $this->ui_form_number($form, $attr, $opts);
        }

        // https://github.com/misd-service-development/phone-number-bundle
        if ((isset($r->type) && $r->type == 'tel') || $r->block_prefixes[2] == 'tel') {
            return $this->ui_form_tel($form, $attr, $opts);
        }

        return $this->getField('Text', $form, $attr, $opts);
    }

    public function ui_form_select(FormView $form, $attr = array(), $opts = array())
    {
        return $this->getField('Select', $form, $attr, $opts);
    }

    public function ui_form_date(FormView $form, $attr = array(), $opts = array())
    {
        return $this->getField('Date', $form, $attr, $opts);
    }

    public function ui_form_date_select(FormView $form, $attr = array(), $opts = array())
    {
        return $this->getField('DateSelect', $form, $attr, $opts);
    }

    public function ui_form_email(FormView $form, $attr = array(), $opts = array())
    {
        return $this->getField('Email', $form, $attr, $opts);
    }

    public function ui_form_number(FormView $form, $attr = array(), $opts = array())
    {
        return $this->getField('Number', $form, $attr, $opts);
    }

    public function ui_form_url(FormView $form, $attr = array(), $opts = array())
    {
        return $this->getField('Url', $form, $attr, $opts);
    }

    public function ui_form_password(FormView $form, $attr = array(), $opts = array())
    {
        return $this->getField('Password', $form, $attr, $opts);
    }

    public function ui_form_tel(FormView $form, $attr = array(), $opts = array())
    {
        return $this->getField('Tel', $form, $attr, $opts);
    }

    public function ui_form_passwords(FormView $form, $attr = array(), $opts = array())
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
            $this->ui_form_password($first, $attr1, $opts)
            . $this->ui_form_password($second, $attr2, $opts);
    }

    public function ui_form_button(FormView $form, $attr = array(), $opts = array(), $type = 'button')
    {
        $opts['type'] = $type;

        if (is_string($attr)) {
            $opts['style'] = $attr;
            $attr          = array();
        }

        if (!empty($attr)) {
            $form->vars['attr'] = array_replace($form->vars['attr'], $attr);
        }

        return $this->getField('Button', $form, $attr, $opts);
    }

    public function ui_form_errors(FormView $form, array $otherError = null)
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

        $error = new Error($this->container, $form);
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

}