<?php
class Dzangocart {
  
  public static $api_url = ''; 
  public static $api_credentials = array(); 
  public static $key = ''; //your dzangocart secret key
  public static $cookie_lifetime = 36000;

  protected static function processQuery($query_info,
                                         $params = array(),
                                         $with_headers = false) {
    if (!function_exists('curl_init')) {
      throw new Exception('cURL module is not available.');
    }

    $query = array_merge(self::$api_credentials, $params);
    $query['timestamp'] = time();
    $url = self::$api_url . $query_info . ((false !== strpos($query_info, '?')) ? '&' : '?') . http_build_query($query, '', '&');

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
    //curl_setopt($curl, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
    //curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
    if ($with_headers) { curl_setopt($curl, CURLOPT_HEADER, true); }

    $response = curl_exec($curl);
    if (curl_errno($curl)) {
      throw new Exception(curl_error($curl));
    }

    curl_close($curl);

    if ($with_headers) {
      // Transform headers in array, suitable with sfWebResponse class
      list ($headerString, $response) = explode("\r\n\r\n", $response, 2);
      $response_headers = explode("\r\n", $headerString);
      $headers = array();
      foreach ($response_headers as $header) {
        $header_chunks = explode(':', $header);
        if (sizeof($header_chunks) == 2) {
          $headers[trim($header_chunks[0])] = trim($header_chunks[1]);
        }
      }
    }

    // first, try to decrypt response
    $decrypted_response = json_decode(self::decrypt($response, self::$key), true);
    // if $decrypted_response is a proper array, then response was encrypted
    $decoded_response = json_decode($response, true);
    $response = is_array($decrypted_response) ? $decrypted_response : ($decoded_response ? $decoded_response : $response);

    if (is_array($response) && array_key_exists('error', $response)) {
      $error = $response['error'];
      $message = 'An unidentified error has occurred.';
      if (is_string($error)) { $message = $error; }
      if (is_array($error) && array_key_exists('message', $error)) {
        $message = $error['message'];
      }
      throw new Exception($message);
    }

    return ($with_headers) ? array($headers, $response) : $response;
  }

  public static function getCarts($customer_code, $affiliate_id = null, $test = false, $params = array()) {
    $url = '/cart/get.json';
    $query = $params;
    $query['customer'] = $customer_code;
    if ($affiliate_id) { $query['affiliate'] = $affiliate_id; }
    $query['test'] = $test;
    
    return self::processQuery($url, $query);
  }

  public static function getOrders($customer_code = null, $affiliate_id, $params = array(), $test = false, $sort_by = null, $list_by = null, $limit = null, $offset = 0) {
    $url = '/orders.json';
    $query = $params;
    if ($customer_code) { $query['customer'] = $customer_code; }
    if ($affiliate_id) { $query['affiliate'] = $affiliate_id; }
    if ($test) { $query['test'] = true; }
    if ($list_by) { $query['list_by'] = $list_by; }
    if ($sort_by) { $query['sort_by'] = $sort_by; }
    if ($limit) { $query['limit'] = $limit; }
    $query['offset'] = $offset;

    return self::processQuery($url, $query);
  }
  
  public function getPurchase($item_id) {
    $url = '/purchase/get.json';
    $query['item_id'] = $item_id;
    
    return self::processQuery($url, $query);
  }
  
  public static function getPurchases($customer_code = null, $affiliate_id = null, $params = array(), $test = false, $sort_by = null, $list_by = null, $limit = null, $offset = 0) {
    $url = '/purchase/list.json';
    $query = $params;
    if ($customer_code) { $query['customer'] = $customer_code; }
    if ($affiliate_id) { $query['affiliate_id'] = $affiliate_id; }
    if ($test) { $query['test'] = true; }
    if ($list_by) { $query['list_by'] = $list_by; }
    if ($sort_by) { $query['sort_by'] = $sort_by; }
    if ($limit) { $query['limit'] = $limit; }
    $query['offset'] = $offset;
    
    return self::processQuery($url, $query);
  }

  public static function getItems($customer_code = null, $affiliate_id = null, $params = array(), $test = false, $sort_by = null, $list_by = null) {
    $url = '/sales.json';
    $query = $params;
    if ($customer_code) { $query['customer'] = $customer_code; }
    if ($affiliate_id) { $query['affiliate'] = $affiliate_id; }
    if ($test) { $query['test'] = true; }
    if ($list_by) { $query['list_by'] = $list_by; }
    if ($sort_by) { $query['sort_by'] = $sort_by; }

    return self::processQuery($url, $query);
  }

  public static function getPOTransactions(array $params = array(), $test = false) {
    $url = '/po/transactions.json';
    $query = $params;
    if ($test) { $query['test'] = true; }
    
    return self::processQuery($url, $query);
  }

  public static function getSogenactif($date_from, $date_to, $test = null) {
    $url = '/sips/transactions.json';
    $query = array();
  
    $query['date_from'] = $date_from;
    $query['date_to'] = $date_to;
    if (!$test) {
      $query['merchant_id'] = sfConfig::get('app_dzangocart_sips_merchant_id');
    }
  
    return self::processQuery($url, $query);
  }  

  public static function getSogenactifTransactions(array $params = array(), $test = false) {
    $url = '/sogenactif.json';
    $query = $params;
    if ($test) { $query['test'] = true; }
    else { $query['merchant_id'] = sfConfig::get('app_dzangocart_sips_merchant_id'); }

    return self::processQuery($url, $query);
  }

  public static function getSipsTransactions(array $params = array(), $test = false, $sort_by = null, $list_by = null, $limit = null, $offset = 0) {
    $url = '/sips.json';
    $query = $params;

    if ($test) { $query['test'] = true; }
    else { $query['merchant_id'] = sfConfig::get('app_dzangocart_sips_merchant_id'); }

    if ($list_by) { $query['list_by'] = $list_by; }
    if ($sort_by) { $query['sort_by'] = $sort_by; }
    if ($limit) { $query['limit'] = $limit; }
    $query['offset'] = $offset;

    return self::processQuery($url, $query);
  }

  public static function getPurchaseOrderTransactions(array $params = array(), $test = false, $sort_by = null, $list_by = null, $limit = null, $offset = 0) {
    $url = '/po.json';
    $query = $params;
    if ($test) { $query['test'] = true; }
    if ($list_by) { $query['list_by'] = $list_by; }
    if ($sort_by) { $query['sort_by'] = $sort_by; }
    if ($limit) { $query['limit'] = $limit; }
    $query['offset'] = $offset;

    return self::processQuery($url, $query);
  }

  public static function getPaypalDirectTransactions(array $params = array(),
                                                     $test = false,
                                                     $sort_by = null,
                                                     $list_by = null,
                                                     $limit = null,
                                                     $offset = 0) {
    $url = '/paypal/direct.json';
    $query = $params;
    if ($test) { $query['test'] = true; }
    if ($list_by) { $query['list_by'] = $list_by; }
    if ($sort_by) { $query['sort_by'] = $sort_by; }
    if ($limit) { $query['limit'] = $limit; }
    $query['offset'] = $offset;
    
    return self::processQuery($url, $query);
  }  

  public static function getPaypalExpressTransactions(array $params = array(), 
                                                      $test = false,
                                                      $sort_by = null,
                                                      $list_by = null,
                                                      $limit = null,
                                                      $offset = 0) {
    $url = '/paypal/express.json';
    $query = $params;
    if ($test) { $query['test'] = true; }
    if ($list_by) { $query['list_by'] = $list_by; }
    if ($sort_by) { $query['sort_by'] = $sort_by; }
    if ($limit) { $query['limit'] = $limit; }
    $query['offset'] = $offset;
    
    return self::processQuery($url, $query);
  }

  public static function getCustomer(array $codes) {
    $url = '/customer.json';
    $query = array('code' => implode(',', $codes));

    return self::processQuery($url, $query);
  }
  
  public static function getOrder($order_no) {
    $url = '/order.json/' . $order_no;
    $query = array();
    
    return self::processQuery($url, $query);    
  }

  public static function cancelOrder($order_id, $gateway_id = null, $date = null) {
    $url = '/order/cancel.json'  ;
    $query = array('order_id' => $order_id,
                   'gateway_id' => $gateway_id,
                   'date' => $date ? $date : date('Y-m-d H:j:s'));
    
    return self::processQuery($url, $query);
  }
  
  public static function createCustomer($data, $check_only = false) {
    $data = self::encode($data, self::$key, self::$cookie_lifetime);
    
    $url = '/customer/create.json';
    $query = array('customer_data' => $data,
                   'check_only' => $check_only);
    
    return self::processQuery($url, $query);
  }
  
  public static function updateCustomer($data) {
    $data = self::encode($data, self::$key, self::$cookie_lifetime);
    
    $url = '/customer/update.json';
    $query = array('customer_data' => $data);

    return self::processQuery($url, $query);
  }
  
  // [OP 2011-09-14] Deprecate in favour of updateCustomer
  public static function updateCustomerData($data) {
    $data = self::encode($data, self::$key, self::$cookie_lifetime);
    
    $url = '/updateCustomer.json';
    $query = array('customer_data' => $data);

    return self::processQuery($url, $query);
  }
  
  public static function updateCustomerCode($email, $code) {
    $url = '/customer/updateCode?sf_format=json';
    $query = array('email' => $email,
                   'code' => $code); // Required for dzangocart 0.11 as route is missing   
    
    return self::processQuery($url, $query);
  }

  public static function cancelItem($item_id, $refund_info = null) {
    $url = '/purchase/cancel.json'  ;
    $query = array('item_id' => $item_id);
    if ($refund_info) { $query['refund_info'] = $refund_info; }
    
    return self::processQuery($url, $query);
  }

  public static function getInvoice($params) {
    $url = '/order/invoice';    
    return self::processQuery($url, $params, true);
  }

  public static function getRefundInfoForm() {
    $url = '/getRefundInfoForm.json';
    $query = array();
    return self::processQuery($url, $query);
  }

  public static function set_cookie($array, $key, $lifetime) {
    sfContext::getInstance()->getResponse()->setCookie('dzangocart', 
                                                       self::encode($array, $key, $lifetime), 
                                                       $lifetime, 
                                                       '/');
  }

  public static function remove_cookie() {
    sfContext::getInstance()->getResponse()->setCookie('dzangocart', 
                                                       '', 
                                                       time() - 3600, 
                                                       '/');
  }

  public static function encode($data, $key, $expires) {
    if (!array_key_exists('expires', $data)) {
      $data['expires'] = date('c', time() + $expires);
    }
    $userdata = json_encode($data);
    return self::encrypt($userdata, $key);
  }
  
  public static function encrypt($data, $key) {
    set_error_handler('Dzangocart::handleError');
    try {
      require_once(dirname(__FILE__).'/pcrypt_0.0.3-stable/pcrypt.php');
      $cipher = new pcrypt($key);
      $result = $cipher->cipher($data);
    }
    catch (ErrorException $e) {
      $result = null;
    }
    restore_error_handler();
    return $result;
  }

  public static function decrypt($data, $key) {
    set_error_handler('Dzangocart::handleError');
    try {
      require_once(dirname(__FILE__).'/pcrypt_0.0.3-stable/pcrypt.php');
      $cipher = new pcrypt($key);
      $result = $cipher->decipher($data);
    }
    catch (ErrorException $e) {
      $result = null;
    }
    restore_error_handler();
    return $result;
  }
  
  /*
   * FIXME [OP 2011-08-26] Ths method cannot go in here. 
   */
  public static function getUserByCustomerCode($code) {
    return PorotUserPeer::retrieveByUsername($code);
  }

  public static function createUserFromDzangocart(array $data) {
    $user = new sfGuardUser();
    $user->setUsername($data['email']);
    Dzangocart::updateUser($user, $data);

    $password = Password::generate(8);
    $user->setPassword($password);
    $user->save();

    $msg = new AutoregisterDzangocartMessage($user, $password);
    try { $msg->send(); }
    catch (Exception $e) { $this->error(800, $e->getMessage()); }
    return $user;
  }

  public static function updateUser($user, array $data) {
    $profile = $user->getProfile();
    $profile->fromArray($data, BasePeer::TYPE_FIELDNAME);

    if (array_key_exists('billing_address', $data)) {
      $address_data = $data['billing_address'];
      $address = $profile->getAddress();

      if (array_key_exists('line1', $address_data)) { $address->setLine1($address_data['line1']); }
      if (array_key_exists('line2', $address_data)) { $address->setLine2($address_data['line2']); }
      if (array_key_exists('city', $address_data)) { $address->setCity($address_data['city']); }
      if (array_key_exists('zip', $address_data)) { $address->setZip($address_data['zip']); }
      if (array_key_exists('country', $address_data)) { $address->setCountryId($address_data['country']); }
    }

    if (array_key_exists('affiliate', $data)) {
      $affiliate = AffiliatePeer::getByDzangocartId($data['affiliate']);
      $profile->setAffiliate($affiliate);
    }

    $profile->save();
  }

  public static function handleError($errno, $errstr, $errfile, $errline) {
    if (0 === error_reporting()) {
      return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
  }
}
