<?php
/**
 * Jimw_Global_Controler
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id:  $
 */
class Jimw_Global_Router implements Zend_Controller_Router_Interface
{
	/**
     * Array of invocation parameters to use when instantiating action 
     * controllers
     * @var array 
     */
    protected $_invokeParams = array();
    
	/**
     * Processes a request and sets its controller and action.  If
     * no route was possible, an exception is thrown.
     *
     * @param  Zend_Controller_Request_Abstract
     * @throws Zend_Controller_Router_Exception
     * @return Zend_Controller_Request_Abstract|boolean
     */
    public function route(Zend_Controller_Request_Abstract $request)
    {
    	if (!$request instanceof Jimw_Global_Request) {
            throw new Zend_Controller_Router_Exception('Jimw_Global_Router requires a Jimw_Global_Request-based request object');
        }
        $jimw_config_db = Zend_Registry::get('config_db');
        try {
        	$db = Zend_Db::factory($jimw_config_db['type'], $jimw_config_db);
        	$select = $db->select ();
        	$select->from('jimw_database');
        	$select->joinNatural('jimw_domain', '');
        	$select->where('domain_name = ?', $request->getDomainName());
        	$select->where('domain_port = ?', $request->getDomainPort());
        	$select->where('domain_protocol = ?', $request->getDomainProtocol());
        	$select->where('domain_subdomain = ?', $request->getSubDomaine());
        	$result = $db->fetchRow($select);
        } catch (Zend_Db_Adapter_Exception $e){
			echo $e->getMessage();
		} catch (Zend_Db_Select_Exception  $e){
			echo $e->getMessage();
		} catch (Zend_Db_Exception $e){
			echo $e->getMessage();
		}
        return $request;
    }

    /**
     * Add or modify a parameter with which to instantiate any helper objects
     * 
     * @param string $name 
     * @param mixed $param 
     * @return Zend_Controller_Router_Interface
     */
    public function setParam($name, $value)
    {
        $name = (string) $name;
        $this->_invokeParams[$name] = $value;
        return $this;
    }

    /**
     * Set parameters to pass to action controller constructors
     * 
     * @param array $params 
     * @return Zend_Controller_Router
     */
    public function setParams(array $params)
    {
        $this->_invokeParams = array_merge($this->_invokeParams, $params);
        return $this;
    }

    /**
     * Retrieve a single parameter from the controller parameter stack
     * 
     * @param string $name 
     * @return mixed
     */
    public function getParam($name)
    {
        if(isset($this->_invokeParams[$name])) {
            return $this->_invokeParams[$name];
        }

        return null;
    }

    /**
     * Retrieve action controller instantiation parameters
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->_invokeParams;
    }

    /**
     * Clear the controller parameter stack
     *
     * By default, clears all parameters. If a parameter name is given, clears 
     * only that parameter; if an array of parameter names is provided, clears 
     * each.
     * 
     * @param null|string|array single key or array of keys for params to clear
     * @return Zend_Controller_Router
     */
    public function clearParams($name = null)
    {
        if (null === $name) {
            $this->_invokeParams = array();
        } elseif (is_string($name) && isset($this->_invokeParams[$name])) {
            unset($this->_invokeParams[$name]);
        } elseif (is_array($name)) {
            foreach ($name as $key) {
                if (is_string($key) && isset($this->_invokeParams[$key])) {
                    unset($this->_invokeParams[$key]);
                }
            }
        }

        return $this;
    }

}
?>