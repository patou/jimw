<?php
class Jimw_Site_View_ViewRenderer extends Zend_Controller_Action_Helper_ViewRenderer
{
    /**
     * Name of layout script to render. Defaults to 'layout'.
     *
     * @var string
     */
    protected $_layoutScript = 'layout';
    protected $_renderLayout = true;
    protected $_appendBody = false;
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
    public function __construct (Zend_View_Interface $view = null, array $options = array())
    {
        if (! isset($options['viewSuffix'])) {
            $options['viewSuffix'] = 'phtml';
        }
        parent::__construct($view, $options);
    }
    /**
     * Set the layout script to be rendered.
     *
     * @param string $script
     */
    public function setLayoutScript ($script)
    {
        Jimw_Debug::deprecated('Jimw_Site_View_ViewRenderer->setLayoutScript()', 'layout()->setLayout()');
        $this->_layoutScript = $script;
        return $this;
    }
    /**
     * Retreive the name of the layout script to be rendered.
     *
     * @return string
     */
    public function getLayoutScript ()
    {
        return $this->_layoutScript;
    }
    /**
     * Set if the Layout must be render.
     *
     * @param string $script
     */
    public function setRenderLayout ($renderLayout = true)
    {
        Jimw_Debug::deprecated('Jimw_Site_View_ViewRenderer->setRenderLayout()', 'layout()->setMvcSuccessfulActionOnly()');
        Zend_Layout::getMvcInstance()->setMvcSuccessfulActionOnly($renderLayout);
        $this->_renderLayout = ($renderLayout) ? true : false;
        return $this;
    }
    /**
     * If appendBody is true The result is append to the body response, otherwise, the result is append to the content
     *
     * @param string $script
     */
    public function setAppendBody ($appendBody = true)
    {
        Jimw_Debug::deprecated('Jimw_Site_View_ViewRenderer->setAppendBody()', 'layout()->setMvcSuccessfulActionOnly()');
        $this->_appendBody = ($appendBody) ? true : false;
        return $this;
    }
    /**
     * Set that the Layout mustn't be render.
     *
     * @param string $script
     */
    public function noRenderLayout ()
    {
        Jimw_Debug::deprecated('Jimw_Site_View_ViewRenderer->noRenderLayout()', 'layout()->setMvcSuccessfulActionOnly()');
        Zend_Layout::getMvcInstance()->setMvcSuccessfulActionOnly(false);
        $this->_renderLayout = false;
        return $this;
    }
    /**
     * Retreive if the Layout must be render.
     *
     * @return string
     */
    public function getRenderLayout ()
    {
        return $this->_renderLayout;
    }
    public function preDispatch ()
    {
        $this->_renderLayout = true;
    }
    /**
     * Retreive the name of the layout script to be rendered.
     *
     * @return string
     */
    public function formatLayoutScript ()
    {
        $layout = $this->_layoutScript;
        if ($this->getViewSuffix())
            $layout .= '.' . $this->getViewSuffix();
        return $layout;
    }
/**
 * Render the action script and assign the the view for use
 * in the layout script. Render the layout script and append
 * to the Response's body.
 *
 * @param string $script
 * @param string $name
 */
/*public function renderScript($script, $name = null)
    {
        $this->view->baseUrl = $this->getRequest()->getBaseUrl();
        if (null === $name) {
            $name = $this->getResponseSegment();
        }
        
        // assign action script name to view.
        //if ($this->_appendBody)
        	//$this->getResponse()->appendBody ($this->view->render($script), null);
        //else
        	//$this->view->appendContent ($this->view->render($script));
        
        // render layout script and append to Response's body
        /*if ($this->_renderLayout) {
        	try {
	        	$layoutScript = $this->formatLayoutScript();
	        	$layoutContent = $this->view->render($layoutScript);
	        	$this->getResponse()->appendBody($layoutContent, $name);
        	}
        	catch (Zend_View_Exception $e) {
        		if ($this->_viewSuffix == 'phtml')
        			throw $e;
        		$this->getResponse()->appendBody ($this->view->getContent());
        	}
        }* /
        //$this->setNoRender();
        parent::renderScript($script, $name);
        Jimw_Debug::dump($this->getResponse());
    }*/
}

