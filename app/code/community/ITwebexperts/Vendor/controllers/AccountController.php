<?php
class ITwebexperts_Vendor_AccountController extends Mage_Core_Controller_Front_Action
{

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('customer/account/login');
            return;
        }
        $customer = $this->_getSession()->getCustomer();
        if (!$customer->getVendor()) {
            $this->_redirect('customer/account');
            return;
        }
    }

    public function settingsAction()
    {

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Vendor Account'));
        $this->renderLayout();
    }

    public function profileAction()
    {

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Vendor Seggins'));
        $this->renderLayout();
    }

    public function reservationsAction()
    {

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Reservations'));
        $this->renderLayout();
    }

    public function saveSettingsAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = $this->_getSession()->getCustomer();
            $customer->load($customer->getId());
            $customer->addData(array(
                'vendor_website' => $this->getRequest()->getParam('vendor_website'),
                'vendor_facebook' => $this->getRequest()->getParam('vendor_facebook'),
                'vendor_twitter' => $this->getRequest()->getParam('vendor_twitter'),
                'vendor_google' => $this->getRequest()->getParam('vendor_google'),
                'vendor_description' => $this->getRequest()->getParam('vendor_description'),
                'vendor_nickname' => $this->getRequest()->getParam('vendor_nickname'),
                'vendor_is_public' => $this->getRequest()->getParam('vendor_is_public')
            ));
            $customer->save();
            $addresses = $this->getRequest()->getPost('address');
            foreach ($addresses AS $addressId => $addressData) {
                $address  = Mage::getModel('customer/address');
                if (!is_numeric($addressId)) {
                    $addressId = false;
                }
                if ($addressId) {
                    $existsAddress = $customer->getAddressById($addressId);
                    if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
                        $address->setId($existsAddress->getId());
                    }
                }

                $errors = array();

                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_address_edit')
                    ->setEntity($address);
                //$addressData    = $addressForm->extractData($this->getRequest());
                $addressErrors  = $addressForm->validateData($addressData);
                if ($addressErrors !== true) {
                    $errors = $addressErrors;
                }


                try {
                	
                	$addressDataOrigin = $addressData;
                    $addressForm->compactData($addressData);
                    $address->setIsVendor(1);
                    $address->setCustomerId($customer->getId());
                        //->setIsDefaultBilling($addressData['default_billing'])
                        //->setIsDefaultShipping($addressData['default_shipping']);

                    $addressErrors = $address->validate();
                    if ($addressErrors !== true) {
                        $errors = array_merge($errors, $addressErrors);
                    }


                    if (count($errors) === 0) {
                        $address->save();
                        if (isset($addressDataOrigin['default_vendor'])) {
		                	if ($addressDataOrigin['default_vendor']) {
			                	$customer->setDefaultVendor($address->getId());
			                	$customer->save();
			                	$customer = Mage::getModel('customer/customer')->load($customer->getId());
		                	}
	                	}
                        $this->_getSession()->addSuccess($this->__('The address has been saved.'));
                    } else {
                        $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
                        foreach ($errors as $errorMessage) {
                            $this->_getSession()->addError($errorMessage);
                        }
                    }
                } catch (Mage_Core_Exception $e) {
                    $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                        ->addException($e, $e->getMessage());
                } catch (Exception $e) {
                    $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                        ->addException($e, $this->__('Cannot save address.'));
                }
            }
            $shippingMethod = $this->getRequest()->getPost('shipping');
            try {
	            $shippingCollection = Mage::getModel('vendor/shippingrate')->getCollection()->addFieldToFilter('customer_id', array('eq' => $customer->getId()));
	            foreach ($shippingCollection AS $shipping) {
		            $shipping->delete();
	            }
	            foreach ($shippingMethod AS $shippingData) {
		            $shipping = Mage::getModel('vendor/shippingrate');
		            $shipping->addData($shippingData);
		            $shipping->setCustomerId($customer->getId());
		            $shipping->save();
	            }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot delete shipping.'));
            }
            
            $this->_redirectSuccess(Mage::getUrl('vendor/account/settings', array('_secure'=>true)));
        }
    }

    public function saveAvatarAction()
    {
        $customer = $this->_getSession()->getCustomer();
        $customer->load($customer->getId());
        if ($this->getRequest()->isPost() && isset($_FILES['vendor_avatar']['name']) && ($_FILES['vendor_avatar']['name'] != '')) {
            $value = $_FILES['vendor_avatar'];
            $path = Mage::getBaseDir('media') .DS.'vendor';
            try {
                $uploader = new Varien_File_Uploader($value);
                $uploader->setFilesDispersion(true);
                $uploader->setFilenamesCaseSensitivity(false);
                $uploader->setAllowRenameFiles(true);
                $uploader->setAllowedExtensions(array('jpg', 'JPG', 'png','PNG', 'gif', 'GIF'));
                $uploader->save($path, $value['name']);
                $fileName = $uploader->getUploadedFileName();
                #save customer
                $customer->setData('vendor_avatar', $fileName);
                $this->_getSession()->addSuccess($this->__('The profile photo has been saved.'));
                $customer->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        $this->_redirectSuccess(Mage::getUrl('vendor/account/settings', array('_secure'=>true)));
    }

    public function saveGalleryAction()
    {
        $customer = $this->_getSession()->getCustomer();
        $customer->load($customer->getId());
        if ($this->getRequest()->isPost() && isset($_FILES['vendor_gallery_photo']['name']) && ($_FILES['vendor_gallery_photo']['name'] != '')) {
            $value = $_FILES['vendor_gallery_photo'];
            $path = Mage::getBaseDir('media') .DS.'vendor'.DS.'gallery';
            try {
                $uploader = new Varien_File_Uploader($value);
                $uploader->setFilesDispersion(true);
                $uploader->setFilenamesCaseSensitivity(false);
                $uploader->setAllowRenameFiles(true);
                $uploader->setAllowedExtensions(array('jpg', 'JPG', 'png','PNG', 'gif', 'GIF'));
                $uploader->save($path, $value['name']);
                $fileName = $uploader->getUploadedFileName();
                $gallery = Mage::getModel('vendor/gallery');
                $gallery->setCustomerId($customer->getId());
                $gallery->setPath($fileName);
                $gallery->save();
                $this->_getSession()->addSuccess($this->__('The photo has been saved.'));
                $customer->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        $this->_redirectSuccess(Mage::getUrl('vendor/account/settings', array('_secure'=>true)));
    }

}