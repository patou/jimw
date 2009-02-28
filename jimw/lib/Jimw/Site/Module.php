<?php
/**
 * Jimw_Site_Module
 *
 * @author	   Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Site_Module extends Jimw_Db_Table
{
    protected $_name = 'module';
    protected $_dependentTables = array('Jimw_Site_Site');
    protected $_rowClass = 'Jimw_Site_Module_Row';

    /**
     * Install a new module to the
     */
    public function install($module_name) {
        if (is_readable(JIMW_REP_MODULE . '/' . $module_name . '/jimw.xml')) {
            $result = $this->fetchRow($this->select()->where('module_path = ?', $module_name));
            if ($result) //Si le module est déjà ajouter on ne l'ajoute pas à nouveau
                return; //TODO Display error or not ?
            $xml = new Zend_Config_Xml(JIMW_REP_MODULE . '/' . $module_name . '/jimw.xml');
            $update = new Jimw_Db_Update();
            $result = $update->updateModule($module_name);
            if (isset($result['noSQL']) || $result['database']['success'])
            {
                $new = $this->fetchNew();
                $new->path = $module_name;
                $new->name = $xml->name;
                $new->comment = $xml->comment;
                $new->version = $xml->version;
                $new->author = $xml->author;
                $new->save();
            }
        }
        else {
            throw new Jimw_Admin_Exception('This module didn\'t have a config file');
        }
    }
}