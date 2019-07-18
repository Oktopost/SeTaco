<?php
namespace SeTaco\Exceptions;


use SeTaco\Query\ISelector;


class QueryException extends SeTacoException
{
	/** @var ISelector */
	private $selector;
	
	
	public function __construct(ISelector $selector, string $message)
	{
		parent::__construct($message);
		$this->selector = $selector;
	}
	
	
	public function getSelectorAsString(string $tabulation = '', string $newLine = PHP_EOL): string
	{
		$original	= $this->selector->originalQuery();
		$generated	= $this->selector->query();
		$type		= $this->selector->type();
		$resolver	= $this->selector->resolver();
		
		$message = '';
		$message .= $tabulation . "String: $original"	. $newLine;
		$message .= $tabulation . "Query:  $generated"	. $newLine;
		$message .= $tabulation . "Type:   $type"		. $newLine;
		
		if (!$resolver)
			return $message;
		
		$resolver = get_class($resolver);
		$message .= $tabulation . "Using:  $resolver"	. $newLine;
		
		return $message;
	}
	
	public function __toString()
	{
		$message = $this->getMessage() . PHP_EOL;
		$message .= $this->getSelectorAsString("\t");
		$message .= $this->getTraceAsString();
		
		return $message;
	}
}