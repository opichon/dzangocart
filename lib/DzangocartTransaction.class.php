<?php

class DzangocartTransaction extends DzangocartObject {
  
  const CSS_CLASS = 'transaction';
  
  public function isTest() {
    return $this->data['test'];
  }
  
  public function getCssClass() {
    $css = array(static::CSS_CLASS);
    return implode(' ', $css);
  }

  public function getActionsPartial() {
    return 'dzangocart/transaction_actions';
  }

  public function getBatchActionPartial() {
    return 'dzangocart/transaction_batch_action';
  }
  
  public function getDateFormat() { return 'd/m/Y H:i'; }
}