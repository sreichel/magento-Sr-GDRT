<?php

/* @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

// change config values
$configTable = $this->getTable('core_config_data');

$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_ENABLED . "'
    WHERE path = 'google/gdrt_general/gdrt_enable';
    ");
$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_PRODUCT_USE_SKU . "'
    WHERE path = 'google/gdrt_general/gdrt_product_id';
    ");
$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_PRODUCT_PREFIX . "'
    WHERE path = 'google/gdrt_general/gdrt_product_id_prefix';
    ");
$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_PRODUCT_PREFIX_CP_ONLY . "'
    WHERE path = 'google/gdrt_general/gdrt_product_id_prefix_ofcp';
    ");
$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_PRODUCT_SUFFIX . "'
    WHERE path = 'google/gdrt_general/gdrt_product_id_ending';
    ");
$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_PRODUCT_SUFFIX_CP_ONLY . "'
    WHERE path = 'google/gdrt_general/gdrt_product_id_ending_ofcp';
    ");
$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_INCLUDE_TAX . "'
    WHERE path = 'google/gdrt_general/gdrt_tax';
    ");

$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_COVERSION_ID . "'
    WHERE path = 'google/gdrt_general/gc_id';
    ");
$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_COVERSION_LABEL . "'
    WHERE path = 'google/gdrt_general/gc_label';
    ");

    //

$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_PAGETYPE_HOME . "'
    WHERE path = 'google/gdrt_pages/home';
    ");
$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_PAGETYPE_SEARCHRESULTS . "'
    WHERE path = 'google/gdrt_pages/searchresults';
    ");
$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_PAGETYPE_CATEGORY . "'
    WHERE path = 'google/gdrt_pages/category';
    ");
$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_PAGETYPE_PRODUCT . "'
    WHERE path = 'google/gdrt_pages/product';
    ");
$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_PAGETYPE_CART . "'
    WHERE path = 'google/gdrt_pages/cart';
    ");
$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_PAGETYPE_PURCHASE . "'
    WHERE path = 'google/gdrt_pages/purchase';
    ");

    //

$installer->run("
    UPDATE {$configTable}
    SET   path = '" . Sr_Gdrt_Helper_Data::XML_PATH_DEBUG . "'
    WHERE path = 'google/gdrt_debug/show_info';
    ");

$installer->endSetup();
