<?php
/**
 * Jimw_Module_Action_Alias
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
abstract class Jimw_Module_Action_Alias extends Jimw_Module_Action_Abstract
{
    abstract public function viewModule ($alias);
    /**
     * Dispatch the requested action
     *
     * @param string $action Method name of action
     * @return void
     */
    public function dispatch ($action)
    {
        // Notify helpers of action preDispatch state
        $this->_helper->notifyPreDispatch();
        $this->preDispatch();
        if ($this->getRequest()->isDispatched()) {
            // preDispatch() didn't change the action, so we can continue
            $this->viewModule($action);
            $this->postDispatch();
        }
        // whats actually important here is that this action controller is
        // shutting down, regardless of dispatching; notify the helpers of this
        // state
        $this->_helper->notifyPostDispatch();
    }
}