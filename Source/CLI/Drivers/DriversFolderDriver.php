<?php
namespace SeTaco\CLI\Drivers;


use SeTaco\Exceptions\CLIException;


class DriversFolderDriver
{
	private const CHROME_DRIVE_IDENTIFIER = 'chromedrive';
	
	private $homeDir = null;
	private $dir = null;
	
	
	private function getFullName(string $version): string
	{
		return "$version." . self::CHROME_DRIVE_IDENTIFIER;
	}
	
	private function getPathToVersion(string $version): string
	{
		return $this->getPath($this->getFullName($version));
	}
	
	
	private function getPath(?string $to = null): string
	{
		$path = $this->homeDir . DIRECTORY_SEPARATOR . $this->dir;
		
		if ($to)
		{
			$path .= DIRECTORY_SEPARATOR . $to;
		}
		
		return $path;
	}
	
	
	public function __construct(string $homeDir, string $dir)
	{
		$this->homeDir = $homeDir;
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
	
	public function clean(?string $match = null): void
	{
		
	}
	
	public function getForVersion(string $major): string
	{
		
	}
	
	public function getForMajorVersion(string $major): string
	{
		
	}
	
	public function store(string $file, string $version, bool $cleanup = true): void
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
		
		
	}
	
	/**
	 * @return string[]
	 */
	public function list(): array
	{
		$path = $this->getPath();
		$pattern = $path . '/*.' . self::CHROME_DRIVE_IDENTIFIER;
		
		error_clear_last();
		$data = glob($pattern, GLOB_ERR);
		
		CLIException::throwIfLastErrorNotEmpty("There was an error while trying to execute glob('$pattern')");
		
		return [];
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