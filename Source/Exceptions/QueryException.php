<?php
namespace SeTaco\Exceptions;


use SeTaco\Query\ISelector;
use Structura\Arrays;


class QueryException extends SeTacoException
{
	/** @var ISelector */
	private $selectors;
	
	
	private function getSelectorAsString(ISelector $selector, string $tabulation = '', string $newLine = PHP_EOL): string
	{
		$original	= $selector->originalQuery();
		$generated	= $selector->query();
		$type		= $selector->type();
		$resolver	= $selector->resolver();
		
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
	
	private function getSelectorsAsString(string $tabulation, string $newLine = PHP_EOL): string
	{
		if (count($this->selectors) == 1)
		{
			return $this->getSelectorAsString($this->selectors[0]);
		}
		else
		{
			$message = '';
			$num = 1;
			
			foreach ($this->selectors as $selector)
			{
				$message .= "Select $num: $newLine";
				$message .= $this->getSelectorAsString($selector, "{$tabulation}{$tabulation}", $newLine);
				$message .= "{$tabulation}{$tabulation}{$newLine}";
				
				$num++;
			}
			
			return $message;
		}
	}
	
	public function generateMessage(string $message)
	{
		$message = $message . PHP_EOL;
		$message .= $this->getSelectorsAsString("\t");
		$message .= $this->getTraceAsString();
		
		return $message;
	}
	
	
	public function __construct($selector, string $message)
	{
		$this->selectors = array_values(Arrays::toArray($selector));
		parent::__construct($this->generateMessage($message));
	}
}