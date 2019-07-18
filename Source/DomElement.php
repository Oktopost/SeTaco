<?php
namespace SeTaco;


use SeTaco\Exceptions\Element\MissingAttributeException;
use Facebook\WebDriver\Remote\RemoteWebElement;


class DomElement implements IDomElement
{
	/** @var RemoteWebElement */
	private $element;
	
	/** @var BrowserSetup */
	private $setup;
	
	
	public function __construct(RemoteWebElement $element, BrowserSetup $setup)
	{
		$this->setup = $setup;
		$this->element = $element;
	}
	
	
	public function getRemoteWebElement(): RemoteWebElement
	{
		return $this->element;
	}
	
	
	public function click(bool $hover = false): IDomElement
	{
		if ($hover)
			$this->hover();
		
		$this->element->click();
		
		return $this;
	}
	
	public function hover(): IDomElement
	{
		$this->setup->RemoteWebDriver
			->action()
			->moveToElement($this->element)
			->perform();
		
		return $this;
	}
	
	public function input(string $input): IDomElement
	{
		$this->element->clear();
		$this->element->sendKeys($input);
		return $this;
	}
	
	public function appendInput(string $input): IDomElement
	{
		$this->element->sendKeys($input);
		return $this;
	}
	
	public function clear(): IDomElement
	{
		$this->element->clear();
		return $this;
	}
	
	public function getName(bool $allowMissing = true): ?string
	{
		return $this->getAttribute('name', $allowMissing);
	}
	
	public function getAttribute(string $name, bool $allowMissing = true): ?string
	{
		$value = $this->element->getAttribute($name);
		
		if (is_null($value) && !$allowMissing)
			throw new MissingAttributeException($name);
		
		return $value;
	}
	
	public function query(): IQuery
	{
		return new Query($this->setup, $this->element);
	}
}