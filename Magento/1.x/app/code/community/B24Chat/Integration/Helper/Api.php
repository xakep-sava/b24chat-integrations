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

class B24Chat_Integration_Helper_Api extends Mage_Core_Helper_Abstract
{
    public function get($action = '', $url = 'https://b24chat.com') // TODO: url from take settings
    {
        // TODO: защита токеном от спама
        $helper = Mage::helper('b24chat_integration');

        try {
            $ch = curl_init(sprintf('%s/api/v1/%s', $url, $action));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            curl_close($ch);

            if ($error = curl_error($ch)) {
                $helper->addLog('B24Chat_Integration_Helper_Api - get', 'error', json_encode($error));
                return false;
            }

            try {
                if (!is_object($response)) {
                    $response = json_decode($response);
                }
                return $response->success ? $response : false;
            } catch (Exception $error) {
                $helper->addLog('B24Chat_Integration_Helper_Api - get', 'error', json_encode($error));
                return false;
            }
        } catch (Exception $error) {
            $helper->addLog('B24Chat_Integration_Helper_Api - get', 'error', json_encode($error));
            return false;
        }
    }
}