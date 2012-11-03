<?php
class DzangocartPurchase extends DzangocartObject {
  
  const CSS_CLASS = 'purchase';
  const CSS_CLASS_CANCELLED = 'cancelled';
  const CSS_CLASS_UNPAID = 'unpaid';
  const CSS_CLASS_PAID = 'paid';
  const CSS_CLASS_CREDIT = 'credit';
  
  protected $customer;
  protected $subitems;
  
  public function getId() {
  	return $this->data['id'];
  }
  
  public function getOrderId() {
  	return $this->data['order_id'];
  }
  
  public function getName() {
  	return $this->data['name'];
  }
  
  public function getDate() {
    return new DateTime($this->data['date']);
  }
  
  public function getCategory() {
  	return $this->data['category'];
  }
  
  public function getQuantity() {
  	return $this->data['quantity'];
  }
  
  public function getPriceIncl() {
  	return $this->data['price_incl'];
  }
  
  public function getPriceExcl() {
  	return $this->data['price_excl']; 
  }
  
  public function isTaxIncl() {
  	return $this->data['tax_incl'];
  }
  
  public function getTaxRate() {
  	return $this->data['tax_rate'];
  }
  
  public function getAmountIncl() {
  	return $this->data['amount_incl'];
  }
  
  public function getAmountExcl() {
  	return $this->data['amount_excl'];
  }
  
  public function getAmountTax() {
  	return $this->data['amount_tax'];
  }
  
  public function getCurrency() {
  	return $this->data['currency'];
  }
  
  public function isPaid() {
    return $this->data['paid'];
  }
  
  public function isTest() {
    return $this->data['test'];
  }
  
  public function isCredit() {
    return $this->data['credit'];
  }
  
  public function isCancelled() {
    return $this->data['cancelled'];
  }
  
  public function getCancelledAt() {
  	return $this->data['cancelled_at'];
  }
  
  public function getCancellationItemId() {
  	return $this->data['cancellation_item_id'];
  }
  
  public function isCancellation() {
  	return $this->data['cancelled_item_id'];
  }
  
  public function getCancelledItemId() {
  	return $this->data['cancelled_item_id'];
  }
  
  public function getCustomerName() {
    return sprintf('%s, %s', $this->data['customer']['surname'], $this->data['customer']['given_names']);  
  }
  
  public function getAffiliateId() {
  	return $this->data['affiliate_id'];
  }

  public function getCode() {
  	return $this->data['code'];
  }
  
  public function getCodeGeneric() {
  	return $this->data['code_generic'];
  }

  public function getOrderReference() {
  	return $this->data['order_reference'];
  }

  public function getCustomer() {
  	if (!$this->customer) {
  		$cls = $this->getCustomerClass();
  		$this->customer = new $cls($this->data['customer']);
  	}
	return $this->customer;
  }
  
  public function getSubItems() {
  	if (!$this->subitems) {
      $this->subitems = array();
		if (array_key_exists('sub-items', $this->data)) {
          foreach ($this->data['sub-items'] as $sub) {
            $cls = $this->getSubItemClass($sub['category']);
            $this->subitems[] = new $cls($sub);
          }
      }
  	}
	
	return $this->subitems;
  }
  public function getCssClass() {
    $css = array(static::CSS_CLASS);
    
    if ($this->isCredit()) {
      $css[] = static::CSS_CLASS_CREDIT;
    }
    
    $css[] = $this->isPaid() ? static::CSS_CLASS_PAID : static::CSS_CLASS_UNPAID;
    
    if ($this->isCancelled()) {
      $css[] = static::CSS_CLASS_CANCELLED;
    }
    
    return implode(' ', $css);
  }

  public function getCustomerPartial() {
    return 'dzangocart/customer';
  }
  
  public function getProductPartial() {
    return 'dzangocart/product';
  }

  public function getActionsPartial() {
    return 'dzangocart/purchase_actions';
  }
  
  public function getBatchActionPartial() {
    return 'dzangocart/purchase_batch_action';
  }
  
  public function getDateFormat() { return 'd/m/Y H:i'; }

  public function process() {}
  
  protected function getSubItemClass($category) {
  	return 'DzangocartPurchase';
  }
}