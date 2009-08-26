<?php
class Jimw_Form_Element_Template extends Zend_Form_Element_Select
{
    protected function _getMultiOptions ()
    {
        if (! count($this->options)) {
            $templates = array();
            $dir_iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(JIMW_REP_PUBLIC), RecursiveIteratorIterator::SELF_FIRST);
            foreach ($dir_iterator as $dir) {
                if ($dir->isDir() && file_exists($dir->getPathname() . '/layout.phtml')) {
                    $path = trim(str_replace('\\', '/', $dir->getPathname()), '../');
                    $templates[$path] = trim(str_replace('\\', '/', $dir->getPathname()), '../');
                }
            }
            $this->options = $templates;
        }
        return $this->options;
    }
}