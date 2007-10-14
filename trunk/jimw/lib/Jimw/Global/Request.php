<?php
/**
 * Jimw_Global_Request
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Global_Request extends Zend_Controller_Request_Http
{
	private $_domainName = '';
	private $_subDomain = '';
	private $_domainPort = '';
	private $_domainProtocol = '';
	private $_path = '';
	private $_pageAlias = '';
	private $_tree = null;

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
	}

	/**
	 * Return the sub domains name
	 *
	 * @return string The sub domain
	 */
	public function getSubDomain ()
	{
		return $this->_subDomain;
	}

	/**
	 * Set the sub domain name
	 *
	 * @return Jimw_Global_Request
	 */
	public function setSubDomain ($subDomain)
	{
		$this->_subDomain = $subDomain;
		return $this;
	}

	/**
	 * Return the tree
	 *
	 * @return Jimw_Tree
	 */
	public function getTree ()
	{
		return $this->_tree;
	}

	/**
	 * Set the tree
	 *
	 * @return Jimw_Global_Request
	 */
	public function setTree ($tree)
	{
		$this->_tree = $tree;
		return $this;
	}

	/**
	 * Return the domains name
	 *
	 * @return string The domain name
	 */
	public function getDomainName ()
	{
		return $this->_domainName;
	}

	/**
	 * Set the domain name
	 *
	 * @return Jimw_Global_Request
	 */
	public function setDomainName ($domain)
	{
		$this->_domainName = $domain;
		return $this;
	}

	/**
	 * Return the domains port
	 *
	 * @return string The domain port
	 */
	public function getDomainPort ()
	{
		return $this->_domainPort;
	}

	/**
	 * Set the domain port
	 *
	 * @return Jimw_Global_Request
	 */
	public function setDomainPort ($port)
	{
		$this->_domainPort = $port;
		return $this;
	}

	/**
	 * Return the domains protocol
	 *
	 * @return string "https" if the protocol is securised or "http" otherwith
	 */
	public function getDomainProtocol ()
	{
		return $this->_domainProtocol;
	}

	/**
	 * Set the domain protocol
	 *
	 * @return Jimw_Global_Request
	 */
	public function setDomainProtocol ($protocol)
	{
		$this->_domainProtocol = $protocol;
		return $this;
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
	 * Set the jimw page alias
	 *
	 * @return Jimw_Global_Request
	 */
	public function setPageAlias ($pageAlias)
	{
		$this->_pageAlias = $pageAlias;
		return $this;
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
	 * Set the jimw paths
	 *
	 * @return string path
	 */
	public function setPath ($path)
	{
		$this->_path = $path;
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
			$this->_subDomain = $tab_name[0];
			$this->_domainName = implode(".", array_slice($tab_name, 1));
		}
		else {
			$this->_subDomain = "";
			$this->_domainName = $name;
		}
		$this->_domainPort = $this->get("SERVER_PORT");
		$this->_domainProtocol = ($this->get("HTTPS") == "on") ? "https" : "http";
		return $this;
	}
	
	
}
?>