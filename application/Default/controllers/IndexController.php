<?php
class IndexController extends Zend_Controller_Action
{

    function indexAction()
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
                return $this->_redirect('/index/confirmation');
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
            'business_name'=>$form->getValue('business_name'),
            'owner_name'=>$form->getValue('owner_name'),
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
database.params.username = root
database.params.password =
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