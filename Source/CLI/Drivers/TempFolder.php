<?php
namespace SeTaco\CLI\Drivers;


use SeTaco\Exceptions\CLIException;
use Structura\Random;

class TempFolder
{
	private $path;
	
	
	public function __construct(string $path)
	{
		$this->path = $path;
	}
	
	private function recursiveCleanDirectory(string $dir, bool $cleanCurrent = true): void 
	{
  		$files = array_diff(scandir($dir), ['.', '..']);
  		
		foreach ($files as $file) 
		{
			$fullPath = "$dir/$file";
			
			if (is_dir($fullPath))
			{
				$this->recursiveCleanDirectory($fullPath);
			}
			else
			{
				@unlink($fullPath);
				CLIException::throwIfLastErrorNotEmpty("Failed to delete file '$fullPath'");
			}
		}
		
		if ($cleanCurrent)
		{
			@rmdir($dir);
			CLIException::throwIfLastErrorNotEmpty("Failed to delete '$dir'");
		}
	}
	
	
	public function create(): void
	{
		if (is_dir($this->path))
			return;
		
		@mkdir($this->path);
		CLIException::throwIfLastErrorNotEmpty("Failed to create temporary directory '{$this->path}'");
	}
	
	public function cleanup(): void
	{
		if (!is_dir($this->path))
			return;
		
		$this->recursiveCleanDirectory($this->path, false);
	}
	
	public function getTempFile(): string
	{
		$file = null;
		
		while (!$file || file_exists($file) || is_dir($file))
		{
			$file = $this->path . '/' . Random::string(10) . '.file';
		}
		
		return $file;
	}
}