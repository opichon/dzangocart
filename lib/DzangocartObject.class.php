<?php
class DzangocartObject {
  
  protected $data;

  public function __construct(array $data) {
    $this->data = $data;  
  }
  
  public function __get($name) {
    if (array_key_exists($name, $this->data)) {
      return $this->data[$name];
    }
  }

  public function getData() { return $this->data; }
}