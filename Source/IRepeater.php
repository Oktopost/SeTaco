<?php
namespace SeTaco;


interface IRepeater
{
	public function while(callable $callback, float $delay = 0.1, ?float $timeout = null): IRepeatAction;
	public function whileNull(callable $callback, float $delay = 0.1, ?float $timeout = null): IRepeatAction;
	public function whileNotNull(callable $callback, float $delay = 0.1, ?float $timeout = null): IRepeatAction;
	public function whileEquals(callable $callback, $value, float $delay = 0.1, ?float $timeout = null): IRepeatAction;
	public function whileNotEquals(callable $callback, $value, float $delay = 0.1, ?float $timeout = null): IRepeatAction;
	public function whileSame(callable $callback, $value, float $delay = 0.1, ?float $timeout = null): IRepeatAction;
	public function whileNotSame(callable $callback, $value, float $delay = 0.1, ?float $timeout = null): IRepeatAction;
	public function whileThrowing(callable $callback, float $delay = 0.1, ?float $timeout = null): IRepeatAction;
	
	public function whileElementExists(string $selector, bool $isCaseSensitive = false, float $delay = 0.1, ?float $timeout = null): IRepeatAction;
	public function whileElementMissing(string $selector, bool $isCaseSensitive = false, float $delay = 0.1, ?float $timeout = null): IRepeatAction;
	public function whileAnyElementExists(array $selector, bool $isCaseSensitive = false, float $delay = 0.1, ?float $timeout = null): IRepeatAction;
	public function whileAnyElementMissing(array $selector, bool $isCaseSensitive = false, float $delay = 0.1, ?float $timeout = null): IRepeatAction;
	public function whileAllElementsExist(array $selector, bool $isCaseSensitive = false, float $delay = 0.1, ?float $timeout = null): IRepeatAction;
	public function whileAllElementsMissing(array $selector, bool $isCaseSensitive = false, float $delay = 0.1, ?float $timeout = null): IRepeatAction;
}