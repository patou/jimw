<?php
/**
 * Jimw_Site_View_ViewRenderer
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_View_ViewRenderer 
    extends Zend_Controller_Action_Helper_ViewRenderer
{
    /**
     * Name of layout script to render. Defaults to 'site.tpl.php'.
     *
     * @var string
     */
    protected $_layoutScript = 'site.tpl.php';
    
    /**
     * Name of layout script to render. Defaults to 'site.tpl.php'.
     *
     * @var string
     */
    protected $_layoutPath = './public/template';
    
    
    /**
     * Constructor
     *
     * Set the viewSuffix to "tpl.php" unless a viewSuffix option is 
     * provided in the $options parameter.
     * 
     * @param  Zend_View_Interface $view 
     * @param  array $options 
     * @return void
     */
    public function __construct(Zend_View_Interface $view = null, 
                                array $options = array())
    {
        if (!isset($options['viewSuffix'])) {
            $options['viewSuffix'] = 'tpl.php';
        }
        parent::__construct($view, $options);
    }
    
    /**
     * Set the layout script to be rendered.
     *
     * @param string $script
     */
    public function setLayoutScript($script)
    {
        $this->_layoutScript = $script;
    }
    
    /**
     * Retreive the name of the layout script to be rendered.
     *
     * @return string
     */
    public function getLayoutScript()
    {
        return $this->_layoutScript;
    }
    
     /**
     * Set the layout path to be rendered.
     *
     * @param string $path
     */
    public function setLayoutPath($path)
    {
        $this->_layoutPath = $script;
    }
    
    /**
     * Retreive the path of the layout script to be rendered.
     *
     * @return string
     */
    public function getLayoutPath()
    {
        return $this->_layoutPath;
    }
    
    /**
     * Render the action script and assign the the view for use
     * in the layout script. Render the layout script and append
     * to the Response's body.
     *
     * @param string $script
     * @param string $name
     */
    public function renderScript($script, $name = null)
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        if (null === $name) {
            $name = $this->getResponseSegment();
        }
        
        // assign action script name to view.
        $this->view->content = $script;
        
        // render layout script and append to Response's body
        $layoutScript = $this->getLayoutScript();        
        $layoutContent = $this->view->render($layoutScript);
        $this->getResponse()->appendBody($layoutContent, $name);
        
        $this->setNoRender();
    }
}