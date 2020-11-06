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
        $helper = Mage::helper('b24chat_integration');
        if (!$helper->isWidgetEnabled()) {
            return '';
        }

        $cacheKey = $this->_getCacheKey('script');
        $cacheScript = Mage::app()->loadCache($cacheKey);

        if (!empty($cacheScript)) {
            $script = unserialize($cacheScript);
        } else {
            $script = $this->getJS($helper);
            $this->_saveCache($cacheKey, $script);
        }

        return $script;
    }

    /**
     * @param $helper
     * @return string
     */
    private function getJS($helper)
    {
        $customerInfo = '';
        if ($customer = $helper->getCustomer()) {
            $primaryAddress = $customer->getPrimaryBillingAddress();
            $name = sprintf('%s %s', $customer->getFirstname(), $customer->getLastname());
            $phone = $primaryAddress ? sprintf(', phone: "%s"', $primaryAddress->getTelephone()) : '';

            // TODO: защитить json от левых символов
            $customerInfo = sprintf(
                '<script>window.B24Chat = { customer: { id: %d, email: "%s", name: "%s"%s } }</script>', $customer
                ->getId(), $customer->getEmail(), $name, $phone);
        }

        return '<script src="' . $helper->getApiUrl() . '/init.js" api="{&quot;url&quot;:&quot;' . $helper->getApiUrl()
            . '/&quot;,&quot;widgets&quot;:{&quot;token&quot;:&quot;' . $helper->getToken() . '&quot;}}"></script>'
            . $customerInfo;
    }
}
