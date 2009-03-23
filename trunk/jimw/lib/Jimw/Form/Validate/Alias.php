<?php
class Jimw_Form_Validate_Alias extends Zend_Validate_Alnum
{
    /**
     * Validation key for not equal
     *
     */
    const ALIAS_ALLREADY_EXIST = 'aliasAllreadyExist';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::ALIAS_ALLREADY_EXIST => 'This alias allready exist',
    );

    public function __construct($allowWhiteSpace = false)
    {
        $this->allowWhiteSpace = (boolean) $allowWhiteSpace;
        if (null === parent::$_filter) {
            parent::$_filter = new Jimw_Form_Filter_Alias();
        }

    }
    public function isValid($value, $context = null)
    {
        if (parent::isValid($value, $context) && is_array($context) && isset($context['tree_id'])) {
            $tree = new Jimw_Site_Tree();
            $id = $context['tree_id'];
            $select = $tree->select()->where('tree_alias = ?', $value);
            if (!empty($id))
                $select->where('tree_id <> ?', $id);
            $row = $tree->fetchAll($select);
            if (count($row) != 0) {
                $this->_error(self::ALIAS_ALLREADY_EXIST);
                return false;
            }
            return true;
        }
        return false;
      /*  $valueString = (string) $value;
        Jimw_Debug::dump('Alias'.$value);
        $this->_setValue($valueString);

        if ('' === $valueString) {
            $this->_error(self::STRING_EMPTY);
            return false;
        }
        $whiteSpace = $this->allowWhiteSpace ? '\s' : '';
        $pattern = '/[^-_\p{L}\p{N}' . $whiteSpace . ']/u';
        $new_value = preg_replace($pattern, '', (string) $value);

        self::$_filter->allowWhiteSpace = $this->allowWhiteSpace;

        if ($valueString !== self::$_filter->filter($valueString)) {
            $this->_error(self::NOT_ALNUM);
            return false;
        }

        return true;*/
    }
}
