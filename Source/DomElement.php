<?php
namespace SeTaco;


use Facebook\WebDriver\Remote\RemoteWebElement;
use SeTaco\Exceptions\Element\MissingAttributeException;


class DomElement implements IDomElement
{
	/** @var RemoteWebElement */
	private $element;
	
	
	public function __construct(RemoteWebElement $element)
	{
		$this->element = $element;
	}
	
	
	public function getRemoteWebElement(): RemoteWebElement
	{
		return $this->element;
	}
	
	
	public function click(): void
	{
		$this->element->click();
	}
	
	public function input(string $input): void
	{
		$this->element->sendKeys($input);
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
}