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

    public function getToken()
    {
        return Mage::getStoreConfig('b24chat_integration/general/token');
    }

    public function getApiUrl()
    {
        return Mage::getStoreConfig('b24chat_integration/general/api_url');
    }

    public function sendData($action = 'none', $data = [])
    {
        // TODO: защита токеном от спама
        // TODO: async curl
        try {
            $data = json_encode(array_merge(['action' => $action], $data));
            $ch = curl_init($this->getApiUrl() . '/api/v1/webhook/magento');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ]);
            curl_exec($ch);

//            if ($error = curl_error($ch)) {
//              $this->addLog('sendData (error)', json_encode($error));
//            }
        } catch (Exception $e) {
//            $this->addLog('sendData (error)', json_encode($e));
        }
    }
}
