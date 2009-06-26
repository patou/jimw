<?php

class Jimw_Site_Module_Row extends Jimw_Db_Row
{
    /**
     * Configuration of the module
     * @var Zend_Config_Xml
     */
    protected $_xml = null;

    /**
     * Configuration in the database
     * @var Jimw_Db_Row
     */
    protected $_config = null;

    public function _loadConfiguration() {
        if ($this->_config === null) {
            $mconftable = new Jimw_Site_Moduleconfig();
            $site = Zend_Registry::get('site');
            if (!$site) return;
            $fetch = $mconftable->fetchRow($mconftable->select()
            			->where('module_id = ?', $this->id)
            			->where('site_id = ?', $site->id));
            if (!$fetch)
            {
                $fetch = $mconftable->fetchNew();
                $fetch->site_id = $site->id;
                $fetch->module_id = $this->id;
                $fetch->config = array();
                $fetch->save();
            }
            $this->_config = $fetch;
        }
    }

    public function _saveConfiguration() {
        if ($this->_config !== null) {
            $this->_config->save();
        }
    }

    public function _loadXMLConfiguration() {
        if ($this->_xml === null) {
            $dir = JIMW_REP_MODULE . $this->path . '/jimw.xml';
            if (file_exists($dir) && is_readable($dir))
                $this->_xml = new Zend_Config_Xml($dir);
            else
                throw new Jimw_Exception('This module didn\'t have a configuration file !');
        }
    }

    public function __get($name) {
        if ($name == 'xml')
        {
            $this->_loadXMLConfiguration();
            return $this->_xml;
        }
        if ($name == 'config')
        {
            $this->_loadConfiguration();
            return $this->_config->config;
        }
        return parent::__get($name);
    }

    public function __set($name, $value) {
        if ($name == 'config')
        {
            $this->_loadConfiguration();
            $this->_config->config = $value;
            $this->_saveConfiguration();
        }
        else
        {
            return parent::__set($name, $value);
        }
    }
}
?>