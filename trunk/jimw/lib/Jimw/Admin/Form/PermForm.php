<?php

class Jimw_Admin_Form_PermForm extends Jimw_Form
{
    protected function initForm()
    {
        return array(
            /*'elements' => array(
                'siteperm_id' => array('type' => 'hidden'),
                'group_id' => 'hidden',
                'site_id' => 'hidden'
            )*/
        );
    }

    public function addRessourceForm(array $ressources){
        foreach ($ressources as $ressource_name => $ressource) {
            $ressource_value = Zend_Acl::TYPE_DENY;
            $ressource_label = $ressource_name;
            if (isset($ressource['default'])) {
                $ressource_value = $ressource['default'];
                unset($ressource['default']);
            }
            if (isset($ressource['label'])) {
                $ressource_label = $ressource['label'];
                unset($ressource['label']);
            }
            if (isset($ressource['inherit'])) {
                unset($ressource['inherit']);
            }
            if (isset($ressource['all'])) {
                $ressource_value = (isset($ressource['all']['default'])) ? $ressource['all']['default'] : $ressource['all'];
                if (isset($ressource['all']['label']))
                    $ressource_label = $ressource['all']['label'];
                unset($ressource['all']);
            }
            $perms_form = new Jimw_Form_SubForm();
            $perms_form->setLegend($ressource_label);
            $element = new Jimw_Form_Element_Perms('all');
            $element->setLabel('All');
            $element->setValue($this->formatValuePermission($ressource_value));
            $perms_form->addElement($element);
            if (is_array($ressource)) {
                foreach ($ressource as $permission_name => $permission) {
                    $perm_el = new Jimw_Form_Element_Perms($permission_name);
                    $perm_el->setLabel((isset($permission['label'])) ? $permission['label'] : $permission_name);
                    $perm_el->setValue($this->formatValuePermission($permission['default']));
                    if (isset($permission['description']))
                        $perm_el->setDescription($permission['description']);
                    $perms_form->addElement($perm_el);
                }
            }
            $this->addSubForm($perms_form, $ressource_name);
        }
    }

    /**
     * Formalize the value
     */
    private function formatValuePermission($value) {
        return ($value === true || $value == 'allow' || $value == Zend_Acl::TYPE_ALLOW) ?
            Zend_Acl::TYPE_ALLOW : (($value === false || $value == 'deny' || $value == Zend_Acl::TYPE_DENY) ?
                Zend_Acl::TYPE_DENY : NULL);
    }

}
?>