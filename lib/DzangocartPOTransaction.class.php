<?php

class DzangocartPOTransaction extends DzangocartTransaction {
  
  public function getDate() {
    return new DateTime($this->data['date']);
  }
}