<?php
class Jimw_Form_Filter_Alias extends Zend_Filter_Alnum
{
    protected static $_meansEnglishAlphabet = true;
    
    public function filter($value)
    {
        if ($value == '/')
            return $value;
        $whiteSpace = $this->allowWhiteSpace ? '\s' : '';
        if (!self::$_unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z0-9 match
            $pattern = '/[^a-zA-Z0-9' . $whiteSpace . '_-]/';
        } else if (self::$_meansEnglishAlphabet) {
        	//The Alphabet means english alphabet.
            $pattern = '/[^a-zA-Z0-9'  . $whiteSpace . '_-]/u';
        } else {
        	//The Alphabet means each language's alphabet.
            $pattern = '/[^\p{L}\p{N}' . $whiteSpace . '_-]/u';
        }
        return preg_replace($pattern, '', (string) $value);
    }
}
?>