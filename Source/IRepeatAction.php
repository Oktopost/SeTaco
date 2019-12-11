<?php
namespace SeTaco;


interface IRepeatAction extends IQueryAction
{
	public function addCallback(callable $callback): IRepeatAction;
	public function execute(callable $callback): void;
}