<?php
namespace Dzangocart;

class Transaction extends Object
{
	const CSS_CLASS = 'transaction';

	public function isTest()
	{
		return $this->data['test'];
	}

	public function getCssClass()
	{
		$css = array(static::CSS_CLASS);

		return implode(' ', $css);
	}

	public function getDateFormat()
	{
		return 'd/m/Y H:i';
	}
}
