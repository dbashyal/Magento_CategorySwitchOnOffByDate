<?php
/**
 * Tcategorystatus Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Technooze
 * @package    Tcategorystatus
 * @copyright  Copyright (c) 2014 dltr.org
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Technooze
 * @package    Tcategorystatus
 * @author     Damodar Bashyal @dbashyal
 */
class Technooze_Tcategorystatus_Model_Observer
{
    public function addEvent($observer)
    {
        return;
        // this function can be used when required.
        /*if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_add') {
            Mage::dispatchEvent('checkout_cart_product_add_before', $observer);
        }
        return $this;*/
    }

    public function addToCartBefore(Varien_Event_Observer $observer){
        $productId = $observer->getEvent()->getProduct();
        $request = $observer->getEvent()->getRequest();

        // let's check if product info is supplied, if not try to retrieve it.
        if(!$productId && isset($request['product'])){
            $productId = (int)$request['product'];
        }

        // check if supplied product is object, then get ID only
        if(is_object($productId)){
            $productId = $productId->getId();
        }

        $product = Mage::getModel('tcategorystatus/tcategorystatus')->getProduct($productId);

        if(!$product){
            Mage::throwException(Mage::helper('checkout')->__('The product does not exist.'));
        }

        $categories = $product->getCategoryIds();
        $categories = array_combine($categories, $categories);

        // if the product is not assigned to any categories,
        // then we don't need to go further
        if(!count($categories)){
            return;
        }

        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->addAttributeToFilter('entity_id', array('in' => $categories));

        foreach($collection as $category){
            $path = substr($category->getPath(), (strpos($category->getPath(), '/', 2) + 1));
            $cats = explode('/', $path);
            foreach($cats as $cat){
                $categories[$cat] = $cat;
            }
        }
        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->addAttributeToFilter('entity_id', array('in' => $categories));
        $collection->addAttributeToFilter(Technooze_Tcategorystatus_Model_Tcategorystatus::TCATEGORY_STATUS_ORDER_CUTOFF_CODE, array('lt' => Mage::helper('tcategorystatus')->getDateToday()));

        // if this product belongs to any category that is set to cut off,
        // then do not allow adding product to cart
        if($collection->count()){
            if(Mage::getSingleton("customer/session")->isLoggedIn()){
                Mage::throwException(Mage::helper('checkout')->__('This is restricted product. Please login to retry.'));
            }
            Mage::throwException(Mage::helper('checkout')->__('This product is currently not on sale.'));
        }
    }
}
