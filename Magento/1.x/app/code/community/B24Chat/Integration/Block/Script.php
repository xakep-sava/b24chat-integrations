<?php

class B24Chat_Integration_Block_Script extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        $helper = Mage::helper('b24chat_integration');
        if (!$helper->isEnabled()) {
            return '';
        }

        return $this->getJS($helper->getToken(), $helper->getApiUrl());
    }

    private function getJS($token, $apiUrl)
    {
        return '<script src="' . $apiUrl . '/init.js" api="{&quot;url&quot;:&quot;' . $apiUrl . '/&quot;,' .
            '&quot;widgets&quot;:{&quot;token&quot;:&quot;' . $token . '&quot;}}"></script>';
    }
}
