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
        $productId  = $observer->getEvent()->getProduct();
        $request    = $observer->getEvent()->getRequest();
        /* @var $model Technooze_Tcategorystatus_Model_Checkout */
        $model      = Mage::getModel('tcategorystatus/checkout');

        $product = $model->getProduct($productId, $request);
        if($product){
            $categories = $model->getProductCategories($productId);
            // check to see if user is logged in
            $model->checkConditionIsLoggedIn();

            if(count($categories)){
                $model->isAllowedToPlaceOrder($categories, array(
                    // check to see if cut off date is active
                    // deprecated.
                    // Technooze_Tcategorystatus_Model_Tcategorystatus::TCATEGORY_STATUS_ORDER_CUTOFF_CODE,

                    // check to see if user is logged in
                    // this condition should be checked even if product is not assigned to any categories
                    // so moved this above separately
                    //'is_logged_in',

                    // check if one of the category associated is active or not
                    // disallow for products from inactive categories.
                    'category_status',

                    // check if this product's category has group access date defined
                    // if so, allow to permitted group only.
                    'group_permission',
                  )
                );
            }
        }
        return $this;
    }
}
