<?php
/**
 * Created by PhpStorm.
 * User: artsiomvitkouski
 * Date: 1/21/14
 * Time: 12:03 AM
 */
class ITwebexperts_Vendor_Helper_Data extends Mage_Core_Helper_Abstract
{
    const ROOT_CATEGORY_ID = 3;
    const DEFAULT_SET_ID = 4;

    public static function getIframeJS($callback, $response)
    {
        $output = '<html><head></head><body>';
        $output .= '<script type="text/javascript">';
        $jsonResponse = Zend_Json::encode($response);
        $output .= 'window.parent.'.$callback."({$jsonResponse});";
        $output .= '</script>';
        $output .= '</body></html>';
        return $output;
    }
    
    public static function getMultiIframeJS($callback, $responses)
    {
        $output = '<html><head></head><body>';
        $output .= '<script type="text/javascript">';
        foreach ($responses AS $response) {
	        $jsonResponse = Zend_Json::encode($response);
			$output .= 'window.parent.'.$callback."({$jsonResponse});\n";
        }
        $output .= '</script>';
        $output .= '</body></html>';
        return $output;
    }

    public function getListingsUrl()
    {
        return $this->_getUrl('vendor/listing/listings');
    }

    public function getReservationsUrl()
    {
        return $this->_getUrl('vendor/listing/reservations');
    }
    
    public function getOrderHistoryUrl()
    {
	    return $this->_getUrl('sales/order/history');
    }

}