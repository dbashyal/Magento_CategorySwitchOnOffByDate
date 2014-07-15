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
class Technooze_Tcategorystatus_Model_Checkout extends Mage_Core_Model_Abstract
{
    /**
     * @var array
     */
    private $_products   = array();

    /**
     * @var array
     */
    private $_categories = array();

    /* @var $_helper Technooze_Tcategorystatus_Helper_Data */
    private $_helper = null;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('tcategorystatus/tcategorystatus');
    }

    public function getHelper(){
        if(null === $this->_helper){
            $this->_helper = Mage::helper('tcategorystatus');
        }
        return $this->_helper;
    }

    /**
     * @param $product
     * @return int
     */
    public function getProductId($product){
        if($product && is_object($product)){
            return $product->getId();
        }
        return (int)$product;
    }

    /**
     * @param int $productId
     * @return array
     */
    public function getProductCategories($productId = 0){
        $productId = $this->getProductId($productId);

        if(!$this->_products[$productId]){
            return array();
        }
        if(!isset($this->_categories[$productId]) || empty($this->_categories[$productId])){
            $this->_categories[$productId] = array();
            $categories = $this->_products[$productId]->getCategoryIds();

            // if the product is not assigned to any categories,
            // then we don't need to go further
            if(!count($categories)){
                return array();
            }
            $categories = array_combine($categories, $categories);

            // we are getting all parent categories of assigned categories too.
            $collection = Mage::getModel('catalog/category')->getCollection();
            $collection->addAttributeToFilter('entity_id', array('in' => $categories));

            foreach($collection as $category){
                $path = substr($category->getPath(), (strpos($category->getPath(), '/', 2) + 1));
                $cats = explode('/', $path);
                foreach($cats as $cat){
                    $categories[$cat] = $cat;
                }
            }

            $this->_categories[$productId] = $categories;
        }
        return $this->_categories[$productId];
    }

    /**
     * @param int $productId
     * @return bool|Mage_Catalog_Model_Product
     */
    public function loadProduct($productId=0)
    {
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }

    /**
     * @param int $productId
     * @param array $request
     * @return bool|Mage_Catalog_Model_Product
     */
    public function getProduct($productId = 0, $request = array()){
        $productId = $this->getProductId($productId);
        if(empty($productId)){
            return false;
        }

        if(!isset($this->_products[$productId])){
            $this->_products[$productId] = false;
        }

        if(!$this->_products[$productId]){
            // let's check if product info is supplied, if not try to retrieve it.
            if(!$productId && isset($request['product'])){
                $productId = (int)$request['product'];
            }

            $product = $this->loadProduct($productId);

            if(!$product || !$product->getId()){
                Mage::throwException(Mage::helper('checkout')->__('The product does not exist.'));
            }
            $this->_products[$productId] = $product;
        }
        return $this->_products[$productId];
    }

    /**
     * @param array $categories
     */
    public function checkConditionCutoff($categories=array())
    {
        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->addAttributeToFilter('entity_id', array('in' => $categories));
        $collection->addAttributeToFilter(Technooze_Tcategorystatus_Model_Tcategorystatus::TCATEGORY_STATUS_ORDER_CUTOFF_CODE, array('lt' => $this->getHelper()->getDateToday()));

        // if this product belongs to any category that is set to cut off,
        // then do not allow adding product to cart
        if($collection->count()){
            if(Mage::getSingleton("customer/session")->isLoggedIn()){
                Mage::throwException(Mage::helper('checkout')->__('This is restricted product. Please login to retry.'));
            }
            Mage::throwException(Mage::helper('checkout')->__('This product is currently not on sale.'));
        }
    }

    /*
     * Check if one of the category associated is already inactive
     * Then you can't buy any product from this category
     * This applies to everyone
     */
    /**
     * @param array $categories
     */
    public function checkConditionCategoryStatus($categories=array())
    {
        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->addAttributeToFilter('entity_id', array('in' => $categories));
        $collection->addAttributeToFilter('is_active', 0);

        if($collection->count()){
            Mage::throwException(Mage::helper('checkout')->__('This product is no longer available.'));
        }
    }

    /**
     * @param array $categories
     */
    public function checkConditionGroupPermission($categories=array())
    {
        // check if there is any rule defined for one of the category
        /* @var $collection Technooze_Tcategoryacl_Model_Mysql4_Tcategoryacl_Collection */
        $collection = Mage::getModel('tcategoryacl/tcategoryacl')->getCollection();
        $collection
          ->addFieldToFilter('category_id', array('in' => $categories))
          ->addFieldToFilter('status', '1');

        // if no date specified, no need to go further
        if(!$collection->count()){
            return;
        }

        /* @var $group Technooze_Schoolgroup_Model_Schoolgroup */
        $group = Mage::getModel('schoolgroup/schoolgroup')->getCurrentCustomerSchoolGroup();
        if(!$group || !$group->getId()){
            // if there is group rule defined but current user is not assigned to any group
            // show error message
            if(Mage::getSingleton("customer/session")->isLoggedIn()){
                Mage::throwException(Mage::helper('checkout')->__('This is restricted product. Please login to retry.'));
            }
            Mage::throwException(Mage::helper('checkout')->__('This product is currently not on sale.'));
        }

        // set loaded to false, so below rules are applied
        $collection
          ->clear()
          // now add new conditions
          ->addFieldToFilter('group_id', $group->getId())
          ->addFieldToFilter('allow_from', array('lteq' => $this->getHelper()->getDateToday()))
          ->addFieldToFilter('allow_to', array('gteq' => $this->getHelper()->getDateToday()));

        // if we get result, that means customer can still buy it, else NO.
        if(!$collection->count()){
            Mage::throwException(Mage::helper('checkout')->__('This product is currently not on sale.'));
        }
    }

    public function checkConditionIsLoggedIn()
    {
        if(!$this->getHelper()->isLoggedIn()){
            Mage::throwException(Mage::helper('checkout')->__('Please log in to add books to site.'));
        }
    }

    /**
     * @param array $categories
     * @param array $conditions
     */
    public function isAllowedToPlaceOrder($categories=array(), $conditions=array())
    {
        if(!is_array($conditions) || !count($conditions)){
            return;
        }

        foreach($conditions as $condition){
            switch($condition){
                case Technooze_Tcategorystatus_Model_Tcategorystatus::TCATEGORY_STATUS_ORDER_CUTOFF_CODE:
                    $this->checkConditionCutoff($categories);
                    break;
                case 'is_logged_in':
                    $this->checkConditionIsLoggedIn();
                    break;
                case 'group_permission':
                    $this->checkConditionGroupPermission($categories);
                    break;
                case 'category_status':
                    $this->checkConditionCategoryStatus($categories);
                    break;
                default:
            }
        }
    }
}
