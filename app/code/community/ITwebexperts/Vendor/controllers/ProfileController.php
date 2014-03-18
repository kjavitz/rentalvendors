<?php
class ITwebexperts_Vendor_ProfileController extends Mage_Core_Controller_Front_Action {

	protected function _getSession()
	{
		return Mage::getSingleton('catalog/session');
	}
	
	

    public function publicAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        if ($id = $this->getRequest()->getParam('id')) {
            $vendor = Mage::getModel('customer/customer')->load($id);
            $previewMode = false;
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
	            $customer = Mage::getSingleton('customer/session')->getCustomer();
	            if ($id == $customer->getId()) {
		            $previewMode = true;
	            }
            }
            if ($vendor->getId() && $vendor->getVendor() && ($vendor->getVendorIsPublic() || $previewMode)) {
                Mage::register('current_vendor', $vendor);
                $this->getLayout()->getBlock('head')->setTitle($this->__('%s Profile', $vendor->getVendorNickname()));
            } else {
                Mage::getSingleton('catalog/session')->addError(Mage::helper('vendor')->__('Invalid vendor id.'));
                $this->_redirect('/');
                return;
            }

        } else {
            Mage::getSingleton('catalog/session')->addError(Mage::helper('vendor')->__('Invalid vendor id.'));
            $this->_redirect('/');
            return;
        }


        $this->renderLayout();
    }
    
    public function sendAction()
    {
    	if ($this->getRequest()->isPost()) {
    		Mage::getSingleton('catalog/session')->addSuccess($this->__('Message has been saved.'));
	    	$message = Mage::getModel('vendor/message')->addData($this->getRequest()->getPost())
	    	->setCreatedAt(Varien_Date::now())
	    	->save();
	    	
	    	$emailTemplateVariables = array();
	    	$emailTemplateVariables['store'] = Mage::app()->getStore();
	    	$emailTemplateVariables['message'] = $message->getComment();
	    	$fromCustomer = Mage::getModel('customer/customer')->load($message->getFromId());	
	    	$toCustomer = Mage::getModel('customer/customer')->load($message->getToId());	    	
	    	$emailTemplateVariables['from_name'] = $fromCustomer->getFirstname().' '.$fromCustomer->getLastname();
	    	$emailTemplateVariables['to_name'] = $toCustomer->getFirstname().' '.$toCustomer->getLastname();

	    	$emailTemplate  = Mage::getModel('core/email_template')
					->loadDefault('quote_request_processed_email_template');
					
			$emailTemplate->setSenderName($emailTemplateVariables['from_name']);
			$emailTemplate->setSenderEmail($fromCustomer->getEmail());
			$emailTemplate->setTemplateSubject(Mage::helper('vendor')->__('New message from ').$emailTemplateVariables['from_name']);
			$emailTemplate->send(array($toCustomer->getEmail()), $emailTemplateVariables['to_name'], $emailTemplateVariables);

    	}
	    $this->_redirectSuccess($this->getRequest()->getParam('return_url'));
    }
    
    
    public function postReviewAction()
    {
	    if ($data = Mage::getSingleton('review/session')->getFormData(true)) {
            $rating = array();
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data   = $this->getRequest()->getPost();
            $rating = $this->getRequest()->getParam('ratings', array());
        }
        
        $vendor = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));

        if ($vendor->getId() && !empty($data)) {
            $session    = Mage::getSingleton('core/session');
            /* @var $session Mage_Core_Model_Session */
            $review     = Mage::getModel('review/review')->setData($data);
            /* @var $review Mage_Review_Model_Review */

            $validate = $review->validate();
            if ($validate === true) {
                try {
                    $review->setEntityId($review->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_CUSTOMER_CODE))
                        ->setEntityPkValue($vendor->getId())
                        ->setStatusId(Mage_Review_Model_Review::STATUS_PENDING)
                        ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->setStores(array(Mage::app()->getStore()->getId()))
                        ->save();

                    foreach ($rating as $ratingId => $optionId) {
                        Mage::getModel('rating/rating')
                        ->setRatingId($ratingId)
                        ->setReviewId($review->getId())
                        ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                        ->addOptionVote($optionId, $vendor->getId());
                    }

                    $review->aggregate();
                    $session->addSuccess($this->__('Your review has been accepted for moderation.'));
                }
                catch (Exception $e) {
                    $session->setFormData($data);
                    $session->addError($this->__('Unable to post the review.'));
                }
            }
            else {
                $session->setFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $session->addError($errorMessage);
                    }
                }
                else {
                    $session->addError($this->__('Unable to post the review.'));
                }
            }
        }

        if ($redirectUrl = Mage::getSingleton('review/session')->getRedirectUrl(true)) {
            $this->_redirectUrl($redirectUrl);
            return;
        }
        $this->_redirectReferer();
    }
    
    protected function _goBack()
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {

            if (!$this->_isUrlInternal($returnUrl)) {
                throw new Mage_Exception('External urls redirect to "' . $returnUrl . '" denied!');
            }

            $this->_getSession()->getMessages(true);
            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()
        ) {
            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }
    
    

}