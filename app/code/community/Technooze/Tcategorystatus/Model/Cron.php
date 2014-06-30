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
class Technooze_Tcategorystatus_Model_Cron
{
    private $_fromDate;
    private $_toDate;
    private $_today;

    public function changeCategoryStatus(Mage_Cron_Model_Schedule $schedule)
    {
        $this->_fromDate    = Technooze_Tcategorystatus_Model_Tcategorystatus::TCATEGORY_STATUS_ACTIVE_FROM_CODE;
        $this->_toDate      = Technooze_Tcategorystatus_Model_Tcategorystatus::TCATEGORY_STATUS_ACTIVE_TO_CODE;
        $this->_today       = Mage::helper('tcategorystatus')->getDateToday();

        // first enable new categories that meet date requirements
        $this->enableNewCategories();

        // then disable expired categories
        $this->disableExpiredCategories();
    }

    public function enableNewCategories()
    {
        /* @var $collection Mage_Catalog_Model_Resource_Category_Collection */
        $collection = Mage::getModel('catalog/category')
                        ->getCollection()
                        ->addAttributeToFilter($this->_fromDate, array('lteq' => $this->_today))
                        ->addAttributeToFilter($this->_toDate, array('gteq' => $this->_today))
                        ->addAttributeToFilter('is_active', array('neq' => 1));

        // set all matching categories to active
        foreach($collection as $category){
            /* @var $category Mage_Catalog_Model_Category */
            $category->setIsActive(1)->save();
        }
    }

    public function disableExpiredCategories()
    {
        /* @var $collection Mage_Catalog_Model_Resource_Category_Collection */
        $collection = Mage::getModel('catalog/category')
                        ->getCollection()
                        ->addAttributeToFilter('is_active', 1)
                        ->addAttributeToFilter(
                            array(
                                array(
                                    'attribute' => $this->_fromDate,
                                    'gt' => $this->_today
                                ),
                                array(
                                    'attribute' => $this->_toDate,
                                    'lt' => $this->_today
                                )
                            )
                        );

        // set all matching categories to in active
        foreach($collection as $category){
            /* @var $category Mage_Catalog_Model_Category */
            $category->setIsActive(0)->save();
        }
    }
}
