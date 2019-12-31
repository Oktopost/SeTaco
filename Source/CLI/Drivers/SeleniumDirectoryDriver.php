<?php
namespace SeTaco\CLI\Drivers;


use FileSystem\Path;
use Structura\Strings;
use Structura\Version;


class SeleniumDirectoryDriver
{
	/** @var Path */
	private $path;
	
	
	private function getDriverName(Version $for): string
	{
		return "selenium.$for";
	}
	
	private function getVersionFromName(Path $path): ?Version
	{
		if (!$path->isFile())
			return null;
		
		$name = $path->name();
		
		if (!Strings::isStartsWith($name, 'selenium.') ||
			!Strings::isEndsWith($name, '.jar') ||
			substr_count($name, '.') != 5)
		{
			return null;
		}
		
		return new Version(
			Strings::trimStart(
				Strings::trimEnd($name, '.jar'), 
			'selenium.'));
	}
	
	
	public function __construct($path)
	{
		$this->path = Path::getPathObject($path);
	}
	
	
	public function getLatest(): ?Path
	{
		$greatestPath = null;
		$greatestVersion = null;
		
		foreach ($this->path->scandir() as $item)
		{
			$version = $this->getVersionFromName($item);
			
			if (!$version)
			{
				continue;
			}
			
			if (!$greatestVersion || $version->isHigher($greatestVersion))
			{
				$greatestVersion = $version;
				$greatestPath = $item;
			}
		}
		
		return $greatestPath;
	}
	
	public function delete($version): void
	{
		$version = new Version($version);
		$name = $this->getDriverName($version);
		$path = $this->path->append($name);
		
		if ($path->exists())
		{
			$path->unlink();
		}
	}
	
	public function getLatestVersion(): ?Version
	{
		$path = $this->getLatest();
		
		return ($path ? $this->getVersionFromName($path) : null);
	}
	
	public function cleanup(int $toKeep): void
	{
		$versions = [];
		
		foreach ($this->path->scandir() as $item)
		{
			$version = $this->getVersionFromName($item);
			
			if (!$version)
			{
				$item->delete();
			}
			else
			{
				$versions[] = $version;
			}
		}
		
		usort($versions, function (Version $a, Version $b)
		{
			return $a->compare($b);
		});
		
		for ($i = count($versions) - 1; $i >= 0; $i--)
		{
			if ($toKeep-- > 0)
				continue;
			
			$this->delete($versions[$i]);
		}
	}
	
	public function store($file, $version, bool $cleanup): void
	{
		$file = Path::getPathObject($file);
		$version = new Version($version);
		$name = $this->getDriverName($version);
		$path = $this->path->append($name);
		
		$file->copyFile($path);
		
		if ($cleanup)
		{
			$file->unlink();
		}
	}
}