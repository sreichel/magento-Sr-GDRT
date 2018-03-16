<?php

/**
 * Class Sr_Gdrt_Block_Script
 */
class Sr_Gdrt_Block_Init extends Mage_Core_Block_Template
{
    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getEcommProdid($product)
    {
        $prefix = $suffix = '';

        $value = $this->getConfig('use_product_id') ? $product->getId() : $product->getSku();

        $isConfiguarable = in_array($product->getTypeId(), array(
                Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                Mage_Catalog_Model_Product_Type::TYPE_GROUPED)
        );

        $prefixOnlyForConfiguarables = $this->getConfig('prefix_cp_only');
        if (!$prefixOnlyForConfiguarables || ($isConfiguarable && $prefixOnlyForConfiguarables)) {
            $prefix = $this->getConfig('prefix');
        }

        $suffixOnlyForConfiguarables = $this->getConfig('suffix_cp_only');
        if (!$suffixOnlyForConfiguarables || ($isConfiguarable && $suffixOnlyForConfiguarables)) {
            $suffix = $this->getConfig('suffix');
        }

        return $prefix . $value . $suffix;
    }

    /**
     * @return array
     */
    public function getEcommParams()
    {
        if ($this->hasData('ecomm_params')) {
            return $this->getData('ecomm_params');
        }

        $params = array();
        $type = $this->getEcommPagetype();
        switch ($type) {
            case Sr_Gdrt_Helper_Data::GDRT_PAGE_CATEGORY:
                $params = $this->_getCategoryData($params);
                break;

            case Sr_Gdrt_Helper_Data::GDRT_PAGE_PRODUCT:
                $product = Mage::registry('current_product');
                $totalvalue = $this->_getProductPrice($product, $this->getConfig('include_tax'));

                $params = array(
                    'isSaleItem' => (int)($product->getFinalPrice() < $product->getPrice()),
                    'ecomm_prodid' => $this->getEcommProdid($product),
                    'ecomm_totalvalue' => (float)number_format($totalvalue, '2', '.', '')
                );

                $params = $this->_getCategoryData($params);

                unset($product);
                break;

            case Sr_Gdrt_Helper_Data::GDRT_PAGE_CART:
                $cart = Mage::getSingleton('checkout/session')->getQuote();
                $items = $cart->getAllVisibleItems();
                if (count($items) > 0) {
                    $params = $this->_getProductData($items);
                } else {
                    $params = array('ecomm_pagetype' => Sr_Gdrt_Helper_Data::GDRT_PAGE_DEFAULT);
                }

                unset($cart, $items);
                break;

            case Sr_Gdrt_Helper_Data::GDRT_PAGE_PURCHASE:
                $lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
                $order = Mage::getModel('sales/order')->loadByIncrementId($lastOrderId);
                $params = $this->_getProductData($order->getAllItems());
                unset($order);
                break;

            default:
                break;
        }

        if (!isset($params['ecomm_pagetype'])) {
            $params['ecomm_pagetype'] = $type;
        }

        $this->setData('ecomm_params', $params);

        return $this->getData('ecomm_params');
    }

    /**
     * @return string
     */
    public function getEcommPagetype()
    {
        if (!$this->hasData('ecomm_pagetype')) {
            $type = Sr_Gdrt_Helper_Data::GDRT_PAGE_DEFAULT;
            $pages = Mage::getStoreConfig(Sr_Gdrt_Helper_Data::XML_PATH_PAGETYPES_ARRAY);
            $request = $this->getRequest();
            foreach ($pages as $page => $urlPath) {
                $urlPath = rtrim($urlPath, '/');
                if ($request->getModuleName() . '/' . $request->getControllerName() . '/' . $request->getActionName() == $urlPath
                    || $request->getModuleName() . '/' . $request->getControllerName() == $urlPath
                ) {
                    $type = $page;
                    break;
                }
            }
            $this->setData('ecomm_pagetype', $type);
        }
        return $this->getData('ecomm_pagetype');
    }

    /**
     * @return string
     */
    public function getGoogleTagParams()
    {
        return Mage::helper('core')->jsonEncode($this->getEcommParams());
    }

    /**
     * @return string
     */
    public function getUrlParams()
    {
        $result = array();
        foreach ($this->getEcommParams() as $key => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $result[] = $key . '=' . $value;
        }
        return urlencode(implode(';', $result));
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param bool $inclTax
     * @return float|int|mixed
     */
    protected function _getProductPrice($product, $inclTax)
    {
        $totalvalue = 0;

        // check if we are handling grouped products
        if ($product->getTypeId() == 'grouped') {
            $groupedSimpleProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
            $groupedPrices = array();

            foreach ($groupedSimpleProducts as $gSimpleProduct) {
                $groupedPrices[] = $this->_getProductPrice($gSimpleProduct, $inclTax);
            }
            $totalvalue = min($groupedPrices);
        } else { // handle other product types
            $_price = Mage::helper('tax')->getPrice($product, $product->getPrice(), $inclTax);
            $_specialPrice = Mage::helper('tax')->getPrice($product, $product->getSpecialPrice(), $inclTax);
            $_finalPrice = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), $inclTax);

            if ($_price == $_finalPrice) { // no special price
                $totalvalue = (float)$_price;
            } else { // get special price
                if ((float)$_finalPrice > 0 && (float)$_finalPrice <= (float)$_price) {
                    $totalvalue = (float)$_finalPrice;
                } else {
                    $totalvalue = (float)$_price;
                }
            }
        }
        return $totalvalue;
    }

    /**
     * @param array $productCollection
     * @return array
     */
    protected function _getProductData(array $productCollection)
    {
        $totalvalue = 0;
        $params = array();
        foreach ($productCollection as $item) {
            /* @var Mage_Sales_Model_Order_Item $item */
            $params['ecomm_prodid'][] = $this->getEcommProdid($item->getProduct());

            switch ($this->getEcommPagetype()) {
                case Sr_Gdrt_Helper_Data::GDRT_PAGE_CART:
                    $params['ecomm_quantity'][] = (int)$item->getQty();
                    break;
                case Sr_Gdrt_Helper_Data::GDRT_PAGE_PURCHASE:
                    $params['ecomm_quantity'][] = (int)$item->getQtyToInvoice();
                    break;
            }

            $totalvalue += $this->getConfig('include_tax') ? $item->getRowTotalInclTax() : $item->getRowTotal();
        }
        $params['ecomm_totalvalue'] = (float)number_format($totalvalue, '2', '.', '');

        unset($productCollection, $item);

        return $params;
    }

    /**
     * @param array $params
     * @return array
     */
    protected function _getCategoryData(array $params)
    {
        if (Mage::registry('current_category')) {
            $params['ecomm_category'] = Mage::registry('current_category')->getName();
        }
        return $params;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getConfig($key)
    {
        if (!$this->hasData('gdrt')) {
            $data = array(
                'coversion_id'      => Mage::getStoreConfig(Sr_Gdrt_Helper_Data::XML_PATH_COVERSION_ID),
                'coversion_label'   => Mage::getStoreConfig(Sr_Gdrt_Helper_Data::XML_PATH_COVERSION_LABEL),
                'use_gtm'           => Mage::getStoreConfigFlag(Sr_Gdrt_Helper_Data::XML_PATH_USE_GTM),
                'prefix'            => Mage::getStoreConfig(Sr_Gdrt_Helper_Data::XML_PATH_PRODUCT_PREFIX),
                'suffix'            => Mage::getStoreConfig(Sr_Gdrt_Helper_Data::XML_PATH_PRODUCT_SUFFIX),
                'prefix_cp_only'    => Mage::getStoreConfigFlag(Sr_Gdrt_Helper_Data::XML_PATH_PRODUCT_PREFIX_CP_ONLY),
                'suffix_cp_only'    => Mage::getStoreConfigFlag(Sr_Gdrt_Helper_Data::XML_PATH_PRODUCT_SUFFIX_CP_ONLY),
                'use_sku'           => Mage::getStoreConfigFlag(Sr_Gdrt_Helper_Data::XML_PATH_PRODUCT_USE_SKU),
                'include_tax'       => Mage::getStoreConfigFlag(Sr_Gdrt_Helper_Data::XML_PATH_INCLUDE_TAX)
            );
            $config = new Varien_Object($data);
            $this->setData('gdrt', $config);
        }
        return $this->getData('gdrt')->getData($key);
    }
}
