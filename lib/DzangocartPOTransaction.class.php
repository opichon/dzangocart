<?php
namespace Dzangocart;

class DzangocartPOTransaction.class extends Transaction
{
  public function getDate()
  {
    return new DateTime($this->data['date']);
  }
}
