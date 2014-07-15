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
class Technooze_Tcategorystatus_Model_Resource_Setup extends Mage_Catalog_Model_Resource_Setup
{
    public function addActiveDateAttribute($entityType, $attributeCode, $values=array())
    {
        $default = array(
                    'label' => 'Active From',
                    'group' => 'Category Status',
                    'type' => 'datetime',
                    'input' => 'date',
                    'backend' => 'eav/entity_attribute_backend_datetime',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'required' => 0,
                    'user_defined' => 0,
                    'filterable_in_search' => 0,
                    'is_configurable' => 0,
                    'used_in_product_listing' => 1,
                );

        $data = array_merge($default, $values);
        $this->addAttribute($entityType, $attributeCode, $data);
    }
}