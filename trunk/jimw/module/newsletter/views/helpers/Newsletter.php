<?php
/**
 * Blog_View_Helper_MenuRSS
 *
 * @author	    Patou
 * @category   Jimw_Core
 * @package    Jimw
 * @copyright  Copyright (c) 2006-2007 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */
class Newsletter_View_Helper_Newsletter extends Zend_View_Helper_Abstract
{
   public function newsletter() {
   		return new newsletter_Form_NewsletterForm();
   }
}