<?php

/**
 * FormController
 * Module to create web form
 * @author	    Patou
 * @category   Jimw_Form
 * @package    Jimw_Module
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id: FormController.php 157 2008-02-07 13:19:58Z patou.de.saint.steban $
 */
class Form_FormController extends Jimw_Module_Action_Alias {

    public function viewModule($alias) {
        $req = $this->getRequest();
        $tree = $req->getTree();
        $site_path = Zend_Registry::get('site_path', '');
        $form_file = JIMW_ROOT . $site_path . '/' . $tree->param['form_file'];
        if (!empty($tree->param->form_file) && file_exists($form_file)) {
            if (strpos($form_file, '.ini') > 0)
                $config = new Zend_Config_Ini($form_file);
            else
                $config = new Zend_Config_Xml($form_file);

            //Jimw_Debug::dump($config->toArray());
        }
        else {
            //Default contact form
            $config = $this->defaultContactForm();
        }
        $form = new Jimw_Form($config);
        Jimw_Debug::dump($form);
        if ($req->isPost() && $form->isValid($req->getPost())) {
            $this->validForm($form, $tree);
        } else {
            $form->setAction($this->view->url(array('alias' => $tree->alias), 'alias'));
            $this->view->form = $form;
            $this->render('form');
        }
    }

    protected function validForm(Jimw_Form $form, $tree) {
        $database = $tree->param->get('database', false);
        if ($database) {
            $db = new form_Form_Table(array('name' => $database));
            /* $new = $db->fetchNew($form->getValues());
              $data = array();

              $cols = $db->info('cols');
              foreach($cols as $col) {
              $data[$col] = $form->getValue($col);
              $new->__set($col, $data[$col]);
              }
              //$new->setFromArray($data); */
            $new = $db->createRow($form->getValues());
            $new->save();
        }
        $email = $tree->param->get('email', false);
        if ($email) {
            if (JIMW_UTF8) {
                $mail = new Zend_Mail('UTF-8');
            } else {
                $mail = new Zend_Mail();
            }

            $email_text = trim($tree->param->get('email_text', 'form/mail.phtml'));
            if (empty($email_text)) {
                $email_text = 'form/mail.phtml';
            }
            $values = $form->getValues();
            if (isset($tree->param['form_file']) && empty($tree->param['email_text'])) {
                $text = "\n";
                foreach ($values as $name => $value) {
                    $text .= ucfirst(str_replace('_', ' ', $name)) . ': ' . $value . "\n";
                }
                $values['contact'] = $text;
            }
            $content = $this->view->partial($email_text, null, $values);
            $pos = strpos($content, "\n");
            $subject = substr($content, 0, $pos);
            $content = substr($content, $pos);
            $mail->setBodyText($content);
            if (isset($values['email'])) {
                $mail->setFrom($values['email'], $values['email']);
            }
            else {
                $mail->setFrom($email, $email);
            }
            $mail->addTo($email, $email);
            $mail->setSubject($subject);
            $mail->send();
        }
        $this->render('formSuccess');
    }

    private function defaultContactForm() {
        return new Zend_Config(array(
            'elements' => array(
                'name' => array(
                    'type' => 'text'
                    , 'options' => array(
                        'label' => 'Name'
                        , 'required' => true
                    )
                )
                , 'email' => array(
                    'type' => 'Email'
                    , 'options' => array(
                        'label' => 'Email'
                        , 'required' => true
                    //, 'validators' => array('email' => array('validator' => 'EmailAddress'))
                    )
                )
                , 'contact' => array(
                    'type' => 'textarea'
                    , 'options' => array(
                        'label' => 'Text'
                        , 'required' => true
                    )
                )
                , 'send' => array(
                    'type' => 'submit'
                    , 'options' => array(
                        'label' => 'Send'
                    )
                )
            )
        ));
    }

}
