<?php

class B24Chat_Integration_Block_Script extends B24Chat_Integration_Block_Base
{
    const CACHE_GROUP = 'B24Chat_Integration_Script';

    /**
     * @return mixed|string
     * @throws Mage_Core_Exception
     */
    protected function _toHtml()
    {
        // TODO: аналитику нужно поместить в head для большей точности
        $helper = Mage::helper('b24chat_integration');
        $analytics = '';
        $widget = '';

        if ($helper->isAnalyticsEnabled()) {
            $analytics = $this->getAnalyticsJS($helper);
        }

        if ($helper->isWidgetEnabled()) {
            $widget = $this->getWidgetJS($helper);
        }

        return $analytics . $this->getAnalyticsData($helper) . $widget;
    }

    /**
     * @param $helper
     * @return string
     */
    private function getWidgetJS($helper)
    {
        return '<script src="' . $helper->getApiUrl() . '/init.js?v=' . $helper->getModuleVersion() .
            '" api="{&quot;url&quot;:&quot;' . $helper->getApiUrl() .
            '/&quot;,&quot;widgets&quot;:{&quot;token&quot;:&quot;' . $helper->getToken() . '&quot;}}"></script>';
    }

    /**
     * @param $helper
     * @return string
     */
    private function getAnalyticsJS($helper)
    {
        return '<script id="b24chat-a" defer src="' . $helper->getApiUrl() . '/a.js?v=' . $helper->getModuleVersion() .
            '" token="' . $helper->getToken() . '"></script>';
    }

    /**
     * @param $helper
     * @return string
     */
    private function getAnalyticsData($helper)
    {
        $analyticsData = '';
        $customerData = '';
        $categoryData = '';
        $productData = '';

        $cacheKeyCustomer = $this->_getCacheKey('customer');
        $cacheCustomer = Mage::app()->loadCache($cacheKeyCustomer);

        if (!empty($cacheCustomer)) {
            $customerData = unserialize($cacheCustomer);
        }

        if (empty($customerData) && Mage::getSingleton('customer/session')->isLoggedIn() && $customer = $helper->getCustomer()) {
            $primaryAddress = $customer->getPrimaryBillingAddress();
            $customerData .= $customer->getFirstname() ? sprintf(', firstName: "%s"', $customer->getFirstname()) : '';
            $customerData .= $customer->getLastname() ? sprintf(', lastName: "%s"', $customer->getLastname()) : '';
            $customerData .= $customer->getGender() ?
                sprintf(', gender: "%s"', $customer->getGender() === '1' ? 'man' : 'woman') : '';

            if ($primaryAddress) {
                $customerData .= $primaryAddress->getCity()
                    ? sprintf(', city: "%s"', $primaryAddress->getCity()) : '';
                $customerData .= $primaryAddress->getCountry()
                    ? sprintf(', country: "%s"', $primaryAddress->getCountry()) : '';
                $customerData .= $primaryAddress->getPostcode()
                    ? sprintf(', postcode: "%s"', $primaryAddress->getPostcode()) : '';
                $customerData .= $primaryAddress->getSteet()
                    ? sprintf(', street: "%s"', $primaryAddress->getSteet()) : '';
                $customerData .= $primaryAddress->getTelephone() ?
                    sprintf(', phone: "%s"', $primaryAddress->getTelephone()) : '';
            }

            $customerData = sprintf('window.B24Chat.customer = { id: %d, email: "%s"%s};', $customer->getId(),
                $customer->getEmail(), $customerData);
            $this->_saveCache($cacheKeyCustomer, $customerData);
        } else {
            $customerData = '';
            $this->_saveCache($cacheKeyCustomer, $customerData);
        }

        $product = Mage::registry('current_product');
        if ($product) {
            $productData = sprintf('window.B24Chat.product = { id: %d };', $product->getId());
        }

        $category = Mage::registry('current_category');
        if ($category) {
            $categoryData = sprintf('window.B24Chat.category = { id: %d };', $category->getId());
        }

        if (!empty($customerData) || !empty($productData) || !empty($categoryData)) {
            // TODO: защитить json от левых символов
            $analyticsData = sprintf(
                '<script>if(!window.B24Chat){window.B24Chat = {}}%s%s%s</script>',
                $productData, $customerData, $categoryData);
        }

        return $analyticsData;
    }
}
