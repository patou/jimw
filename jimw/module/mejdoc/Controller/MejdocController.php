<?php
/**
 * MejdocController
 *
 * @author	   Logisim
 * @category   Mej_Doc
 * @package    Mej_Module
 * @copyright  Copyright (c) 2006-2008 jimw.fr
 * @license    http://www.jimw.fr
 * @version    $Id$
 */

include_once('Model/Mejdoc.php');
class Mejdoc_MejdocController extends Jimw_Module_Action_Alias
{
	public function viewModule ($alias)
	{
		$request = $this->_request;
		$tree = $request->getTree();
		
		// Current documents
		$documents = new Mejdoc();
		$result = $documents->fetchAll(array('tree_id = ?' => $tree->id), 'mejdoc_order');
		$this->view->documents = $result;
		
		// Sub documents
		$trees = new Jimw_Site_Tree();
		$result = $trees->fetchAllChildren($tree->id);
		$subdocs = array();
		foreach ($result as $subtree) {
			if ($subtree->id != $tree->id) {
				$subresult = $documents->fetchAll(array('tree_id = ?' => $subtree->id), 'mejdoc_order');
				if (count($subresult)>0) $subdocs[] = array('title' => $subtree->pagetitle, 'alias' => $subtree->alias, 'description' => $subtree->description, 'documents' => $subresult);
			}
		}
		$this->view->subdocuments = $subdocs;

		$this->view->tree = $tree;
		$this->render('mejdoc');
	}
}
?>