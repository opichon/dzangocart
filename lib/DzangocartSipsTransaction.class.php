<?php

class DzangocartSipsTransaction extends DzangocartTransaction {
  
  public function getDate() {
    return $this->data['payment_time'] ?
             date_create_from_format('YmdHis', $this->data['payment_date'] . $this->data['payment_time']) :
             date_create_from_format('Ymd', $this->data['payment_date']);
  }

  public function getAmount() {
    return $this->data['decimals']
              ? (float) ($this->data['amount'] / pow(10, $this->data['decimals']))
              : $this->data['amount'];
  }
}