<?php
namespace SeTaco\Keywords;


class PlaceholderKeywordResolver extends AbstractKeywordResolver
{
	protected function getTemplate(string $keyword): string
	{
		return '[placeholder="' . $keyword . '"]';
	}
}