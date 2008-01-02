<?php


class Jimw_Plugin_Site extends Zend_Controller_Plugin_Abstract {
    /**
     * Called before Zend_Controller_Router exits.
     *
     * Called before Zend_Controller_Front exits from the router.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
      $front = Zend_Controller_Front::getInstance ();
      $router = $front->getRouter();
      $route = new Zend_Controller_Router_Route_Static('?id_local=18', array('alias' => 'accueil'));
      $router->addRoute('rcf', $route);
    }

}

?>
