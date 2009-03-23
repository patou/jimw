<?php
class Jimw_Acl extends Zend_Acl
{
    const GESTS = 'Gests';
    const MEMBERS = 'Members';
    const ADMINISTRATORS = 'Administrators';
	public function init() {
		$this->buildRoleList();
		$this->buildRessourceList();
        $this->buildPermissionList();
        $this->buildDefaultAdminRole();
	}

    public function isRoleAllowed($resource = null, $privilege = null) {
        return $this->isAllowed(null, $resource, $privilege);
	}

	public function isAllowed($role = null, $resource = null, $privilege = null) {
        if ($role == null)
            $role = $this->loadUserRole();
        return parent::isAllowed($role, $resource, $privilege);
	}

	public function buildRoleList() {
	    $groups = new Jimw_Site_Group();
        $group_list = $groups->fetchAll();
        $list = array();
        foreach ($group_list as $group) {
            $parent = array();
            if (!empty($group->parents))
                $parent = explode(',', $group->parents);
            $list[$group->name] = $parent;
        }
        foreach ($list as $name => $parent) {
            $this->_buildRole($name, $list);
        }
	}

	private function _buildRole($role, &$list) {
	    if (is_string($role) && !$this->hasRole($role)) {
	        $parent = $list[$role];
            foreach ($parent as $par) {
                $this->_buildRole($par, $list);
            }
            if ($this->hasRole($role))
                throw new Jimw_Exception('circular groups parents');
            if (empty($parent))
                $this->addRole(new Zend_Acl_Role($role));
            else
                $this->addRole(new Zend_Acl_Role($role), $parent);
        }
	}

	public function buildRessourceList() {
        $list = $this->getRessourcelist();
        Jimw_Debug::dump($list);
        foreach ($list as $ressource => $permissions) {
            $res = new Zend_Acl_Resource($ressource);
            if (!$this->has($res)) {
                $parent = NULL;
                if (isset($permissions['inherit'])) {
                    $parent = $permissions['inherit'];
                    unset($permissions['inherit']);
                }
                $this->add($res, $parent);
            }
            $value = $permissions;
            if (isset($permissions['default'])) {
                $value = $permissions['default'];
                unset($permissions['default']);
            }
            if (isset($permissions['label'])) {
                unset($permissions['label']);
            }
            if (isset($permissions['all'])) {
                $value = (isset($permissions['all']['default'])) ? $permissions['all']['default'] : $permissions['all'];
                unset($permissions['all']);
            }
            $value = $this->formatValuePermission($value);
            if (!is_array($value) && $value !== null) {
                    $this->setRule(Zend_Acl::OP_ADD, $value, null, $res);
                    $this->allow(self::ADMINISTRATORS, $res);
            }
            else {
                foreach ($permissions as $permission => $type) {
                    $value = (isset($type['default']) ? $type['default'] : $type);
                    $type = $this->formatValuePermission($value);
                    if ($type === Zend_Acl::TYPE_ALLOW || $type === Zend_Acl::TYPE_DENY) {
                        $this->setRule(Zend_Acl::OP_ADD, $type, null, $res, $permission);
                        $this->allow(self::ADMINISTRATORS, $res, $permission);
                    }
                }
            }
        }
	}

	public function buildPermissionList() {
	    $site = Zend_Registry::get('site');
        $siteperm = new Jimw_Site_Siteperm();
        $groups = new Jimw_Site_Group();
        $sitesperms = $siteperm->fetchAll($siteperm->select()
            ->from($siteperm->getRealTableName())
            ->joinNatural($groups->getRealTableName(), array('group_name'))
            ->setIntegrityCheck(false)
            ->where('site_id = ?', $site->id));
        foreach ($sitesperms as $siteperm) {
            $perms = $siteperm->perm;
            foreach ($perms as $ressource => $permissions) {
                $res = new Zend_Acl_Resource($ressource);
                if (!is_array($permissions))
                    continue;
                if (!$this->has($res))
                    $this->add($res);
                foreach ($permissions as $permission => $type) {
                    if ($type === Zend_Acl::TYPE_ALLOW || $type === Zend_Acl::TYPE_DENY) {
                        if ($permission === 'all') {
                            $this->setRule(Zend_Acl::OP_ADD, $type, $siteperm->group_name, $res);
                        }
                        else {
                            $this->setRule(Zend_Acl::OP_ADD, $type, $siteperm->group_name, $res, $permission);
                        }

                    }
                }
            }
        }
	}

	public function getRessourcelist() {
        $list = array();
        $global_ressources = new Zend_Config_Xml(JIMW_REP_CONFIG . 'permissions.xml');
        foreach ($global_ressources as $ressource => $permission) {
            $list[$ressource] = $permission->toArray();
        }
        $modules = new Jimw_Site_Module();
        $modules_list = $modules->fetchAll();
        foreach ($modules_list as $module) {
            $xml = $module->xml;
            if (isset($xml->permissions)) {
                foreach ($xml->permissions as $ressource => $permission) {
                    $permission = $permission->toArray();
                    if (isset($list[$ressource]))
                        $permission = array_merge($permission, $list[$ressource]);
                    $list[$ressource] = $permission;
                }
            }
        }
        return $list;
	}

	public function buildDefaultAdminRole() {
	    //Allow the admin user to acces all permission
	    $this->removeDeny(self::ADMINISTRATORS);
	    $this->allow(self::ADMINISTRATORS);
	    //Deny the GESTS to access admin
	    $this->deny(self::GESTS, 'admin');
	}


	protected $_currentRole = null;

	public function loadUserRole() {
	    if (empty($this->_currentRole)) {
            $auth = Zend_Auth::getInstance();
            if ($auth->hasIdentity()) {
                $identity = $auth->getIdentity();
                $id = $identity->user_id;
                $users = new Jimw_Site_User();
                $user = $users->find($id)->current();
                $parent = array(self::MEMBERS);
                $groups = $user->findJimw_Site_GroupViaJimw_Site_UserGroup();
                foreach ($groups as $group) {
                    if ($this->hasRole($group->name)) {
                        $parent[] = $group->name;
                    }
                }
                $this->addRole(new Zend_Acl_Role($user->login), $parent);
                $this->_currentRole = $user->login;
            }
            else {
                $this->_currentRole = self::GESTS;
            }
	    }
	    return $this->_currentRole;
	}

    private function formatValuePermission($value) {
        return ($value === true || $value == 'allow' || $value == Zend_Acl::TYPE_ALLOW) ?
            Zend_Acl::TYPE_ALLOW : (($value === false || $value == 'deny' || $value == Zend_Acl::TYPE_DENY) ?
                Zend_Acl::TYPE_DENY : NULL);
    }
}
?>