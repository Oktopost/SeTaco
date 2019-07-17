<?php
namespace SeTaco\Query;


use Traitor\TEnum;


class SelectorType
{
	use TEnum;
	
	
	public const ID			= 'id';
	public const NAME		= 'name';
	public const CSS		= 'css';
	public const XPATH		= 'xpath';
	public const TAG_NAME	= 'name';
}