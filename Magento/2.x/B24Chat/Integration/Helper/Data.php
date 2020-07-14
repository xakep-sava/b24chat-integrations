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

namespace B24Chat\Integration\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
  public function sendData($action = 'none', $data = [])
  {
    // TODO: брать урл из настроек dev или prod
    // TODO: защита токеном от спама
    // TODO: async curl
    try {
      $data = json_encode(array_merge(['action' => $action], $data));
      $ch = curl_init('https://b24chat.com/api/v1/webhook/magento'); // TODO: url from config
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
      ]);
      curl_exec($ch);

//      if ($error = curl_error($ch)) {
//        $this->addLog('sendData (error)', json_encode($error));
//      }
    } catch (\Exception $e) {
//      $this->addLog('sendData (error)', json_encode($e));
    }
  }
}