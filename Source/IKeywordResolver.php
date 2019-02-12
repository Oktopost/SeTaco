<?php
namespace SeTaco;


interface IKeywordResolver
{
	public function resolve(string $keyword): ?string;
}