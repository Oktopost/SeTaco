<?php
namespace SeTaco\Keywords;


class ContainsKeywordResolver extends AbstractKeywordResolver
{
	protected function getTemplate(string $keyword): string
	{
		return '//*[contains(text(), "' . $keyword . '")]';
	}
}