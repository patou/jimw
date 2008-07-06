<?php
// Configuration
define('DEFAULT_VERSION', 1);
class Jimw_Db_Update
{
    private $db;
    private $prefix = JIMW_PREFIX;

    public function __construct (Zend_Db_Adapter_Abstract $db, $prefix = JIMW_PREFIX)
    {
        $this->db = $db;
        $this->prefix = $prefix;
    }

    public function update_all() {
        $config = $this->db->getConfig();
        $dir = JIMW_REP_INSTALL_SQL . $this->get_database_type($config['type']) . '/' . JIMW_INSTALL_SQL_GLOBAL . '/';
        $version = $this->get_schema_version(JIMW_INSTALL_SQL_GLOBAL, JIMW_REP_INSTALL_SQL);
        echo "<br />--- Update global database ---<br />";
        $this->install_version(JIMW_INSTALL_SQL_GLOBAL, $dir, $version[JIMW_INSTALL_SQL_GLOBAL], $this->db);
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

    private function get_database_type ($type)
    {
        switch (strtoupper($type)) {
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

    private function get_prefix($name) {
        if ($this->prefix != '') {
            return ltrim($this->prefix, '_') . '_' . rtrim($name, '_');
        }
        return $name;
    }
}
?>
