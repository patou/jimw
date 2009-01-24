<?php
class Jimw_Form_Validate_Alias extends Zend_Validate_Alnum
{
    public function __construct($allowWhiteSpace = false)
    {
        $this->allowWhiteSpace = (boolean) $allowWhiteSpace;
        if (null === parent::$_filter) {
            parent::$_filter = new Jimw_Form_Filter_Alias();
        }

    }
    /*public function isValid($value)
    {
        $valueString = (string) $value;
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

        return true;
    }*/
}
