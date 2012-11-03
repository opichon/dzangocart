<?php
class DzangocartOrder extends DzangocartObject {

  const CSS_CLASS = 'order';
  const CSS_CLASS_CANCELLED = 'cancelled';
  const CSS_CLASS_CANCELLATION = 'cancellation';
  const CSS_CLASS_CREDIT = 'credit';
  const CSS_CLASS_UNPAID = 'unpaid';
  const CSS_CLASS_PAID = 'paid';
  
  protected $customer;
  protected $items;
  
  public static function getOrder($order_id) {
    $order_class = static::getOrderClass();
    return new $order_class(Dzangocart::getOrder($order_id));
  }
  
  public static function getOrderClass() {
    return sfConfig::get('app_dzangocart_order_class', 'DzangocartOrder');
  }

  public function isTest() {
    return $this->data['test'];
  }

  public function isCancelled() {
    return $this->data['cancelled'];
  }
  
  public function isCancellation() {
  	return $this->data['cancellation'];
  }
  
  public function isCredit() {
  	return $this->data['credit'];
  }
  
  public function getCssClass() {
    $css = array(static::CSS_CLASS);
    if ($this->isCancelled()) {
      $css[] = static::CSS_CLASS_CANCELLED;
    }
	
	if ($this->isCredit()) {
		$css[] = static::CSS_CLASS_CREDIT;
	}
	
	if ($this->isCancellation()) {
		$css[] = static::CSS_CLASS_CANCELLATION;
	}
	
    $css[] = $this->paid ? static::CSS_CLASS_PAID : static::CSS_CLASS_UNPAID;

    return implode(' ', $css);
  }

  public function getDateFormat() { return 'd/m/Y H:i'; }

  public function getDate() {
    return new DateTime($this->data['date']);
  }
  
  public function getDatePaid($format = 'd/m/Y') {
  	return new DateTime($this->data['paid_at']);
  }

  public function getCustomerName() {
    return sprintf('%s, %s', $this->data['customer']['surname'], $this->data['customer']['given_names']);
  }

  public function getCustomerCode() {
    return $this->data['customer']['code'];
  }
  
  public function getCustomerReferralCode() {
    return $this->data['customer']['referral_code'];  
  }
  
  public function getOrderId() {
    return $this->data['id'];
  }
  
  public function getCustomerEmail() {
    return $this->data['customer']['email'];
  }
  
  public function getCustomer() {
  	if (!$this->customer) {
  		$cls = $this->getCustomerClass();
  		$this->customer = new $cls($this->data['customer']);
  	}
	return $this->customer;
  }
  
  public function getCurrency() {
  	return $this->data['currency'];
  }
  
  public function getAmount() {
  	return $this->data['amount'];
  }
  
  public function getAmountPaid() {
  	return $this->data['amount_paid'];
  }
  
  public function getItems() {
    if (!$this->items) {
    	$this->items = array();
		foreach ($this->data['items'] as $item_data) {
			$cls = $this->getItemClass($item_data['category']);
			// When returning an order, Dzangocart only supplies the customer data at order level, not item level.
			$item_data['customer'] = $this->customer;
			$item = new $cls($item_data);
			$this->items[] = $item;
		}
	}
	
	return $this->items;
  }
  
  public function getActionsPartial() {
    return 'dzangocart/order_actions';
  }

  public function getDetailsPartial() {
    return 'dzangocart/order_details';
  }

  public function getBatchActionPartial() {
    return 'dzangocart/order_batch_action';
  }
  
  public function process($confirm = false) {
    if ($confirm) { $this->confirm(); }
    $this->generateInvoice();  
    $this->doProcessOrder();
    $this->processItems();
  }
  
  protected function doProcessOrder() {}
  
  protected function confirm() {}
  
  protected function generateInvoice() {}
  
  protected function processItems() {
    $items = $this->getItems();
    foreach ($items as $item) {
      $item->process(); 
	}
  }
  
  protected function getCustomerClass() {
  	return 'DzangocartCustomer';
  }

  protected function getItemClass($category) {
  	return 'DzangocartPurchase';
  }
}