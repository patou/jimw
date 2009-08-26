<?php
class Jimw_Form_Validate_Directory extends Zend_Validate_NotEmpty
{
    private $_rootPath = '';
    private $_createDirectory = false;
    /**
     * Validation key for not equal
     *
     */
    const DIRECTORY_NOT_EXIST = 'directoryNotExist';
    const DIRECTORY_CANT_CREATE = 'directoryCantCreate';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::DIRECTORY_NOT_EXIST => 'The directory didn\'t exist',
        self::DIRECTORY_CANT_CREATE => 'Can\'t create the directory'
    );

    public function __construct($rootPath = '', $createDirectory = false)
    {
        $this->_rootPath = $rootPath;
        $this->_createDirectory = $createDirectory;
    }
    
    public function isValid($value, $context = null)
    {
        /*if ($value == "/")
            return $value;*/
        if (parent::isValid($value, $context)) {
            if (!file_exists($this->_rootPath . $value)) {
                if (!$this->_createDirectory) {
                    $this->_error(self::DIRECTORY_NOT_EXIST);
                    return false;
                }
                if (!mkdir($this->_rootPath . $value,0777, true)) {
                    $this->_error(self::DIRECTORY_CANT_CREATE);
                    return false;
                }
            }
            if (!file_exists($this->_rootPath . $value)) {
                $this->_error(self::DIRECTORY_NOT_EXIST);
                return false;
            }
            return true;
        }
        return false;
    }
}
