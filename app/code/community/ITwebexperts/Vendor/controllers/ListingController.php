<?php
class ITwebexperts_Vendor_ListingController extends Mage_Core_Controller_Front_Action {

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

    public function listingsAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Products'));
        $this->renderLayout();
    }

    public function addproductAction()
    {

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Add Product'));
        $this->renderLayout();
    }



    public function uploadphotoAction()
    {
        try {
            $uploader = new Mage_Core_Model_File_Uploader('image');
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->addValidateCallback('catalog_product_image',
                Mage::helper('catalog/image'), 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save(
                Mage::getSingleton('catalog/product_media_config')->getBaseTmpMediaPath()
            );

            Mage::dispatchEvent('catalog_product_gallery_upload_image_after', array(
                'result' => $result,
                'action' => $this
            ));
            

            /**
             * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
             */
            $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
            $result['path'] = str_replace(DS, "/", $result['path']);

            $result['url'] = Mage::getSingleton('catalog/product_media_config')->getTmpMediaUrl($result['file']);
            $result['file'] = $result['file'] . '.tmp';
            $result['cookie'] = array(
                'name'     => session_name(),
                'value'    => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path'     => $this->_getSession()->getCookiePath(),
                'domain'   => $this->_getSession()->getCookieDomain()
            );
            $result['base_image'] = $this->getRequest()->getParam('base_image', 0);
        } catch (Exception $e) {
            $result = array(
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode());
        }
        $this->getResponse()->setBody(ITwebexperts_Vendor_Helper_Data::getIframeJS('onPhotoUploaded', $result));
    }
    
    public function multiuploadAction()
    {
    	$results = array();
    	foreach ($_FILES AS $_field => $_file) {
	    	try {
	            $uploader = new Mage_Core_Model_File_Uploader($_field);
	            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	            $uploader->addValidateCallback('catalog_product_image',
	                Mage::helper('catalog/image'), 'validateUploadFile');
	            $uploader->setAllowRenameFiles(true);
	            $uploader->setFilesDispersion(true);
	            $result = $uploader->save(
	                Mage::getSingleton('catalog/product_media_config')->getBaseTmpMediaPath()
	            );
	
	            Mage::dispatchEvent('catalog_product_gallery_upload_image_after', array(
	                'result' => $result,
	                'action' => $this
	            ));
	            
	
	            /**
	             * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
	             */
	            $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
	            $result['path'] = str_replace(DS, "/", $result['path']);
	
	            $result['url'] = Mage::getSingleton('catalog/product_media_config')->getTmpMediaUrl($result['file']);
	            $result['file'] = $result['file'] . '.tmp';
	            $result['cookie'] = array(
	                'name'     => session_name(),
	                'value'    => $this->_getSession()->getSessionId(),
	                'lifetime' => $this->_getSession()->getCookieLifetime(),
	                'path'     => $this->_getSession()->getCookiePath(),
	                'domain'   => $this->_getSession()->getCookieDomain()
	            );
	            $result['base_image'] = $this->getRequest()->getParam('base_image', 0);
	        } catch (Exception $e) {
	            $result = array(
	                'error' => $e->getMessage(),
	                'errorcode' => $e->getCode());
	        }
	        $results[] = $result;
    	}
	    
        if (!count($results)) {
	        $results[] = array(
	        	'error' => 1001,
                'errorcode' => 'no_photos'
	        );
        }
        $this->getResponse()->setBody(ITwebexperts_Vendor_Helper_Data::getMultiIframeJS('onPhotoUploaded', $results));
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $product = Mage::getModel('catalog/product')->load($id);
            Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
            $product->delete();
            $this->_getSession()->addSuccess($this->__('The product has been removed.'));
        }
        $this->_redirectSuccess(Mage::getUrl('vendor/listing/listings', array('_secure'=>true)));
    }

    public function editproductAction()
    {
        $product = Mage::getModel('catalog/product')
            ->load($this->getRequest()->getParam('id', null));
        // check id
        if (!$product->getId()) {
            $this->_getSession()->addError('Product does not exists');
            $this->_redirectSuccess(Mage::getUrl('vendor/listing/addproduct', array('_secure'=>true)));
            return;
        } else {
            Mage::register('vendor_current_product', $product);
            Mage::register('current_product', $product);
        }
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Edit Product'));
        $this->renderLayout();

    }

    public function saveProductAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $customer = $this->_getSession()->getCustomer();
                $product = Mage::getModel('catalog/product');
                $originalStoreId = Mage::app()->getStore()->getId();
                if ($id = $this->getRequest()->getParam('id')) {
                    $product->load($id);
                } else {
                    $product->setStoreId($originalStoreId);
                    $product->setAttributeSetId(ITwebexperts_Vendor_Helper_Data::DEFAULT_SET_ID);
                    $product->setTypeId('reservation');
                    $product->setSku('vndr_'.microtime(true).rand(1000, 9999));
                }
                Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
                // save product
                $request = $this->getRequest();
                $product->setName($request->getParam('name', null));
                $product->setDescription($request->getParam('vendor_product_spec', null));
                $product->setShortDescription($request->getParam('description', null));
                $product->setVendorProductSpec($request->getParam('vendor_product_spec', null));
                $product->setVendorColor($request->getParam('vendor_color', null));
                $product->setVendorStyle($request->getParam('vendor_style', null));
                $product->setVendorRentalTerms($request->getParam('vendor_rental_terms', null));
                $product->setWeight(1);
                $product->setStatus($request->getParam('status', 2));
                $product->setVisibility(4);
                $product->setIsReservation(1);
                $product->setTaxClassId(0);
                $product->setResBlackoutDates($request->getParam('res_blackout_dates', null));
                $product->setVendorId($customer->getId());
                $product->setPayperrentalsQuantity($request->getParam('payperrentals_quantity', 0));
                $buyoutPrice = floatval(str_replace('$', '', $request->getParam('payperrentals_buyoutprice', 0)));
                $product->setPayperrentalsBuyoutprice($buyoutPrice);
                $stockData = $product->getStockData();
                if (!is_array($stockData)) {
                    $stockData = array();
                }
                $stockData['qty'] = $product->getPayperrentalsQuantity();
                $stockData['is_in_stock'] = 1;
                $stockData['manage_stock'] = 0;
                $stockData['use_config_manage_stock'] = 0;
                $categoryIds = $request->getPost('category_ids');
                if (null !== $categoryIds) {
                    if (empty($categoryIds)) {
                        $categoryIds = array();
                    }
                    $product->setCategoryIds($categoryIds);
                }
                $product->setStockData($stockData);
                // add images
                //$product->addImageToMediaGallery('/Users/artsiomvitkouski/Library/Containers/com.bitnami.mampstack/Data/app-5_4_9/apache2/htdocs/hostess/media/catalog/product/b/d/bdq740vccaagvwz444.jpg', array('image', 'small_image', 'thumbnail'),true,false);

                /*$mediaGallery = array(
                    'images' => Zend_Json::decode('[{"url":"http://localhost:8080/hostess/media/tmp/catalog/product/b/d/bdjlhowcqaa1keu_1ssss.jpg","file":"/b/d/bdjlhowcqaa1keu_1ssss.jpg","label":"","position":1,"disabled":0,"removed":0}]'),
                    'values' => Zend_Json::decode('{"image":"/b/d/bdjlhowcqaa1keu_1ssss.jpg","small_image":"no_selection","thumbnail":"no_selection"}')
                );*/
                $mediaGallery = $request->getPost('media_gallery');
                $product->setMediaGallery($mediaGallery);
                $mediaGalleryValues = Zend_Json::decode($mediaGallery['values']);

                $product->setImage($mediaGalleryValues['image']);
                $product->setSmallImage($mediaGalleryValues['image']);
                $product->setthumbnail($mediaGalleryValues['image']);


                // save
                $product->save();
                // update product website
                $actionModel = Mage::getSingleton('catalog/product_action');
                $actionModel->updateWebsites(array($product->getId()), array(1), 'add');
                // delete prices
                $priceCollection = Mage::getModel('payperrentals/reservationprices')
                    ->getCollection()
                    ->addEntityIdFilter($product->getId());
                foreach ($priceCollection AS $oldPrice) {
                    $oldPrice->delete();
                }
                // reservationprice
                $price = Mage::getModel('payperrentals/reservationprices');
                $price->setEntityId($product->getId());
                $price->setNumberof(1);
                $price->setPtype(3);
                $price->setCustgroup(1);
                $price->setDateFrom('0000-00-00 00:00:00');
                $price->setDateTo('0000-00-00 00:00:00');
                $resPrice = floatval(str_replace('$', '', $request->getParam('res_prices', 0)));
                $price->setPrice($resPrice);
                $price->save();
                // add tags
                $tagModel = Mage::getModel('tag/tag');
                $tagName    = (string) $this->getRequest()->getPost('productTagName');
                if (strlen($tagName)) {
                    $tagNamesArr = $this->_cleanTags($this->_extractTags($tagName));
                    foreach ($tagNamesArr as $tagName) {
                        // unset previously added tag data
                        $tagModel->unsetData()
                            ->loadByName($tagName);

                        if (!$tagModel->getId()) {
                            $tagModel->setName($tagName)
                                ->setFirstCustomerId($customer->getId())
                                ->setFirstStoreId($originalStoreId)
                                ->setStatus($tagModel->getApprovedStatus())
                                ->save();
                        }
                        $relationStatus = $tagModel->saveRelation($product->getId(), $customer->getId(), $originalStoreId);
                    }
                    if (count($tagNamesArr)) {
                        $this->_getSession()->addSuccess($this->__('The tags has been saved.'));
                    }
                }
                // return origin store
                Mage::app()->getStore()->setId($originalStoreId);
                $this->_getSession()->addSuccess($this->__('The product has been saved.'));
                $this->_redirectSuccess(Mage::getUrl('vendor/listing/editproduct', array('id' => $product->getId(), '_secure'=>true)));
                return;
            }  catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }

        }
        $this->_redirectSuccess(Mage::getUrl('vendor/listing/addproduct', array('_secure'=>true)));
    }

    /**
     * Checks inputed tags on the correctness of symbols and split string to array of tags
     *
     * @param string $tagNamesInString
     * @return array
     */
    protected function _extractTags($tagNamesInString)
    {
        return explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $tagNamesInString));
    }

    /**
     * Clears the tag from the separating characters.
     *
     * @param array $tagNamesArr
     * @return array
     */
    protected function _cleanTags(array $tagNamesArr)
    {
        foreach( $tagNamesArr as $key => $tagName ) {
            $tagNamesArr[$key] = trim($tagNamesArr[$key], '\'');
            $tagNamesArr[$key] = trim($tagNamesArr[$key]);
            if( $tagNamesArr[$key] == '' ) {
                unset($tagNamesArr[$key]);
            }
        }
        return $tagNamesArr;
    }


    public function reservationsAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Reservations'));
        $this->renderLayout();
    }

}