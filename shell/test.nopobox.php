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
	protected function _getTestModel($countryId = 'US' , $RegionId = 43){
		$model  = Mage::getModel('Matt_Nopobox_Model_Quote_Address');
		$model->setFirstname('Jia');
		$model->setLastname('Cai');
		$model->setCity('Whaterveer');
		$model->setTelephone('186');
		$model->setCountryId($countryId);
		$model->setPostcode('512443');
		$model->setRegionId($RegionId);
		return $model;
	}
	protected function _setField($field = 'active' , $value = 1){
		$section = 'nopobox';
		$groups  = array(
			'option' => array(
				'fields' => array(
						$field => array(
							'value' => $value
					)
				)
			)
		);
		Mage::getModel('adminhtml/config_data')
                ->setSection($section)
                //->setWebsite($website)
                //->setStore($store)
                ->setGroups($groups)
                ->save();
        //$model = Mage::getModel('core/config');
        //$model->saveConfig('nopobox/option/active' , $active);
        // reinit configuration
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();
	}
	public function testNopoboxEnable(){
		$this->_setField('active' , 0);
		$active = Mage::getStoreConfig('nopobox/option/active');
		$this->assertEquals(0, $active);
		$this->_setField();
        $active = Mage::getStoreConfig('nopobox/option/active');
        $this->assertEquals(1, $active);
	}
	
	public function testHasPoboxNoCountry(){
		$this->_setField('allowspecific' , 0);
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
		$_errors[] = Mage::helper('customer')->__('Please enter the first name.');
		$_errors[] = Mage::helper('customer')->__('Please enter the street.');
		$this->assertEquals($_errors, $errors);
		
		//join pobox validate
		$street = array();
		$street[] = 'Futian street';
		$street[] = 'pobox test';
		$model->setStreet($street);
		$errors = $model->validate();
		$_errors =array();
		$_errors[] = Mage::helper('customer')->__('Please enter the first name.');
		$_errors[] = Mage::helper('core')->__(Mage::getStoreConfig('nopobox/option/aerror'));
		$this->assertEquals($_errors, $errors);
	}
	public function testHasPoboxHasCountry(){
		$this->_setField('allowspecific');
		$this->_setField('specificcountry' , array('DE' , 'US'));
		$model = $this->_getTestModel('DE' , 80);
		$street = array();
		$street[] = 'Futian street';
		$street[] = 'Pobox test';
		$model->setStreet($street);
		$errors = $model->validate();
		$this->assertEquals(true, $errors);
		
		$street = array();
		$street[] = 'Pobox test';
		$street[] = 'Futian street';
		$model->setStreet($street);
		$errors = $model->validate();
		$this->assertEquals(true, $errors);
		
		$street = array();
		$street[] = 'Pobox test';
		$street[] = 'Futian street';
		$model->setStreet($street);
		$errors = $model->validate();
		$this->assertEquals(true, $errors);
		
		$street = array();
		$street[] = 'POBOX test';
		$street[] = 'Futian street';
		$model->setStreet($street);
		$errors = $model->validate();
		$this->assertEquals(true, $errors);
		
		
		//can not use pobox
		$model = $this->_getTestModel('FR' , 200);
		$street = array();
		$street[] = 'Futian street';
		$street[] = 'Pobox test';
		$model->setStreet($street);
		$errors = $model->validate();
		$_errors =array();
		$_errors[] = Mage::helper('core')->__(Mage::getStoreConfig('nopobox/option/cerror')); 
		$this->assertEquals($_errors, $errors);
		
		$street = array();
		$street[] = 'Pobox test';
		$street[] = 'Futian street';
		$model->setStreet($street);
		$errors = $model->validate();
		$_errors =array();
		$_errors[] = Mage::helper('core')->__(Mage::getStoreConfig('nopobox/option/cerror')); 
		$this->assertEquals($_errors, $errors);
		
		$street = array();
		$street[] = 'Pobox test';
		$street[] = 'Futian street';
		$model->setStreet($street);
		$errors = $model->validate();
		$_errors =array();
		$_errors[] = Mage::helper('core')->__(Mage::getStoreConfig('nopobox/option/cerror')); 
		$this->assertEquals($_errors, $errors);
		
		$street = array();
		$street[] = 'POBOX test';
		$street[] = 'Futian street';
		$model->setStreet($street);
		$errors = $model->validate();
		$_errors =array();
		$_errors[] = Mage::helper('core')->__(Mage::getStoreConfig('nopobox/option/cerror')); 
		$this->assertEquals($_errors, $errors);
		
		$this->_setField('allowspecific' , 0);
		$this->_setField('specificcountry' , array());
	}
}
?>