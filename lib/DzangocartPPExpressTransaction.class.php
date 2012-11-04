<?php
namespace Dzangocart;

class DzangocartPPExpressTransaction.class extends Transaction
{
  public function getDate()
  {
    return date_create_from_format('Y-m-d\TH:i:s\Z', $this->data['order_time']);
  }
}
