<?php
namespace SeTaco\Source;


use PHPUnit\Framework\TestCase;
use SeTaco\BrowserSession;
use SeTaco\Exceptions\Query\ElementNotFoundException;
use SeTaco\IBrowser;
use SeTaco\TacoConfig;
use Structura\Strings;


class QueryTest extends TestCase
{
	private const PATH = __DIR__ . '/../html/Query';
	
	
	/** @var BrowserSession */
	private static $session = null;
	
	
	private function getBrowser(string $path): IBrowser
	{
		if (self::$session)
		{
			self::$session->close();
		}
		
		self::$session = new BrowserSession(TacoConfig::parse([]));
		
		$browser = self::$session->open('default');
		
		$path = Strings::startWith($path, '/');
		$path = Strings::endWith($path, '.html');
		$path = realpath(self::PATH . $path);
		
		$browser->goto("file://$path");
		
		return $browser;
	}
	
	private function getBrowserForContent(string $content): IBrowser
	{
		$tmpFile = self::PATH . '/tmp.html';
		$templateFile = self::PATH . '/_template_.html';
		
		if (file_exists($tmpFile))
			unlink($tmpFile);
		
		$template = file_get_contents($templateFile);
		$data = str_replace('{data}', $content, $template);
		
		file_put_contents($tmpFile, $data);
		
		return $this->getBrowser('tmp.html');
	}
	
	
	public function test_exists_ElementNotFound_ReturnFalse()
	{
		$browser = $this->getBrowser('blank');
		
		
		self::assertFalse($browser->exists('.not_found', 0.0, false));
	}
	
	public function test_exists_ElementFound_ReturnTrue()
	{
		$browser = $this->getBrowserForContent('<div>Hello</div>');
		
		
		self::assertTrue($browser->exists('txt:hello', 0.0, false));
	}
	
	public function test_exists_CaseSensitiveFlagUsed_ReturnTrue()
	{
		$browser = $this->getBrowserForContent('<div>Hello</div>');
		
		
		self::assertFalse($browser->exists('txt:hello', 0.0, true));
		self::assertTrue($browser->exists('txt:Hello', 0.0, true));
	}
	
	public function test_exists_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('blank');
		
		
		$startTime = microtime(true);
		$browser->exists('.not_found', 0.0, false);
		$emptyRunTime = microtime(true) - $startTime;
		
		
		$startTime = microtime(true);
		$browser->exists('.not_found', 0.12, false);
		$runTime = microtime(true) - $startTime;
		
		
		self::assertTrue($runTime > $emptyRunTime);
		self::assertTrue($runTime > 0.12);
	}
	
	
	public function test_existsAny_NonFound_ReturnFalse()
	{
		$browser = $this->getBrowser('blank');
		
		
		self::assertFalse($browser->existsAny(['.a', '.b'], 0.0, false));
	}
	
	public function test_existsAny_ElementsFound_ReturnTrue()
	{
		$browser = $this->getBrowserForContent('<div>hello</div>');
		
		self::assertTrue($browser->existsAny(['.a', 'txt:hello', '.b'], 0.0, false));
	}
	
	public function test_existsAny_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowserForContent('<div>HeLLo</div>');
		
		self::assertFalse($browser->existsAny(['.a', 'txt:Hello', '.b'], 0.0, true));
		self::assertTrue($browser->existsAny(['.a', 'txt:HeLLo', '.b'], 0.0, true));
	}
	
	public function test_existsAny_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('blank');
		
		
		$startTime = microtime(true);
		$browser->existsAny(['.a'], 0.0, false);
		$emptyRunTime = microtime(true) - $startTime;
		
		
		$startTime = microtime(true);
		$browser->existsAny(['.a', '.b'], 0.12, false);
		$runTime = microtime(true) - $startTime;
		
		
		self::assertTrue($runTime > $emptyRunTime);
		self::assertTrue($runTime > 0.12);
	}
	
	
	public function test_existsAll_NonFound_ReturnFalse()
	{
		$browser = $this->getBrowserForContent('<div>Hello</div>');
		
		
		self::assertFalse($browser->existsAll(['.a', '.b'], 0.0, false));
		self::assertFalse($browser->existsAll(['.a', 'txt:hello'], 0.0, false));
	}
	
	public function test_existsAll_ElementsFound_ReturnTrue()
	{
		$browser = $this->getBrowserForContent('<div>hello</div><div>World</div><div>hello</div>');
		
		self::assertTrue($browser->existsAll(['txt:world', 'txt:hello'], 0.0, false));
	}
	
	public function test_existsAll_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowserForContent('<div>HeLLo</div>');
		
		self::assertFalse($browser->existsAll(['txt:Hello'], 0.0, true));
		self::assertTrue($browser->existsAll(['txt:HeLLo'], 0.0, true));
	}
	
	public function test_existsAll_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('blank');
		
		
		$startTime = microtime(true);
		$browser->existsAll(['.a'], 0.0, false);
		$emptyRunTime = microtime(true) - $startTime;
		
		
		$startTime = microtime(true);
		$browser->existsAll(['.a', '.b'], 0.12, false);
		$runTime = microtime(true) - $startTime;
		
		
		self::assertTrue($runTime > $emptyRunTime);
		self::assertTrue($runTime > 0.12);
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_text_ElementNotFound_ExceptionThrown()
	{
		$browser = $this->getBrowser('blank');
		
		self::assertNull($browser->text('.a', 0.0, false));
	}
	
	public function test_text_ElementFound_TextReturned()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello</div>');
		
		self::assertEquals('hello', $browser->text('.a', 0.0, false));
	}
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\MultipleElementsExistException
	 */
	public function test_text_MoreThenOneElementExists_ExceptionThrown()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello</div><div class="a">hello</div>');
		
		self::assertNull($browser->text('.a', 0.0, false));
	}
	
	
	public function test_count_ElementNotFound_Return0()
	{
		$browser = $this->getBrowser('blank');
		
		self::assertEquals(0, $browser->count('.a', 0.0, false));
	}
	
	public function test_count_ElementsExist_ReturnCount()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello</div><div class="a">hello</div>');
		
		self::assertEquals(2, $browser->count('.a', 0.0, false));
	}
	
	public function test_count_ArrayOfSelectorsPassed_TotalCountReturned()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello</div><div class="a">hello</div>');
		
		self::assertEquals(4, $browser->count(['.a', '.b', 'txt:hello'], 0.0, false));
	}
	
	public function test_count_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello</div><div class="a">hELlo</div>');
		
		self::assertEquals(1, $browser->count(['txt:hello'], 0.0, true));
	}
	
	public function test_count_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('blank');
		
		
		$startTime = microtime(true);
		$browser->count(['.a'], 0.0, false);
		$emptyRunTime = microtime(true) - $startTime;
		
		
		$startTime = microtime(true);
		$browser->count(['.a', '.b'], 0.12, false);
		$runTime = microtime(true) - $startTime;
		
		
		self::assertTrue($runTime > $emptyRunTime);
		self::assertTrue($runTime > 0.12);
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_find_ElementNotFound_ExceptionThrown()
	{
		$browser = $this->getBrowser('blank');
		
		self::assertNull($browser->find('.a', 0.0, false));
	}
	
	public function test_find_ElementFound_ElementReturned()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello</div>');
		
		self::assertEquals('hello', $browser->find('.a', 0.0, false)->getText());
	}
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\MultipleElementsExistException
	 */
	public function test_find_MoreThenOneElementExists_ExceptionThrown()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello</div><div class="a">hello</div>');
		
		$browser->find('.a', 0.0, false);
	}
	
	public function test_find_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello</div><div class="a">hELlo</div>');
		
		self::assertNotNull($browser->find('txt:hello', 0.0, true));
	}
	
	public function test_find_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('blank');
		
		
		$startTime = microtime(true);
		try { $browser->find('.a', 0.0, false); } catch (ElementNotFoundException $e) {}
		$emptyRunTime = microtime(true) - $startTime;
		
		
		$startTime = microtime(true);
		try { $browser->find('.a', 0.12, false); } catch (ElementNotFoundException $e) {}
		$runTime = microtime(true) - $startTime;
		
		
		self::assertTrue($runTime > $emptyRunTime);
		self::assertTrue($runTime > 0.12);
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_findFirst_ElementNotFound_ExceptionThrown()
	{
		$browser = $this->getBrowser('blank');
		
		$browser->findFirst('.a', 0.0, false);
	}
	
	public function test_findFirst_ElementFound_ElementReturned()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello</div>');
		
		self::assertEquals('hello', $browser->findFirst('.a', 0.0, false)->getText());
	}
	
	public function test_findFirst_ArrayOfSelectorsPassed_FirstFoundSelectorReturned()
	{
		$browser = $this->getBrowserForContent('<div class="c">hello c</div><div class="b">hello b</div>');
		
		self::assertEquals('hello b', $browser->findFirst(['.a', '.b', '.c'], 0.0, false)->getText());
	}
	
	public function test_findFirst_MoreThenOneElementExists_FirstElementReturned()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello 1</div><div class="a">hello 2</div>');
		
		self::assertEquals('hello 1', $browser->findFirst('.a', 0.0, false)->getText());
	}
	
	public function test_findFirst_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello</div><div class="a">heLLo</div>');
		
		self::assertEquals('heLLo', $browser->findFirst('txt:heLLo', 0.0, true)->getText());
	}
	
	public function test_findFirst_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('blank');
		
		
		$startTime = microtime(true);
		try { $browser->findFirst('.a', 0.0, false); } catch (ElementNotFoundException $e) {} 
		$emptyRunTime = microtime(true) - $startTime;
		
		
		$startTime = microtime(true);
		try { $browser->findFirst('.a', 0.12, false); } catch (ElementNotFoundException $e) {}
		$runTime = microtime(true) - $startTime;
		
		
		self::assertTrue($runTime > $emptyRunTime);
		self::assertTrue($runTime > 0.12);
	}
	
	
	public function test_findAll_ElementNotFound_ReturnEmptyArray()
	{
		$browser = $this->getBrowser('blank');
		
		self::assertEmpty($browser->findAll('.a', 0.0, false)->get());
	}
	
	public function test_findAll_ElementFound_ElementReturned()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello</div>');
		
		$result = $browser->findAll('.a', 0.0, false)->get();
		
		self::assertCount(1, $result);
	}
	
	public function test_findAll_ElementsFound_AllElementsReturned()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello</div><div class="a">world</div>');
		
		$result = $browser->findAll('.a', 0.0, false)->get();
		
		self::assertCount(2, $result);
	}
	
	public function test_findAll_ArrayOfSelectorsPassed_AllElementsReturned()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello</div><div class="b">world</div>');
		
		$result = $browser->findAll(['.a', '.b', '.c'], 0.0, false)->get();
		
		self::assertCount(2, $result);
		self::assertEquals('hello', $result[0]->getText());
		self::assertEquals('world', $result[1]->getText());
	}
	
	public function test_findAll_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowserForContent('<div class="a">hello</div><div class="a">heLLo</div>');
		
		self::assertEquals('heLLo', $browser->findAll('txt:heLLo', 0.0, true)->get()[0]->getText());
	}
	
	public function test_findAll_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('blank');
		
		
		$startTime = microtime(true);
		$browser->findAll('.a', 0.0, false); 
		$emptyRunTime = microtime(true) - $startTime;
		
		
		$startTime = microtime(true);
		$browser->findAll('.a', 0.12, false);
		$runTime = microtime(true) - $startTime;
		
		
		self::assertTrue($runTime > $emptyRunTime);
		self::assertTrue($runTime > 0.12);
	}
	
	
	public function test_tryFind_ElementNotFound_ReturnNull()
	{
		$browser = $this->getBrowser('blank');
		
		self::assertNull($browser->tryFind('.a', 0.0, false));
	}
	
	public function test_tryFind_MoreThanOneElementExists_ReturnNull()
	{
		$browser = $this->getBrowserForContent('<div class="a"></div><div class="a"></div>');
		
		self::assertNull($browser->tryFind('.a', 0.0, false));
	}
	
	public function test_tryFind_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowserForContent('<div>a</div>');
		
		self::assertNull($browser->tryFind('txt:A', 0.0, true));
		self::assertNotNull($browser->tryFind('txt:A', 0.0, false));
	}
	
	public function test_tryFind_ElementExists_ElementReturned()
	{
		$browser = $this->getBrowserForContent('<div class="a"></div>');
		
		self::assertNotNull($browser->tryFind('.a', 0.0, false));
	}
	
	
	public function test_tryFindFirst_ElementNotFound_ReturnNull()
	{
		$browser = $this->getBrowser('blank');
		
		self::assertNull($browser->tryFindFirst('.a', 0.0, false));
	}
	
	public function test_tryFindFirst_ElementExists_ElementReturned()
	{
		$browser = $this->getBrowserForContent('<div class="a"></div>');
		
		self::assertNotNull($browser->tryFindFirst('.a', 0.0, false)->getText());
	}
	
	public function test_tryFindFirst_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowserForContent('<div>a</div>');
		
		self::assertNull($browser->tryFindFirst('txt:A', 0.0, true));
		self::assertNotNull($browser->tryFindFirst('txt:A', 0.0, false));
	}
	
	public function test_tryFindFirst_ArrayOfSelectorsPassed_ReturnFirstfound()
	{
		$browser = $this->getBrowserForContent('<div class="a">a</div><div class="B">b</div>');
		
		self::assertEquals('b', $browser->tryFindFirst(['.c', '.b'], 0.0, false)->getText());
	}
	
	public function test_tryFindFirst_MoreThanOneElementExists_ReturnFirstElement()
	{
		$browser = $this->getBrowserForContent('<div class="a">a</div><div class="a">b</div>');
		
		self::assertNotNull($browser->tryFindFirst('.a', 0.0, false));
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_waitForElement_ElementMissing_ExceptionThrown()
	{
		$browser = $this->getBrowserForContent('<div>b</div>');
		
		$browser->waitForElement('txt:a', 0.0);
	}
	
	public function test_waitForElement_ElementExists_NoErrors()
	{
		$browser = $this->getBrowserForContent('<div>a</div>');
		
		$browser->waitForElement('txt:a', 0.0);
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_waitForAnyElements_ElementMissing_ExceptionThrown()
	{
		$browser = $this->getBrowserForContent('<div>b</div>');
		
		$browser->waitForAnyElements(['txt:a', 'txt:c'], 0.0);
	}
	
	public function test_waitForAnyElements_ElementFound_NoErrors()
	{
		$browser = $this->getBrowserForContent('<div>a</div>');
		
		$browser->waitForAnyElements(['txt:c', 'txt:a'], 0.0);
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_waitForAllElements_ElementMissing_ExceptionThrown()
	{
		$browser = $this->getBrowserForContent('<div>a</div>');
		
		$browser->waitForAllElements(['txt:a', 'txt:b', 'txt:c'], 0.0);
	}
	
	public function test_waitForAllElements_AllElementsExist_NoErrors()
	{
		$browser = $this->getBrowserForContent('<div>a</div><div>b</div><div>b</div><div>c</div>');
		
		$browser->waitForAllElements(['txt:a', 'txt:b', 'txt:c'], 0.0);
	}
}