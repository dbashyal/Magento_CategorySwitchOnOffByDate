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

/* @var $installer Technooze_Tcategorystatus_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/*set order's cut off date*/
$installer->deleteTableRow('eav/attribute', 'attribute_code', Technooze_Tcategorystatus_Model_Tcategorystatus::TCATEGORY_STATUS_ORDER_CUTOFF_CODE);

/*
 * or, run this manually
DELETE FROM `eav_attribute` WHERE attribute_code = 'order_cut_off_date';
 */

$installer->addActiveDateAttribute('catalog_category', Technooze_Tcategorystatus_Model_Tcategorystatus::TCATEGORY_STATUS_ORDER_CUTOFF_CODE, array(
    'label' => Technooze_Tcategorystatus_Model_Tcategorystatus::TCATEGORY_STATUS_ORDER_CUTOFF_LABEL,
    'sort_order'    => 997,
));

$installer->endSetup();