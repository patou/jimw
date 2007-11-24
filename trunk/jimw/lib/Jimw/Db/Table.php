<?php
/**
 * Jimw_Db_Table
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Jimw_Db_Table extends Zend_Db_Table
{
	protected $_rowClass = 'Jimw_Db_Row';
	protected $_rowsetClass = 'Jimw_Db_Rowset';
	protected $_tableName = '';
	const PREFIX = 'prefix';
	protected $_prefix = 'jimw';
	
	   /**
     * Constructor.
     *
     * Supported params for $config are:
     * - db              = user-supplied instance of database connector,
     *                     or key name of registry instance.
     * - name            = table name.
     * - primary         = string or array of primary key(s).
     * - rowClass        = row class name.
     * - rowsetClass     = rowset class name.
     * - referenceMap    = array structure to declare relationship
     *                     to parent tables.
     * - dependentTables = array of child tables.
     * - metadataCache   = cache for information from adapter describeTable().
     *
     * @param  mixed $config Array of user-specified config options, or just the Db Adapter.
     * @return void
     */
    public function __construct($config = array())
    {
    	if (isset($config[self::PREFIX])) {
			$this->_prefix = $config[self::PREFIX];
		}
		else {
			$this->_prefix = Zend_Registry::get('db_prefix');
		}
		parent::__construct($config);
    }
	/**
	 * Setup the table name and add the db prefix before the table name
	 *
	 */
	protected function _setupTableName()
    {
    	
    	parent::_setupTableName();
        if (!strpos($this->_name, $this->_prefix)) {
        	$this->_tableName = $this->_name;
        	if (!empty($this->_prefix))
        		$this->_name = $this->_prefix . '_' . $this->_name;
        }
        else {
        	$this->_tableName = substr($this->_name, strlen($this->_prefix));
        }
    }

    /**
     * Get the real table name without the prefix
     *
     * @return string
     */
    public function getTableName () {
    	return $this->_tableName;
    }

    /**
     * Transform a column name from the user-specified form
     * to the physical form used in the database.
     * You can override this method in a custom Row class
     * to implement column name mappings, for example inflection.
     *
     * @param string $columnName Column name given.
     * @return string The column name after transformation applied (none by default).
     * @throws Zend_Db_Table_Row_Exception if the $columnName is not a string.
     */
    protected function _transformColumn($columnName)
    {
        if (!is_string($columnName)) {
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception('Specified column is not a string');
        }
        if (!strpos($columnName, '_')) {
	        $prefix = $this->_tableName;
        	if (!empty($prefix)) {
       			$columnName = $prefix . '_' . $columnName;
        	}
        }
        return $columnName;
    }

	public function insert(array $data)
    {
        // add a timestamp
        /**$creationdate = $this->_transformColumn('creationdate');
        if (empty($data[$creationdate]))
        	$data[$creationdate] = time ();
        $editiondate = $this->_transformColumn('editiondate');
        if (empty($data[$editiondate]))
        	$data[$editiondate] = time();*/
        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        // add a timestamp
        /**$editiondate = $this->_transformColumn('editiondate');
        if (empty($data[$editiondate]))
        	$data[$editiondate] = time();*/
        return parent::update($data, $where);
    }
}
?>