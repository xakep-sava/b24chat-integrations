<?php

class B24Chat_Integration_Block_Script extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        $helper = Mage::helper('b24chat_integration');
        if (!$helper->isEnabled()) {
            return '';
        }

        return $this->getJS($helper);
    }

    private function getJS($helper)
    {
        $customerInfo = '';
        if ($customer = $helper->getCustomer()) {
            $customerInfo = '<script>window.B24Chat = { customer: { id: ' . $customer->getId() . ', email: \'' .
                $customer->getEmail() . '\'} }</script>';
        }

        return '<script src="' . $helper->getApiUrl() . '/init.js" api="{&quot;url&quot;:&quot;' . $helper->getApiUrl()
            . '/&quot;,&quot;widgets&quot;:{&quot;token&quot;:&quot;' . $helper->getToken() . '&quot;}}"></script>'
            . $customerInfo;
    }
}
