<?php



class Jimw_Global_Layout_Plugin extends Zend_Layout_Controller_Plugin_Layout {
    public function postDispatch(Zend_Controller_Request_Abstract $request) {
        try {
            parent::postDispatch($request);
        }
        catch (Zend_View_Exception $e) {
            
            $this->getResponse()->setBody($request->getParam('layoutContent', ''));
        }
    }
}

?>
