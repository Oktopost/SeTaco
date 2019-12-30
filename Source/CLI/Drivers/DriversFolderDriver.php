<?php
namespace SeTaco\CLI\Drivers;


use Structura\Arrays;
use Structura\Strings;

use SeTaco\Exceptions\CLIException;


class DriversFolderDriver
{
	private const CHROME_DRIVE_IDENTIFIER = '.chromedrive';
	
	private $dir = null;
	
	
	private function getFullName(string $version): string
	{
		return Strings::endWith($version, self::CHROME_DRIVE_IDENTIFIER);
	}
	
	private function getPathToVersion(string $version): string
	{
		return $this->getPath($this->getFullName($version));
	}
	
	private function getPath(?string $to = null): string
	{
		$path = $this->dir;
		
		if ($to)
		{
			$path .= DIRECTORY_SEPARATOR . $to;
		}
		
		return $path;
	}
	
	
	public function __construct(string $dir)
	{
		$this->dir = $dir;
	}
	
	
	public function has(string $version): bool
	{
		$path = $this->getPathToVersion($version);
		
		error_clear_last();
		$exists = @file_exists($path);
		
		CLIException::throwIfLastErrorNotEmpty("Error when running `file_exists('$path')`");
		
		return $exists;
	}
	
	public function delete(string $version): void
	{
		if (!$this->has($version))
			return;
		
		$path = $this->getPathToVersion($version);
		
		error_clear_last();
		@unlink($path);
		
		CLIException::throwIfLastErrorNotEmpty("Error when running `unlink('$path')`");
	}
	
	public function clean(string $match = '*'): void
	{
		$all = $this->list($match);
		
		foreach ($all as $element)
		{
			$this->delete($element);
		}
	}
	
	public function getForVersion(string $version): string
	{
		$major = explode('.', $version, 2)[0];
		return $this->getForMajorVersion($major);
	}
	
	public function getForMajorVersion(string $major): ?string
	{
		$all = $this->list("$major.*");
		
		if (!$all)
			return null;
		
		return $this->getPath(Arrays::last($all));
	}
	
	public function store(string $file, string $version, bool $cleanup = true): string
	{
		$path = $this->getPathToVersion($version);
		$this->delete($version);
		
		error_clear_last();
		@copy($file, $path);
		
		CLIException::throwIfLastErrorNotEmpty("copy command failed for '$file' -> '$path'");
		
		if ($cleanup)
		{
			@unlink($file);
			CLIException::throwIfLastErrorNotEmpty("Failed to unlink source file $file");
		}
		
		return $path;
	}
	
	/**
	 * @return string[]
	 */
	public function list(string $filter = '*'): array
	{
		$path = $this->getPath();
		$pattern = $path . "/$filter" . self::CHROME_DRIVE_IDENTIFIER;
		
		$data = glob($pattern, GLOB_ERR);
		
		if ($data === false)
		{
			throw new CLIException("There was an error while trying to execute glob('$pattern')");
		}
		
		
		foreach ($data as &$item)
		{
			$item = Strings::trimStart($item, $path . '/');
		}
		
		sort($data);
		
		return $data;
	}
	
	public function init(): void
	{
		$path = $this->getPath();
		
		error_clear_last();
		$exists = @is_dir($path);
		CLIException::throwIfLastErrorNotEmpty("Error in `mkdir` when trying to create $path");
		
		if (!$exists)
		{
			@mkdir($path, 0777, true);
			CLIException::throwIfLastErrorNotEmpty("Error in `mkdir` when trying to create $path");
		}
	}
}