<?php
/**
 * Copyright © B24 Chat. All rights reserved.
 *
 * PHP version 7
 *
 * @category B24Chat
 * @package  B24Chat_Integration
 * @author   Vladimir Savrov <i@sava.team>
 */

class B24Chat_Integration_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('b24chat_integration/general/enabled')
            && $this->getToken() && $this->getApiUrl();
    }

    public function isWidgetEnabled()
    {
        return Mage::getStoreConfigFlag('b24chat_integration/general/widget_enabled')
            && $this->isEnabled();
    }

    public function isAnalyticsEnabled()
    {
        return Mage::getStoreConfigFlag('b24chat_integration/general/analytics_enabled')
            && $this->isEnabled();
    }

    public function getModuleVersion()
    {
        return Mage::getConfig()->getNode('modules/B24Chat_Integration/version');
    }

    public function getToken()
    {
        return Mage::getStoreConfig('b24chat_integration/general/token');
    }

    public function getApiUrl()
    {
        return 'http://localhost:3070';//Mage::getStoreConfig('b24chat_integration/general/api_url');
    }

    public function getCustomer()
    {
        $customerSession = Mage::getSingleton('customer/session');

        return $customerSession->isLoggedIn() ? $customerSession->getCustomer() : null;
    }

    public function getCacheTime($block = 'widget')
    {
        return Mage::getStoreConfig("b24chat_integration/cache_time/$block");
    }

    public function sendData($action = 'a', $type = 'none', $data = [])
    {
        // TODO: защита токеном от спама
        try {
            $data = json_encode(array_merge(['action' => $type], $data));
            $ch = curl_init($this->getApiUrl() . '/api/v1/webhook/' . $action);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ]);
            curl_exec($ch);

            if ($error = curl_error($ch)) {
                $this->addLog('B24Chat_Integration_Helper_Data - sendData', 'error', json_encode($error));
            }
        } catch (Exception $error) {
            $this->addLog('B24Chat_Integration_Helper_Data - sendData', 'error', json_encode($error));
        }
    }

    public function addLog($method, $type, $message)
    {
        // TODO: отправка ошибок к нам
        Mage::log(sprintf('%s (%s): %s', $method, $type, $message), null, 'b24chat_integration.log', true);
    }
}
