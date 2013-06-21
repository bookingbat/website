<?php
class IndexController extends Zend_Controller_Action
{

    function indexAction()
    {
        $user = bootstrap::getInstance()->getUser();
        $this->view->isLoggedIn = $user['id'];

        if($this->getRequest()->isPost()) {
            $db = Zend_Registry::get('db');

            $dbname_escaped = 'bookingbat_'.preg_replace('#[^a-zA-Z0-9]#','',$this->_getParam('business_name'));
            try {
                $db->query(sprintf('CREATE DATABASE `%s`',$dbname_escaped));
            } catch(Exception $e) {
                // it must already exist, just try to proceed.
            }
            `mysql --user=root $dbname_escaped < ../application/install.sql`;
        }


    }

}