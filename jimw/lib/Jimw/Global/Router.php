<?php
/**
 * Jimw_Global_Controler
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Global_Router extends Zend_Controller_Router_Abstract
{
    
    /**
     * Test the connexion to the database
     *
     * @param  array
     * @throws Jimw_Global_Exception
     * @return Zend_Controller_Request_Abstract|boolean
     */
    private function testConnexion (array $param) {
    	$db_param = array ('dbname' => $param['database_name'],
    						'host' => $param['database_server'],
    						'username' => $param['database_user'],
    						'password' => $param['database_pass']);
    	try {
    		$db = Zend_Db::factory($param['database_type'], $db_param);
    		$db->getConnection();
    		Zend_Db_Table::setDefaultAdapter($db);
    		Zend_Registry::set('db', $db);
    		Zend_Registry::set('db_prefix', $param['database_prefix']);
    	} catch (Zend_Db_Adapter_Exception $e){
			echo $e->getMessage();
			throw new Jimw_Global_Exception('Could not connect to the database');
		}
    }
    
	/**
     * Processes a request and sets its controller and action.  If
     * no route was possible, an exception is thrown.
     *
     * @param  Zend_Controller_Request_Abstract
     * @throws Jimw_Global_Exception
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
        	$select->from('jimw_database', '*');
        	$select->joinNatural('jimw_domain', 'database_id');
        	$select->where('domain_name = ?', $request->getDomainName());
        	$select->where('domain_port = ?', $request->getDomainPort());
        	$select->where('domain_protocol = ?', $request->getDomainProtocol());
        	$select->where('domain_subdomain = ?', $request->getSubDomain());
        	$result = $db->fetchRow($select);
        	if ($result === false)
        		throw new Jimw_Global_Exception('Unknow website', 404);
        	$this->testConnexion($result);
        	//Zend_Debug::dump($result);
        } catch (Zend_Db_Adapter_Exception $e){
			echo $e->getMessage();
		} catch (Zend_Db_Select_Exception  $e){
			echo $e->getMessage();
		} catch (Zend_Db_Exception $e){
			echo $e->getMessage();
		}
        return $request;
    }
}
?>