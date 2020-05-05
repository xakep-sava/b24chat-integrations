<?php

class ControllerExtensionAnalyticsB24Chat extends Controller
{
  public function order($route, $data)
  {
    $orderId = current($data);

    $this->sendData('order', [
      'order' => $this->getOrder($orderId),
      'products' => $this->getOrderProducts($orderId),
      'customer' => $this->getSessionCustomerData() // TODO: не всегда верный юзер, к примеру в админке смена статуса
    ]);
  }

  public function addCart($route, $data)
  {
    $this->sendData('cart.add', [
      'customer' => $this->getSessionCustomerData(),
      'product' => $data
    ]);
  }

  public function editCart($route, $data)
  {
    $this->sendData('cart.edit', [
      'customer' => $this->getSessionCustomerData(),
      'product' => $data
    ]);
  }

  public function removeCart($route, $data)
  {
    $this->sendData('cart.remove', [
      'customer' => $this->getSessionCustomerData(),
      'products' => count($data) ? $data[0]->getProducts() : []
    ]);
  }

  private function getSessionCustomerData()
  {
    $customerId = $this->session->data['user_id'] ?? null;
    $device = $this->session->data['device'];
    $language = $this->session->data['language'];
    $currency = $this->session->data['currency'];
    $customer = $this->getCustomer($customerId);

    return array_merge([
      'device' => $device,
      'language' => $language,
      'currency' => $currency
    ], $customer);
  }

  private function getCustomer($customerId)
  {
    $query = false;
    try {
      $query = $this->db->query("SELECT customer_id, store_id, firstname, lastname, email, telephone FROM " . DB_PREFIX .
        "customer WHERE customer_id = '" . (int)$customerId . "'")->row;
    } catch (Exception $e) {
      $this->addLog('getOrder (error)', json_encode($e));
    }

    return $query;
  }

  public function getOrder($orderId)
  {
    $query = false;
    try {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order as o " .
        "JOIN " . DB_PREFIX . "order_status as os ON os.order_status_id = o.order_status_id " .
        "WHERE o.order_id =  " . (int)$orderId)->row;
    } catch (Exception $e) {
      $this->addLog('getOrder (error)', json_encode($e));
    }

    return $query;
  }

  public function getOrderProducts($orderId)
  {
    $query = false;
    try {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = " . (int)$orderId)->rows;
    } catch (Exception $e) {
      $this->addLog('getOrder (error)', json_encode($e));
    }

    return $query;
  }

  private function sendData($action = 'none', $data = [])
  {
    // TODO: брать урл из настроек dev или prod
    // TODO: защита токеном от спама
    // TODO: async curl
    try {
      $data = json_encode(array_merge(['action' => $action], $data));
      $ch = curl_init('https://d0b44380.ngrok.io/api/v1/webhook/opencart');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
      ]);
      curl_exec($ch);

      if ($error = curl_error($ch)) {
        $this->addLog('sendData (error)', json_encode($error));
      }
    } catch (Exception $e) {
      $this->addLog('sendData (error)', json_encode($e));
    }
  }

  private function addLog($action = '', $message = '', $file = 'B24Chat.log')
  {
    try {
      $log = new Log($file);
      $log->write($action . ': ' . $message);
    } catch (Exception $e) {
    }
  }
}