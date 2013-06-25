<?php
class IndexController extends Zend_Controller_Action
{

    function indexAction() {}
    function featuresAction() {}
    function pricingAction() {}
    function contactAction() {}
    function signupAction() {}
    function userguideAction()
    {
        $this->view->page = $this->getParam('page');
    }

    function trialAction()
    {
        $form = new Form;
        $this->view->form = $form;

        if($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $db = Zend_Registry::get('db');

            try {
                $id = $this->createDb($db,$form);
                $this->createConfig($id);

                $password = substr(sha1(uniqid()), 1, rand(8,12));
                $this->setAdminPassword($db,$id,$password);

                bootstrap::getInstance()->getSession()->id = $id;
                bootstrap::getInstance()->getSession()->password = $password;
                $this->_helper->FlashMessenger->addMessage('Created Website');
                return $this->_redirect('/confirmation');
            } catch(Exception $e) {
                throw $e;
            }
        }
    }

    function confirmationAction()
    {
        $this->view->id = bootstrap::getInstance()->getSession()->id;
        $this->view->password = bootstrap::getInstance()->getSession()->password;
    }

    function createDb($db,$form)
    {
        $db->insert('applications',array(
            'email'=>$form->getValue('email'),
            'website'=>$form->getValue('website'),
            'business_name'=>$form->getValue('business_name'),
            'owner_name'=>$form->getValue('owner_name'),
            'phone'=>$form->getValue('phone'),
            'created'=>new Zend_Db_Expr('NOW()')
        ));

        $id = $db->lastInsertId();
        $dbname = $this->dbName($id);
        $db->query(sprintf('CREATE DATABASE `%s`',$dbname));

        $command = Zend_Registry::get('mysql_command');
        `$command $dbname < ../application/install.sql`;
        return $id;
    }

    function createConfig($id)
    {
        $dbName = $this->dbName($id);
        $config = "
[production]
database.adapter = \"mysqli\"
database.protocol = \"mysql\"
database.params.host =  localhost
database.params.dbname = $dbName
database.params.username = bookingbat_web
database.params.password = f00b@r1337
";

        file_put_contents('var/website_configs/'.$id,$config);
    }

    function setAdminPassword($db,$id,$password)
    {
        $db->update($this->dbName($id).'.user',array(
            'password'=>sha1($password),
        ), 'username="admin"');
    }

    function dbName($id)
    {
        return 'bookingbat_'.$id;
    }
}
