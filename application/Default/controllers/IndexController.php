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
            } catch(Exception $e) {
                throw $e;
            }

            $this->_helper->FlashMessenger->addMessage('Created website');
            return $this->_redirect('/');
        }
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
        `mysql --user=root $dbname < ../application/install.sql`;
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

    function dbName($id)
    {
        return 'bookingbat_'.$id;
    }
}