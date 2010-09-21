<?php
/**
 * SiteController
 *
 * @author
 * @version
 */
class SiteController extends Jimw_Install_Action
{
    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {// TODO Auto-generated SiteController::indexAction() default action
        $this->_forward('list');
    }

    public function listAction ()
    {
        /**
		 * @var $db Zend_Db_Adapter_Abstract Database
		 */
        /*global $jimw_config_db;
        $db = Zend_Db::factory($jimw_config_db['type'], $jimw_config_db);
        $prefix = !empty($jimw_config_db['prefix']) ? $jimw_config_db['prefix'] . '_' : '';
        $this->view->list = $db->fetchAll($db->select()->from($prefix.'database'));*/
        $domains = new Jimw_Site_Domain();
        $this->view->list = $domains->fetchAll($domains->select()->where('domain_status = ?', 1));
    }

    public function createAction ()
    {
        $form = new Jimw_Install_SiteCreateForm();
        $req = $this->getRequest();
        if ($req->isPost() && $form->isValid($req->getPost())) {
            $val = $form->getValues();
            Jimw_Debug::dump($val);
            $database = $val['database'];
            if (JIMW_DEBUG)
                $database['profiler'] = true;
            $db = Zend_Db::factory($database['type'], $database);
            try {
                $db->getConnection();
                $message = false;
            } catch (Zend_Db_Exception $e) {
                $message = $e->getMessage();
            }
            $database['prefix'] = trim($database['prefix'], '_');
            if (! $message) {
                $update = new Jimw_Db_Update($db, $database['prefix']);
                $result = $update->update();
                Jimw_Debug::dump($result);
                if ($result['database']['success']) {
                    Zend_Registry::set('db', $db);
                    Zend_Registry::set('db_prefix', $database['prefix']);
                    $databases = new Jimw_Global_Database();
                    $base = $databases->createRow();
                    $base->name = $database['dbname'];
                    $base->server = $database['host'];
                    $base->user = $database['username'];
                    $base->pass = (! empty($database['password'])) ? $database['password'] : '';
                    $base->type = $database['type'];
                    $base->prefix = $database['prefix'];
                    $base->save();
                    $domains = new Jimw_Site_Domain();
                    $domain = $domains->createFromUrl($val['url']);
                    $domain->site_id = 1;
                    $domain->database_id = $base->id;
                    $domain->status = 1;
                    $domain->save();
                    $users = new Jimw_Site_User();
                    $user = $val['user'];
                    $new_user = $users->fetchNew();
                    $new_user->login = $user['login'];
                    $new_user->password = md5($user['password']);
                    $new_user->firstname = $user['firstname'];
                    $new_user->lastname = $user['lastname'];
                    $new_user->email = $user['mail'];
                    $new_user->status = 1;
                    $new_user->information = array();
                    $new_user->save();
                    $usergroups = new Jimw_Site_Usergroup();
                    $groups = $usergroups->fetchNew();
                    $groups->group_id = 3;
                    $groups->user_id = $new_user->id;
                    $groups->save();
                    $module = new Jimw_Site_Module();
                    $module->install('article');
                    $trees = new Jimw_Site_Tree();
                    $tree = $trees->fetchNew();
                    $tree->parentid = 0;
                    $tree->site_id = 0;
                    $tree->pagetitle = $this->_('Home');
                    $tree->description = $this->_('Welcome to your new website, you can now begin to edit it');
                    $tree->user_id = $new_user->id;
                    $tree->module_path = 'article';
                    $tree->save();
                    $sites = new Jimw_Site_Site();
                    $site = $sites->fetchNew();
                    $site->name = $val['title'];
                    $site->path = $val['path'];
                    $site->template = $val['template'];
                    $site->tree_id = 0;
                    $site->domain_id = $domain->id;
                    $site->default_tree_id = $tree->id;
                    $site->parentid = 0;
                    $site->save();
                    $tree->site_id = $site->id;
                    $tree->save();
                    $domain->site_id = $site->id;
                    $domain->save();
                    $this->view->message = 'Create ' . $val['title'] . ' site successful';
                    $this->render('create-success');
                }
                else {
                    $message = 'Error to create the database';
                }
            } else {
                $this->view->message = $message;
            }
        }
        $this->view->form = $form;
    }
}
?>

