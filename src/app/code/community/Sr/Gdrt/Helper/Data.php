<?php

/**
 * Class Sr_Gdrt_Helper_Data
 */
class Sr_Gdrt_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_COVERSION_ID             = 'google/sr_gdrt/coversion_id';
    const XML_PATH_COVERSION_LABEL          = 'google/sr_gdrt/coversion_label';

    const XML_PATH_USE_GTM                  = 'google/sr_gdrt/use_gtm';

    const XML_PATH_ENABLED                  = 'google/sr_gdrt/enabled';
    const XML_PATH_PRODUCT_USE_SKU          = 'google/sr_gdrt/product_use_sku';
    const XML_PATH_PRODUCT_PREFIX           = 'google/sr_gdrt/product_prefix';
    const XML_PATH_PRODUCT_SUFFIX           = 'google/sr_gdrt/product_suffix';
    const XML_PATH_PRODUCT_PREFIX_CP_ONLY   = 'google/sr_gdrt/product_prefix_cp_only';
    const XML_PATH_PRODUCT_SUFFIX_CP_ONLY   = 'google/sr_gdrt/product_suffix_cp_only';
    const XML_PATH_INCLUDE_TAX              = 'google/sr_gdrt/include_tax';
    const XML_PATH_DEBUG                    = 'google/sr_gdrt/debug';

    const XML_PATH_PAGETYPES_ARRAY          = 'google/sr_gdrt_pages';

    const XML_PATH_PAGETYPE_HOME            = 'google/sr_gdrt_pages/home';
    const XML_PATH_PAGETYPE_SEARCHRESULTS   = 'google/sr_gdrt_pages/searchresults';
    const XML_PATH_PAGETYPE_CATEGORY        = 'google/sr_gdrt_pages/category';
    const XML_PATH_PAGETYPE_PRODUCT         = 'google/sr_gdrt_pages/product';
    const XML_PATH_PAGETYPE_CART            = 'google/sr_gdrt_pages/cart';
    const XML_PATH_PAGETYPE_PURCHASE        = 'google/sr_gdrt_pages/purchase';

    const GDRT_PAGE_HOME                    = 'home';
    const GDRT_PAGE_CATEGORY                = 'category';
    const GDRT_PAGE_CART                    = 'cart';
    const GDRT_PAGE_PRODUCT                 = 'product';
    const GDRT_PAGE_PURCHASE                = 'purchase';
    const GDRT_PAGE_SEARCHRESULTS           = 'searchresults';
    const GDRT_PAGE_DEFAULT                 = 'other';

    protected $_moduleName = 'Sr_Gdrt';

    /**
     * @param string $moduleName
     * @return bool
     */
    public function isModuleEnabled($moduleName = null)
    {
        if (is_null($moduleName)) {
            $moduleName = $this->_moduleName;
        };

        if (parent::isModuleEnabled($moduleName)) {
            return $this->canShow() || $this->canShowDebug();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function canShow()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED) && !empty(Mage::getStoreConfig(self::XML_PATH_COVERSION_ID));
    }

    /**
     * @return bool
     */
    public function canShowDebug()
    {
        if (Mage::getStoreConfigFlag(self::XML_PATH_DEBUG)) {
            $ips = Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_DEV_ALLOW_IPS);
            if (!$ips) {
                return true;
            }

            $ips = array_filter(array_map('trim', explode(',', $ips)));
            if (!count($ips) || in_array(Mage::helper('core/http')->getRemoteAddr(), $ips)) {
                return true;
            }
        }
        return false;
    }
}
