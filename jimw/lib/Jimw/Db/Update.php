<?php
// Configuration
define('DEFAULT_VERSION', 1);
class Jimw_Db_Update
{
	/** @var Zend_Db_Adapter_Abstract $db */
    private $db;
    private $prefix = JIMW_PREFIX;

    public function __construct (Zend_Db_Adapter_Abstract $db = null, $prefix = '')
    {
        if (is_null($db) && Zend_Registry::has('db')) {
            $db = Zend_Registry::get('db');
        }
        $this->db = $db;
        if (empty($prefix)) {
            $prefix = Zend_Registry::has('db_prefix') ? Zend_Registry::get('db_prefix'):JIMW_PREFIX;
        }
        $this->prefix = $prefix;
    }

    public function update_all() {
        $dir = JIMW_REP_INSTALL_SQL . $this->get_database_type() . '/' . JIMW_INSTALL_SQL_GLOBAL . '/';
        $version = $this->get_schema_version(JIMW_INSTALL_SQL_GLOBAL, JIMW_REP_INSTALL_SQL);
        echo "<br />--- Update global database ---<br />";
        $this->install_version(JIMW_INSTALL_SQL_GLOBAL, $dir, $version[JIMW_INSTALL_SQL_GLOBAL], $this->db);
        $select = $this->db->select();
		$select->from($this->get_prefix('database'));
		$databases = $this->db->fetchAll($select);
		foreach ($databases as $database) {
			echo "<br />-- Update database {$database['database_id']}--<br />";
			$options = array('dbname' => $database['database_name'],
					'host' => $database['database_server'],
					'port' => $database['database_port'],
					'username' => $database['database_user'],
					'password' => $database['database_pass'],
					'type' => $database['database_type']);
			if (JIMW_DEBUG_MODE) {
    			$options['profiler'] = true;
    		}
			$db = Zend_Db::factory($database['database_type'], $options);
			$prefix = $database['database_prefix'];
			$update = new self($db, $prefix);
			$update->update();
			echo "<br />-- End update database {$database['database_id']}--";        
    	}
    }

    public function update() {
        $type = $this->get_database_type();
        $dir = JIMW_REP_INSTALL_SQL . $type . '/' . JIMW_INSTALL_SQL_DATABASE . '/';
        Jimw_Debug::display($dir);
        $version = $this->get_schema_version(JIMW_INSTALL_SQL_DATABASE, JIMW_REP_INSTALL_SQL);
        Jimw_Debug::dump($version);
        echo "<br />--- Update database ---<br />";
        $this->install_version(JIMW_INSTALL_SQL_DATABASE, $dir, $version[JIMW_INSTALL_SQL_DATABASE], $this->db);
  		$select = $this->db->select();
		$select->from($this->get_prefix('module'));
		$modules = $this->db->fetchAll($select);
		foreach ($modules as $module) {
			$module_name = $module['module_path'];
			$module_dir = JIMW_REP_MODULE . '/' . $module_name . '/install/sql/' . $this->get_database_type() . '/';
			if (file_exists($module_dir)) {
				echo "<br />-- Update database for module {$module_name}--<br />";
				$module_version = (isset($version[$module_name])) ? $version[$module_name] : DEFAULT_VERSION;				
				$this->install_version($module_name, $module_dir, $module_version);
				echo "<br />-- End update database for module {$module_name}--<br />";
			}
		}
    }

    public function get_schema_version ($default = 'global', $dir = JIMW_REP_INSTALL_SQL)
    {
        $version[$default] = DEFAULT_VERSION;
        try {
            $schemas = $this->db->fetchAll($this->db->select()->from($this->prefix . '_schema', '*'));
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
        } catch (Exception $e) {
            Jimw_Debug::display_exception($e);
        }
        return $list;
    }

    public function install_sql ($file)
    {
        $sql_file = @file_get_contents($file);
        if (! $sql_file) {
            return false;
        }
        $all_sql = explode(";", $sql_file);
        foreach ($all_sql as $sql) {
            $sql = str_replace('{PREFIX}', $this->get_prefix(''), trim($sql));
            if (! empty($sql)) {
                try {
                    if (JIMW_DEBUG_MODE)
                        Jimw_Debug::display($sql);
                    $stm = $this->db->query($sql);
                    //$stm->execute();
                } catch (Zend_Db_Exception $e) {
                    Jimw_Debug::display_exception($e);
                    return false;
                }
            }
        }
        return true;
    }

    public function update_version ($module, $version)
    {
        try {
            $sel = $this->db->fetchOne($this->db->select()->from($this->get_prefix('schema'), 'schema_version')->where('schema_module = ?', $module));
            echo "** Update $module to version $version **";
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
        $list = $this->get_version_list($path);
        $version = $cur_version;
        for (; isset($list[$version]); $version++) {
            echo "Install version $version ... ";
            if ($this->install_sql($list[$version]))
                echo "<font color=green>OK</font><br />"; else
                return false;
        }
        if ($version == $cur_version) {
            $version = max(array_keys($list)) + 1;
        }
        return $this->update_version($module, $version);
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
                    return strtolower($type);
            }
        }
        else {
            switch (get_class($this->db->getAdapter())) {
                case 'Zend_Adapter_Pdo_Mysql':
                case 'Zend_Adapter_Mysqli':
                    return 'mysql';
                case 'Zend_Adapter_Pdo_Sqlite':
                case 'Zend_Adapter_Sqlite':
                    return 'sqlite';
                default:
                    return strtolower($type);
            }
        }
    }

    private function get_prefix($name) {
        if ($this->prefix != '') {
            return ltrim($this->prefix, '_') . '_' . rtrim($name, '_');
        }
        return $name;
    }
}
?>
