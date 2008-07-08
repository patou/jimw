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
}
    public function listAction ()
    {}
    public function createAction ()
    {
        $form = new Jimw_Install_SiteCreateForm();
        Jimw_Debug::dump($this->getRequest()->getPost());
        if ($form->isValid($this->getRequest()->getPost())) {
            $val = $form->getValues();
            $database = $val['database'];
            Jimw_Debug::dump($database);
            if (JIMW_DEBUG_MODE) $database['profiler'] = true;
            $db = Zend_Db::factory($database['type'], $database);
            try {
                $db->getConnection();
                $message = false;
            }
            catch (Zend_Db_Exception $e) {
                $message = $e->getMessage();
            }
            if (!$message) {
                $databases = new Jimw_Global_Database();
                $base = $databases->createRow();
                $base->name = $database['dbname'];
                $base->server = $database['host'];
                $base->user = $database['username'];
                $base->pass = !empty($database['password'])?$database['password']:'';
                $base->type = $database['type'];
                $base->prefix = $database['prefix'];
                $base->save();
                $url = Zend_Uri::factory($val['url']);
                Jimw_Debug::dump($url);
                $domains = new Jimw_Site_Domain();
                $domain = $domains->fetchNew();
                $domain->site_id = 1;
                $domain->database_id = $base->id;
                $host = $url->getHost();
                $tab_name = explode(".", $host);
                if (count($tab_name) >= 3) {
                    $domain->subdomain = $tab_name[0];
                    $domain->name = implode(".", array_slice($tab_name, 1));
                }
                else {
                    $domain->subdomain = "";
                    $domain->name = $host;
                }
                $domain->port = is_int($port = $url->getPort())?$port: 80;
                $domain->protocol = $url->getScheme();
                $domain->path = trim($url->getPath(), '/');
                $update = new Jimw_Db_Update($db, $database['prefix']);
                ob_start();
                $update->update();
                Jimw_Debug::display(ob_get_clean());
                Zend_Registry::set('db', $db);
                Zend_Registry::set('db_prefix', $database['prefix']);
                $users = new Jimw_Site_User();
                $user = $val['user'];
                $new_user = $users->fetchNew();
                $new_user->login = $user['login'];
                $new_user->password = md5($user['password']);
                $new_user->firstname = $user['firstname'];
                $new_user->lastname = $user['lastname'];
                $new_user->email = $user['mail'];
                $new_user->status = 1;
                $new_user->save();
                $sites = new Jimw_Site_Site();
                $site = $sites->fetchNew();
                $site->name = $val['title'];
                $site->path = JIMW_REP_PUBLIC;
                $site->tree_id = 1;
                $site->save();
                $domain->site_id = $site->id;
                $domain->save();
            }
            else {
                $this->view->message = $message;
            }
        }
        $this->view->form = $form;
    }
}
?>

