<?php
// Configuration
define('DEFAULT_VERSION', 0);
class Jimw_Db_Update
{
    /** @var Zend_Db_Adapter_Abstract $db */
    private $db;
    private $prefix = JIMW_PREFIX;
    public function __construct (Zend_Db_Adapter_Abstract $db = null, $prefix = '')
    {
        if (is_null($db) && Zend_Registry::get('db')) {
            $db = Zend_Registry::get('db');
        }
        $this->db = $db;
        if (empty($prefix)) {
            $prefix = Zend_Registry::get('db_prefix') ? Zend_Registry::get('db_prefix') : JIMW_PREFIX;
        }
        $this->prefix = $prefix;
    }
    public function update_all ()
    {
        $dir = JIMW_REP_INSTALL_SQL . $this->get_database_type() . '/' . JIMW_INSTALL_SQL_GLOBAL . '/';
        $version = $this->get_schema_version(JIMW_INSTALL_SQL_GLOBAL, JIMW_REP_INSTALL_SQL);
        Jimw_Debug::display("--- Update global database ---");
        $result = array();
        $result['global'] = $this->install_version(JIMW_INSTALL_SQL_GLOBAL, $dir, $version[JIMW_INSTALL_SQL_GLOBAL], $this->db);
        $select = $this->db->select();
        $select->from($this->get_prefix('database'));
        $databases = $this->db->fetchAll($select);
        $result['database'] = array();
        foreach ($databases as $database) {
            Jimw_Debug::display("-- Update database {$database['database_id']}--");
            $options = array('dbname' => $database['database_name'] , 'host' => $database['database_server'] , 'port' => $database['database_port'] , 'username' => $database['database_user'] , 'password' => $database['database_pass'] , 'type' => $database['database_type']);
            if (JIMW_DEBUG) {
                $options['profiler'] = true;
            }
            $db = Zend_Db::factory($database['database_type'], $options);
            $prefix = $database['database_prefix'];
            $update = new self($db, $prefix);
            $result['database'][$database['database_id']] = $update->update();
            Jimw_Debug::display("-- End update database {$database['database_id']}--");
        }
        return $result;
    }
    public function update ()
    {
        $result = array();
        $type = $this->get_database_type();
        $dir = JIMW_REP_INSTALL_SQL . $type . '/' . JIMW_INSTALL_SQL_DATABASE . '/';
        $version = $this->get_schema_version(JIMW_INSTALL_SQL_DATABASE, JIMW_REP_INSTALL_SQL);
        Jimw_Debug::display("--- Update database ---");
        $result['database'] = $this->install_version(JIMW_INSTALL_SQL_DATABASE, $dir, $version[JIMW_INSTALL_SQL_DATABASE], $this->db);
        $select = $this->db->select();
        $select->from($this->get_prefix('module'));
        $modules = $this->db->fetchAll($select);
        $result['modules'] = array();
        foreach ($modules as $module) {
            $module_name = $module['module_path'];
            Jimw_Debug::display("-- Update database for module {$module_name}--");
            $result['modules'][$module_name] = $this->updateModule($module_name, $version);
            Jimw_Debug::display("-- End update database for module {$module_name}--");
        }
        return $result;
    }

    public function updateModule($module_name, $version = null)
    {
        if (is_null($version)) {
            $version = $this->get_schema_version(JIMW_INSTALL_SQL_DATABASE, JIMW_REP_INSTALL_SQL);
        }
        $module_dir = JIMW_REP_MODULE . '/' . $module_name . '/install/sql/' . $this->get_database_type() . '/';
        $result = array();
        $result['directory'] = $module_dir;
        if (file_exists($module_dir)) {
            $module_version = (isset($version[$module_name])) ? $version[$module_name] : DEFAULT_VERSION;
            $result['database'] = $this->install_version($module_name, $module_dir, $module_version);
        }
        else {
            $result['noSQL'] = true;
        }
        return $result;
    }

    public function get_schema_version ($default = 'global', $dir = JIMW_REP_INSTALL_SQL)
    {
        $version[$default] = DEFAULT_VERSION;
        try {
            $schemas = $this->db->fetchAll($this->db->select()->from($this->get_prefix('schema'), '*'));
            foreach ($schemas as $schema) {
                $version[$schema['schema_module']] = $schema['schema_version'];
            }
        } catch (Zend_Db_Exception $e) {
            Jimw_Debug::display_exception($e);
            Jimw_Debug::display($dir . 'schema.sql');
            $this->install_sql($dir . 'schema.sql');
        }
        return $version;
    }
    public function get_version_list ($path)
    {
        $list = array();
        try {
            $dir = new DirectoryIterator($path);
            foreach ($dir as $file) {
                if (! $dir->isDot()) {
                    $result = array();
                    if (ereg('^([0-9]{3})-(.*).sql$', $file->getFilename(), $result)) {
                        $list[intval($result[0])] = $path . $file->getFilename();
                    }
                }
            }
            if (file_exists($path . 'install.sql'))
                $list[0] = $path . 'install.sql';
        } catch (Exception $e) {
            Jimw_Debug::display_exception($e);
        }
        return $list;
    }
    /**
     * Insert in the database an SQL file
     */
    public function install_sql ($file)
    {
        $result = array();
        $result['file'] = $file;
        $sql_file = @file_get_contents($file);
        if (! $sql_file) {
            return false;
        }
        $all_sql = explode(";", $sql_file);
        $result['sql'] = array();
        $i = 0;
        foreach ($all_sql as $sql) {
            $sql = str_replace('{PREFIX}', $this->get_prefix(''), trim($sql));
            $result['sql'][$i]['query'] = $sql;
            if (! empty($sql)) {
                try {
                    if (JIMW_DEBUG)
                        Jimw_Debug::display($sql);
                    $stm = $this->db->query($sql);
                    $result['sql'][$i]['result'] = $stm;
                    //$stm->execute();
                } catch (Zend_Db_Exception $e) {
                    Jimw_Debug::display_exception($e);
                    $result['success'] = false;
                    return $result;
                }
            }
            $i++;
        }
        $result['success'] = true;
        return $result;
    }

    public function update_version ($module, $version)
    {
        try {
            $sel = $this->db->fetchOne($this->db->select()->from($this->get_prefix('schema'), 'schema_version')->where('schema_module = ?', $module));
            Jimw_Debug::display("** Update $module to version $version **");
            if ($sel)
                $this->db->update($this->get_prefix('schema'), array('schema_version' => $version), $this->db->quoteInto('schema_module = ?', $module));
            else
                $this->db->insert($this->get_prefix('schema'), array('schema_version' => $version , 'schema_module' => $module));
            return true;
        } catch (Zend_Db_Exception $e) {
            Jimw_Debug::display_exception($e);
        }
        return false;
    }

    public function install_version ($module, $path, $cur_version = 0)
    {
        $result = array();
        $list = $this->get_version_list($path);
        $version = $cur_version;
        $result['currentVersion'] = $result['newVersion'] = $cur_version;
        $result['update'] = array();
        if ($cur_version == 0 && !empty($list[$version])) {
            Jimw_Debug::display("Install ... ");
            $result['update'][$version] = $this->install_sql($list[$version]);
            if (!$result['update'][$version]['success'])
            {
                $result['newVersion'] = 0;
                $result['success'] = false;
                return $result;
            }
        }
        else {
            for (; isset($list[$version]); $version ++) {
                Jimw_Debug::display("Install version $version ... ");
                $result['update'][$version] = $this->install_sql($list[$version]);
                if (!$result['update'][$version]['success'])
                {
                    $result['newVersion'] = --$version;
                    if (!$this->update_version($module, $version))
                        $result['newVersion'] = $cur_version;
                    $result['success'] = false;
                    return $result;
                }
            }
        }
        if ($version == $cur_version) {
            $version = max(array_keys($list)) + 1;
        }
        $result['newVersion'] = $version;
        $result['success'] = $this->update_version($module, $version);
        return $result;
    }

    private function get_database_type ()
    {
        $config = $this->db->getConfig();
        if (isset($config['type'])) {
            switch (strtoupper($config['type'])) {
                case 'PDO_MYSQL':
                case 'MYSQLI':
                case 'MYSQL':
                    return 'mysql';
                case 'PDO_SQLITE':
                case 'SQLITE':
                    return 'sqlite';
                default:
                    return strtolower($config['type']);
            }
        } else {
            $type = (method_exists($this->db, 'getAdapter'))?get_class($this->db->getAdapter()):get_class($this->db);
            Jimw_Debug::dump($type);
            switch ($type) {
                case 'Zend_Db_Adapter_Pdo_Mysql':
                case 'Zend_Db_Adapter_Mysqli':
                    return 'mysql';
                case 'Zend_Db_Adapter_Pdo_Sqlite':
                case 'Zend_Db_Adapter_Sqlite':
                    return 'sqlite';
                default:
                    return strtolower($type);
            }
        }
    }
    private function get_prefix ($name)
    {
        if ($this->prefix != '') {
            return ltrim($this->prefix, '_') . '_' . rtrim($name, '_');
        }
        return $name;
    }
}
?>
