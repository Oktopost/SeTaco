<?php
namespace SeTaco\Keywords;


use Structura\Strings;


class ConstKeywordResolver extends AbstractKeywordResolver
{
	protected function getTemplate(string $keyword): string
	{
		return $keyword;
	}
	
	
	public function resolve(string $keyword): ?string
	{
		$map = $this->getMap();
		
		if ($this->canResolve($keyword))
			return $this->getTemplate($map[$keyword]);
		
		$prefix = $this->getPrefix();
		
		if ($prefix && Strings::isStartsWith($keyword, $prefix))
		{
			if (Strings::isStartsWith($keyword, $prefix . '.'))
				$prefix .= '.';
			
			$keyword = Strings::replace($keyword, $prefix, '');
			
			if ($this->canResolve($keyword))
				return $this->getTemplate($map[$keyword]);
		}
		
		return null;
	}
}