<?php
class DzangocartAddress extends DzangocartObject {
	
	public function getLine1() {
		return $this->data['line1'];
	}
	
	public function getLine2() {
		return $this->data['line2'];
	}

	public function getZip() {
		return $this->data['zip'];
	}
	
	public function getCity() {
		return $this->data['city'];
	}
	
	public function getCountry() {
		return $this->data['country'];
	}
	
	public function getLines() {
		$lines = array($this->getLine1());
		if ($line2 = $this->getLine2()) {
			$lines[] = $line2;
		}
		$lines[] = $this->getCityLine();
		$lines[] = $this->getCountry();
		
		return $lines;
	}
	
	public function format($separator = "\n") {
		return implode($separator, $this->getLines());
	}
	
	protected function getCityLine() {
		return implode(' ', array($this->getZip(), $this->getCity()));
	}
}
