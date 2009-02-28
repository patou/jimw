<?php
class Jimw_Install_Plugin_Protect extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup (Zend_Controller_Request_Abstract $request)
    {
        if ($request->getControllerName() != 'error') {
            if (defined('JIMW_INSTALL_PROTECT_DOMAIN')) {
                if ($request->getSubDomain() . '.' . $request->getDomainName() != JIMW_INSTALL_PROTECT_DOMAIN)
                {
                    $error            = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
                    $error->exception = new Jimw_Install_Exception('Install administration not allowed on this domain');
                    $error->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER;

                    // Keep a copy of the original request
                    $error->request = clone $request;

                    // Forward to the error handler
                    $request->setParam('error_handler', $error)
                            ->setModuleName('default')
                            ->setControllerName('error')
                            ->setActionName('error')
                            ->setDispatched(false);
                    //throw new Jimw_Install_Exception('Install administration not allowed on this domain');
                }
            }
            $session = new Zend_Session_Namespace('Install');
            if (! isset($session->protect_file) || ! file_exists(JIMW_INSTALL_PROTECTDIR . $session->protect_file)) {
                $request->setControllerName('protect');
                $request->setActionName('index');
                $request->setModuleName('default');
            }
        }
    }
}
