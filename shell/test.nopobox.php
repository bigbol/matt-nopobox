<?php
/*
 * Author: Matt
 * URI:http://www.xbc.me
 * Time:2012.10.30
 * */
class Mage_Shell_Test extends PHPUnit_Framework_TestCase{
	protected function setUp(){
		$path =dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
		$code = 'admin';
		$type = 'store';
		require_once $path . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';
        Mage::app($code, $type);
	}
	protected function _getTestModel(){
		$model  = Mage::getModel('Matt_Nopobox_Model_Quote_Address');
		$model->setFirstname('Jia');
		$model->setLastname('Cai');
		$model->setCity('Whaterveer');
		$model->setTelephone('186');
		$model->setCountryId('US');
		$model->setPostcode('512443');
		$model->setRegionId('43');
		return $model;
	}
	protected function _setStatus($active = 1){
		$section = 'nopobox';
		$groups  = array(
			'option' => array(
				'fields' => array(
					array(
						'active' => array(
							'value' => $active
						)
					)
				)
			)
		);
		Mage::getModel('adminhtml/config_data')
                ->setSection($section)
                ->setWebsite($website)
                ->setStore($store)
                ->setGroups($groups)
                ->save();
        // reinit configuration
        Mage::getConfig()->reinit();
	}
	public function testNopoboxEnable(){
		$this->_setStatus(0);
		$active = Mage::getStoreConfig('nopobox/option/active');
		$this->assertEquals(0, $active);
		$this->_setStatus();
        $active = Mage::getStoreConfig('nopobox/option/active');
        $this->assertEquals(1, $active);
	}
	
	public function testHasPoboxNoCountry(){
		$model = $this->_getTestModel();
		$street = array();
		$street[] = 'Futian street';
		$street[] = 'pobox test';
		$model->setStreet($street);
		$errors = $model->validate();
		$_errors =array();
		$_errors[] = Mage::helper('core')->__(Mage::getStoreConfig('nopobox/option/aerror')); 
		$this->assertEquals($_errors, $errors);
		
		$street = array();
		$street[] = 'pobox test';
		$street[] = 'Futian street';
		$model->setStreet($street);
		$errors = $model->validate();
		$_errors =array();
		$_errors[] = Mage::helper('core')->__(Mage::getStoreConfig('nopobox/option/aerror')); 
		$this->assertEquals($_errors, $errors);
		
		$street = array();
		$street[] = 'Pobox test';
		$street[] = 'Futian street';
		$model->setStreet($street);
		$errors = $model->validate();
		$_errors =array();
		$_errors[] = Mage::helper('core')->__(Mage::getStoreConfig('nopobox/option/aerror')); 
		$this->assertEquals($_errors, $errors);
		
		$street = array();
		$street[] = 'POBOX test';
		$street[] = 'Futian street';
		$model->setStreet($street);
		$errors = $model->validate();
		$_errors =array();
		$_errors[] = Mage::helper('core')->__(Mage::getStoreConfig('nopobox/option/aerror')); 
		$this->assertEquals($_errors, $errors);
	}
	public function testErrorMerge(){
		$model = $this->_getTestModel();
		$model->setFirstname('');
		$errors = $model->validate();
		$_errors =array();
		$errors[] = Mage::helper('customer')->__('Please enter the first name.');
		$this->assertEquals($_errors, $errors);
		
		//join pobox validate
		$street = array();
		$street[] = 'Futian street';
		$street[] = 'pobox test';
		$model->setStreet($street);
		$errors = $model->validate();
		$_errors =array();
		$_errors[] = Mage::helper('core')->__(Mage::getStoreConfig('nopobox/option/aerror'));
		$this->assertEquals($_errors, $errors);
	}
}
?>