<?php
class Form extends Zend_Form
{
    function init()
    {
        $this->addElement('text','owner_name',array(
            'label'=>'Your Full Name',
            'required'=>true
        ));
        $this->addElement('text','business_name',array(
            'label'=>'Business Name',
            'required'=>true
        ));
        $this->addElement('text','phone',array(
            'label'=>'Phone',
            'required'=>true
        ));
        $this->addElement('text','email',array(
            'label'=>'Email',
            'required'=>true
        ));
    }
}