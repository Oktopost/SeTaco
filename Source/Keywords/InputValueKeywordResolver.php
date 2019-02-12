<?php
namespace SeTaco\Keywords;


class InputValueKeywordResolver extends AbstractKeywordResolver
{
	protected function getTemplate(string $keyword): string
	{
		return 'input[value="' . $keyword . '"]';
	}
}