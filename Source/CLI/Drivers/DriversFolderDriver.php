<?php
namespace SeTaco\CLI\Drivers;


use FileSystem\Path;

use Structura\Strings;
use Structura\Version;

use SeTaco\Exceptions\CLIException;


class DriversFolderDriver
{
	private $type; 
	
	/** @var Path */
	private $path;
	
	
	private function getDriverName(Version $for): string
	{
		return "{$this->type}.$for.driver";
	}
	
	private function getDriverPath(Version $for): Path
	{
		return $this->path->append($this->getDriverName($for)); 
	}
	
	private function getVersionFromName(Path $path): ?Version
	{
		if (!$path->isFile())
			return null;
		
		$fileName = $path->name();
		
		if (!Strings::isEndsWith($fileName, '.driver') || 
			!Strings::isStartsWith($fileName, $this->type . '.'))
		{
			return null;
		}
		
		$version = Strings::trimStart($fileName, $this->type . '.');
		$version = Strings::trimEnd($version, '.driver');
		
		if (substr_count($version, '.') != 3)
		{
			return null;
		}
		
		return new Version($version);
	}
	
	
	public function __construct($path, string $type)
	{
		$this->path = Path::getPathObject($path);
		$this->type = $type;
	}
	
	
	/**
	 * @param Version|string 
	 * @return bool
	 */
	public function has($version): bool
	{
		return $this
			->getDriverPath(new Version($version))
			->exists();
	}
	
	/**
	 * @param Version|string 
	 * @return ?Path
	 */
	public function get($version): ?Path
	{
		$path = $this->getDriverPath(new Version($version));
		return $path->exists() ? $path : null;
	}
	
	/**
	 * @param Version|string $version
	 */
	public function delete($version): void
	{
		$this
			->getDriverPath(new Version($version))
			->unlink();
	}
	
	/**
	 * @return Version[]
	 */
	public function listVersions(): array
	{
		$items = $this->path->scandir();
		$versions = [];
		
		foreach ($items as $item)
		{
			$version = $this->getVersionFromName($item);
			
			if ($version)
			{
				$versions[] = $version;
			}
		}
		
		usort($versions, function (Version $a, Version $b)
		{
			if ($a->isSame($b)) return 0;
			else if ($a->isLower($b)) return -1;
			else return 1;
		});
		
		return $versions;
	}
	
	/**
	 * At least major version must match, and only same or lower version will be excepted.
	 * 
	 * @param string|Version $forVersion
	 * @return ?Version
	 */
	public function getBestMatch($forVersion): ?Version
	{
		$version = new Version($forVersion);
		$matching = null;
	
		foreach ($this->listVersions() as $existingVersion)
		{
			if ($existingVersion->isHigher($version))
			{
				break;
			}
			else if ($existingVersion->getMajor() == $version->getMajor())
			{
				$matching = $existingVersion;
			}
		}
		
		return $matching;
	}
	
	public function cleanup($currentVersion, int $driversToKeep = 5): void
	{
		$version = new Version($currentVersion);
		$driversKept = 0;
		
		/** @var Version[] $newestFirstVersions */
		$newestFirstVersions = array_reverse($this->listVersions());
	
		foreach ($newestFirstVersions as $existingVersion)
		{
			if ($existingVersion->isHigher($version))
			{
				$this->delete($existingVersion);
			}
			else if ($driversKept >= $driversToKeep)
			{
				$this->delete($existingVersion);
			}
			else 
			{
				$driversKept++;	
			}
		}
	}
	
	/**
	 * @param string|Path $file
	 * @param string|Version $version
	 * @param bool $cleanup
	 */
	public function store($file, $version, bool $cleanup = true): void
	{
		$file = Path::getPathObject($file);
		$version = new Version($version);
		
		$path = $this->getDriverPath($version);
		
		$file->copyFile($path);
		
		if ($cleanup)
		{
			$file->unlink();
		}
	}
}