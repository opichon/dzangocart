<?php
namespace Dzangocart;

class POTransaction extends Transaction
{
 	public function getDate()
 	{
    	return new DateTime($this->data['date']);
    }
}
