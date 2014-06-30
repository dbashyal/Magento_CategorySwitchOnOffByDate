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
class Technooze_Tcategorystatus_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $_today;

    public function getDateToday()
    {
        if(empty($this->_today)){
            $timestamp      = Mage::getModel('core/date')->timestamp(time());
            $this->_today   = date('Y-m-d', $timestamp);
        }
        return $this->_today;
    }
}
