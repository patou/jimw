<?php

/**
 * TemplateController.php
 *
 * @author	   Patou
 * @category   Jimw_Admin
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

/**
 * This controler allow to administrate the list of template.
 *
 */
class TemplateController extends Jimw_Admin_Action {


    /**
     * Check the user allowed with the admin_system permission
     */
    public function preDispatch() {
        $this->checkRoleAllowed('admin_system');
    }

    /**
     * The default action - show the home page
     */
    public function indexAction() {
        $this->_forward('list');
    }

    /**
     * List all template
     *
     */
    public function listAction() {
        $templates = array();
        $dir_iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(JIMW_REP_PUBLIC), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($dir_iterator as $dir) {
            if ($dir->isDir() && file_exists($dir->getPathname() . '/layout.phtml')) {
                $path = trim(str_replace('\\', '/', $dir->getPathname()), '../');
                $templates[$path] = array('path' => trim(str_replace('\\', '/', $dir->getPathname()), '../'), 'name' => $this->getTemplateName($dir), 'configuration' => false);
                if (file_exists($dir->getPathname() . '/jimw.xml')) {
                    $xml = simplexml_load_file($dir->getPathname() . '/jimw.xml');
                    if (isset($xml->name)) {
                        $templates[$path]['name'] = $xml->name;
                    }
                    if (isset($xml->version)) {
                        $templates[$path]['version'] = $xml->version;
                    }
                    if (isset($xml->author)) {
                        $templates[$path]['author'] = $xml->author;
                    }
                    if (isset($xml->url)) {
                        $templates[$path]['url'] = $xml->url;
                    }
                    if (isset($xml->comment)) {
                        $templates[$path]['comment'] = $xml->comment;
                    }
                    if (isset($xml->preview)) {
                        $templates[$path]['preview'] = $xml->preview;
                    }
                    if (isset($xml->comment)) {
                        $templates[$path]['comment'] = $xml->comment;
                    }
                    if (isset($xml->configuration)) {
                        $templates[$path]['configuration'] = true;
                    }
                }
            }
        }
        $this->view->templates_list = $templates;
    }

    /**
     * Edit a template
     *
     */
    public function configurationAction() {
        $templates = new Jimw_Site_Module();
        $trees = new Jimw_Site_Tree();
        $request = $this->getRequest();
        $template_path = $request->template_path;
        if ($template_path == null) {
            throw new Jimw_Admin_Exception('This template didn\'t exist');
        }
        $template = $templates->fetchRow($templates->select()->where('module_path = ?', $templates_path));
        if (!isset($template_path->xml->configuration)) {
            $this->_helper->getHelper('FlashMessenger')->addMessage($this->_('This template hasn\'t configuration'));
            $this->_forward('list');
            return;
        }
        $form = new Jimw_Form($template_path->xml->configuration);
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $template_path->config = $form->getValues();
            $template_path->save ();
            $this->_helper->getHelper('FlashMessenger')->addMessage($this->_('Save configuration successful'));
            $this->_forward('list');
        }
        else {
            $form->addSubmit();
            $config = $$template_path->config->toArray();
            if (config != null) {
                $form->populate($config);
            }
            $this->view->form = $form;
            $this->render('form');
        }
    }

    /**
     *
     * @param <type> $dir
     */
    public function getTemplateName($dir) {
        $filename = $dir->getFilename();
        if ($filename == 'template') {
            $filename = trim($dir->getPath());
            $pos = strrpos($filename, DIRECTORY_SEPARATOR);
            $filename = substr($filename, $pos > 0 ? $pos + 1 : 0);
        }
        return ucfirst(str_replace(array('-','_'), ' ', $filename));
    }
}

