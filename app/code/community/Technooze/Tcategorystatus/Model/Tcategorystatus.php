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
class Technooze_Tcategorystatus_Model_Tcategorystatus extends Mage_Core_Model_Abstract
{
    const TCATEGORY_STATUS_ACTIVE_FROM_CODE     = 'active_date_from';
    const TCATEGORY_STATUS_ACTIVE_FROM_LABEL    = 'Active From';

    const TCATEGORY_STATUS_ACTIVE_TO_CODE       = 'active_date_to';
    const TCATEGORY_STATUS_ACTIVE_TO_LABEL      = 'Active To';

    const TCATEGORY_STATUS_ORDER_CUTOFF_CODE       = 'order_cut_off_date';
    const TCATEGORY_STATUS_ORDER_CUTOFF_LABEL      = 'Order Cut Off';

    protected function _construct()
    {
        parent::_construct();
        $this->_init('tcategorystatus/tcategorystatus');
    }

    public function getProduct($productId=0)
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
}
