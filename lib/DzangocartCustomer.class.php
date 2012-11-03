<?php 
class DzangocartCustomer extends DzangocartObject {

  protected $billing_address;
	protected $shipping_address;
		
	public function isCorporate() {
		return $this->data['corporate'];
	}
	
	public function getSalutation() {
		return $this->data['salutation'];
	}
	
	public function getName() {
		return implode(' ' , array($this->getSalutation(), $this->getGivenNames(), $this->getSurname()));
	}
	
	public function getCompany() {
		return $this->dat['company'];
	}
	
	public function getSurname() {
		return $this->data['surname'];
	}
	
	public function getGivenNames() {
		return $this->data['given_names'];
	}
	
	public function getCode() {
		return $this->data['code'];
	}
	
	public function getEmail() {
		return $this->data['email'];
	}
	
	public function getTelephone() {
		return $this->data['telephone'];
	}
	
	public function getFax() {
		return $this->data['fax'];
	}
	
	public function getMobile() {
		return $this->data['mobile'];
	}
	
	public function shipToBillingAddress() {
		return $this->data['ship_to_billing_address'];
	}
	
	public function getBillingAddress() {
		if (!$this->billing_address) {
			$cls = $this->getAddressClass();
			$this->billing_address = new $cls($this->data['billing_address']);
		}
		return $this->billing_address;
	}
	
	public function getShippingAddress() {
		if ($this->shipToBillingAddress()) {
			return $this->getBillingAddress();
		}
		
		if (!$this->shipping_address) {
			$cls = $this->getAddressClass();
			$this->shipping_address = new $cls($this->data['shipping_address']);
		}
		
		return $this->shipping_address;
	}
	
	protected function getAddressClass() {
		return 'DzangocartAddress';
	}
}
