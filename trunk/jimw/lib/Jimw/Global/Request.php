<?php
/**
 * Jimw_Global_Request
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id:  $
 */
class Jimw_Global_Request extends Zend_Controller_Request_Http
{
	private $_domaineName = "";
	private $_subDomaine = "";
	private $_domainePort = "";
	private $_domaineProtocol = "";
	private $_path = "";
	private $_pageAlias = "";
	
	/**
     * Constructor
     *
     * If a $uri is passed, the object will attempt to populate itself using 
     * that information.
     * 
     * @param string|Zend_Uri $uri 
     * @return void
     * @throws Zend_Controller_Request_Exception when invalid URI passed
     */
	public function __construct($uri = null)
	{
		parent::__construct($uri);
		$this->setDomains();
		$this->setPath();
	}
	
	/**
	 * Return the sub domains name
	 *
	 * @return string The sub domain
	 */
	public function getSubDomaine ()
	{
		return $this->_subDomaine;
	}
	
	/**
	 * Return the domains name
	 *
	 * @return string The domain name
	 */
	public function getDomainName ()
	{
		return $this->_domaineName;
	}

		
	/**
	 * Return the domains port
	 *
	 * @return string The domain port
	 */
	public function getDomainPort ()
	{
		return $this->_domainePort;
	}

	/**
	 * Return the domains protocol
	 *
	 * @return string "https" if the protocol is securised or "http" otherwith
	 */
	public function getDomainProtocol ()
	{
		return $this->_domaineProtocol;
	}
	
	/**
	 * Return the jimw page alias
	 *
	 * @return string 
	 */
	public function getPageAlias ()
	{
		return $this->_pageAlias;
	}
	
	/**
	 * Return the jimw paths
	 *
	 * @return string paths
	 */
	public function getPath ()
	{
		return $this->_path;
	}
		
	/**
	 * Set Domain informations
	 *
	 */
	public function setDomains ()
	{
		$name = $this->get("SERVER_NAME");
		$tab_name = explode(".", $name);
		if (count($tab_name) >= 3) {
			$this->_subDomaine = $tab_name[0];
			$this->_domaineName = implode(".", array_slice($tab_name, 1));
		}
		else {
			$this->_subDomaine = "";
			$this->_domaineName = $name;
		}
		$this->_domainePort = $this->get("SERVER_PORT");
		$this->_domaineProtocol = ($this->get("HTTPS") == "on") ? "https" : "http";
	}
	
	
	/**
	 * Set Domain informations : Path, alias, params, 
	 *
	 */
	public function setPath ()
	{
		$uri = rtrim($this->getRequestUri(), "/");
		$tab_uri = explode("/", $uri);
		if ($uri[count($uri)] == "/") {
			$tab_uri[] = "index.html";
		}
		$nb = count($tab_uri);
		if ($nb == 1) {
			$this->_path = "";
			$this->_pageAlias = "";
		}
		else {
			$this->_pageAlias = $tab_uri[$nb - 1];
			$this->_path = implode("/", array_slice($tab_uri, 0, $nb - 1));;
		}
	}
}
?>