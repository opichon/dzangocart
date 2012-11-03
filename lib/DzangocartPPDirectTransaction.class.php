<?php

class DzangocartPPDirectTransaction extends DzangocartTransaction {
  
  public function getDate() {
    return date_create_from_format('Y-m-d\TH:i:s\Z', $this->data['timestamp']);
  }
}