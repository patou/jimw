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
    private function testConnection (Jimw_Db_Row $param)
    {
        $db_param = array('dbname' => $param->name , 'host' => $param->server , 'username' => $param->user , 'password' => $param->pass);
        if (JIMW_DEBUG_MODE) {
            $db_param['profiler'] = true;
        }
        try {
            $db = Zend_Db::factory($param->type, $db_param);
            $db->getConnection();
            Zend_Db_Table::setDefaultAdapter($db);
            Zend_Registry::set('db', $db);
            Zend_Registry::set('database', $param);
            Zend_Registry::set('db_prefix', $param->prefix);
            if (JIMW_UTF8) {
                try {
                    $db->query('SET NAMES UTF8'); // Use UTF8 for Mysql
                } catch (Exception $e) {
                    Jimw_Debug::display_exception($e);
                }
            }
        } catch (Zend_Db_Adapter_Exception $e) {
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
    public function route (Zend_Controller_Request_Abstract $request)
    {
        if (! $request instanceof Jimw_Global_Request) {
            throw new Zend_Controller_Router_Exception('Jimw_Global_Router requires a Jimw_Global_Request-based request object');
        }
        $jimw_config_db = Zend_Registry::get('config_db');
        if (JIMW_DEBUG_MODE) {
            $jimw_config_db['profiler'] = true;
        }
        $db = Zend_Db::factory($jimw_config_db['type'], $jimw_config_db);
        Zend_Registry::set('db_global', $db);
        $domains = new Jimw_Site_Domain();
        $select = $domains->select()
            ->where('domain_name = ?', $request->getDomainName())
            ->where('domain_port = ?', $request->getDomainPort())
            ->where('domain_protocol = ?', $request->getDomainProtocol())
            ->where('domain_subdomain = ?', $request->getSubDomain())
            ->order('domain_path DESC')
            ->order('domain_id DESC');
        $domain_list = $domains->fetchAll($select);
        $uri = trim($request->getPathInfo(), '/');//
            //trim($request->getRequestUri(), '/');
        //$uri = substr($uri, strlen($request->getBaseUrl()));
        //Jimw_Debug::display($uri);
        foreach ($domain_list as $domain) {
            $path = trim($domain->path, '/');
            
            //Jimw_Debug::display($path);
            if (empty($path) || strpos($uri, $path) === 0) {
                $databases = new Jimw_Global_Database();
                $database = $databases->find($domain->database_id);
                if (count($database)) {
                    $pathInfo = (! empty($path)) ? substr($uri, strlen('/' . $path)) : $uri;
                    //Jimw_Debug::display($pathInfo);
                    if ($pathInfo !== false) {
                        $request->setPathInfo($pathInfo);
                    } else {
                        $request->setPathInfo('/');
                    }
                    $this->testConnection($database->current());
                    $site = $domain->findParentJimw_Site_Site();
                    //Jimw_Debug::dump($site);
                    Zend_Registry::set('current_domain', $domain);
                    Zend_Registry::set('site', $site);
                    Zend_Registry::set('site_path', $site->path);
                    $request->setParam('site_path', $site->path);
                    return $request;
                }
            }
        }
        throw new Jimw_Global_Exception('Unknown website', 404);
    }
    public function assemble ($userParams, $name = null, $reset = false, $encode = true)
    {//@TODO
}
}
