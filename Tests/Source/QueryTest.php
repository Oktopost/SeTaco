<?php
namespace SeTaco\Source;


use function GuzzleHttp\Psr7\_caseless_remove;
use PHPUnit\Framework\TestCase;
use SeTaco\BrowserSession;
use SeTaco\Exceptions\Query\ElementNotFoundException;
use SeTaco\Exceptions\Query\ElementStillExistsException;
use SeTaco\Exceptions\Query\QueriedElementNotClickableException;
use SeTaco\IBrowser;
use SeTaco\TacoConfig;
use Structura\Random;
use Structura\Strings;


class QueryTest extends TestCase
{
	private const PATH = __DIR__ . '/../html/Query';
	
	
	/** @var BrowserSession */
	private static $session = null;
	
	
	private function getBrowserToFile(string $path): IBrowser
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
	
	private function getBrowser(string $content): IBrowser
	{
		$tmpFile = self::PATH . '/tmp.html';
		$templateFile = self::PATH . '/_template_.html';
		
		if (file_exists($tmpFile))
			unlink($tmpFile);
		
		$template = file_get_contents($templateFile);
		$data = str_replace('{data}', $content, $template);
		
		file_put_contents($tmpFile, $data);
		
		return $this->getBrowserToFile('tmp.html');
	}
	
	
	public function test_exists_ElementNotFound_ReturnFalse()
	{
		$browser = $this->getBrowser('');
		
		
		self::assertFalse($browser->exists('.not_found', 0.0, false));
	}
	
	public function test_exists_ElementFound_ReturnTrue()
	{
		$browser = $this->getBrowser('<div>Hello</div>');
		
		
		self::assertTrue($browser->exists('txt:hello', 0.0, false));
	}
	
	public function test_exists_CaseSensitiveFlagUsed_ReturnTrue()
	{
		$browser = $this->getBrowser('<div>Hello</div>');
		
		
		self::assertFalse($browser->exists('txt:hello', 0.0, true));
		self::assertTrue($browser->exists('txt:Hello', 0.0, true));
	}
	
	public function test_exists_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('');
		
		
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
		$browser = $this->getBrowser('');
		
		
		self::assertFalse($browser->existsAny(['.a', '.b'], 0.0, false));
	}
	
	public function test_existsAny_ElementsFound_ReturnTrue()
	{
		$browser = $this->getBrowser('<div>hello</div>');
		
		self::assertTrue($browser->existsAny(['.a', 'txt:hello', '.b'], 0.0, false));
	}
	
	public function test_existsAny_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowser('<div>HeLLo</div>');
		
		self::assertFalse($browser->existsAny(['.a', 'txt:Hello', '.b'], 0.0, true));
		self::assertTrue($browser->existsAny(['.a', 'txt:HeLLo', '.b'], 0.0, true));
	}
	
	public function test_existsAny_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('');
		
		
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
		$browser = $this->getBrowser('<div>Hello</div>');
		
		
		self::assertFalse($browser->existsAll(['.a', '.b'], 0.0, false));
		self::assertFalse($browser->existsAll(['.a', 'txt:hello'], 0.0, false));
	}
	
	public function test_existsAll_ElementsFound_ReturnTrue()
	{
		$browser = $this->getBrowser('<div>hello</div><div>World</div><div>hello</div>');
		
		self::assertTrue($browser->existsAll(['txt:world', 'txt:hello'], 0.0, false));
	}
	
	public function test_existsAll_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowser('<div>HeLLo</div>');
		
		self::assertFalse($browser->existsAll(['txt:Hello'], 0.0, true));
		self::assertTrue($browser->existsAll(['txt:HeLLo'], 0.0, true));
	}
	
	public function test_existsAll_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('');
		
		
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
		$browser = $this->getBrowser('');
		
		self::assertNull($browser->text('.a', 0.0, false));
	}
	
	public function test_text_ElementFound_TextReturned()
	{
		$browser = $this->getBrowser('<div class="a">hello</div>');
		
		self::assertEquals('hello', $browser->text('.a', 0.0, false));
	}
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\MultipleElementsExistException
	 */
	public function test_text_MoreThenOneElementExists_ExceptionThrown()
	{
		$browser = $this->getBrowser('<div class="a">hello</div><div class="a">hello</div>');
		
		self::assertNull($browser->text('.a', 0.0, false));
	}
	
	
	public function test_count_ElementNotFound_Return0()
	{
		$browser = $this->getBrowser('');
		
		self::assertEquals(0, $browser->count('.a', 0.0, false));
	}
	
	public function test_count_ElementsExist_ReturnCount()
	{
		$browser = $this->getBrowser('<div class="a">hello</div><div class="a">hello</div>');
		
		self::assertEquals(2, $browser->count('.a', 0.0, false));
	}
	
	public function test_count_ArrayOfSelectorsPassed_TotalCountReturned()
	{
		$browser = $this->getBrowser('<div class="a">hello</div><div class="a">hello</div>');
		
		self::assertEquals(4, $browser->count(['.a', '.b', 'txt:hello'], 0.0, false));
	}
	
	public function test_count_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowser('<div class="a">hello</div><div class="a">hELlo</div>');
		
		self::assertEquals(1, $browser->count(['txt:hello'], 0.0, true));
	}
	
	public function test_count_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('');
		
		
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
		$browser = $this->getBrowser('');
		
		self::assertNull($browser->find('.a', 0.0, false));
	}
	
	public function test_find_ElementFound_ElementReturned()
	{
		$browser = $this->getBrowser('<div class="a">hello</div>');
		
		self::assertEquals('hello', $browser->find('.a', 0.0, false)->getText());
	}
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\MultipleElementsExistException
	 */
	public function test_find_MoreThenOneElementExists_ExceptionThrown()
	{
		$browser = $this->getBrowser('<div class="a">hello</div><div class="a">hello</div>');
		
		$browser->find('.a', 0.0, false);
	}
	
	public function test_find_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowser('<div class="a">hello</div><div class="a">hELlo</div>');
		
		self::assertNotNull($browser->find('txt:hello', 0.0, true));
	}
	
	public function test_find_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('');
		
		
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
		$browser = $this->getBrowser('');
		
		$browser->findFirst('.a', 0.0, false);
	}
	
	public function test_findFirst_ElementFound_ElementReturned()
	{
		$browser = $this->getBrowser('<div class="a">hello</div>');
		
		self::assertEquals('hello', $browser->findFirst('.a', 0.0, false)->getText());
	}
	
	public function test_findFirst_ArrayOfSelectorsPassed_FirstFoundSelectorReturned()
	{
		$browser = $this->getBrowser('<div class="c">hello c</div><div class="b">hello b</div>');
		
		self::assertEquals('hello b', $browser->findFirst(['.a', '.b', '.c'], 0.0, false)->getText());
	}
	
	public function test_findFirst_MoreThenOneElementExists_FirstElementReturned()
	{
		$browser = $this->getBrowser('<div class="a">hello 1</div><div class="a">hello 2</div>');
		
		self::assertEquals('hello 1', $browser->findFirst('.a', 0.0, false)->getText());
	}
	
	public function test_findFirst_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowser('<div class="a">hello</div><div class="a">heLLo</div>');
		
		self::assertEquals('heLLo', $browser->findFirst('txt:heLLo', 0.0, true)->getText());
	}
	
	public function test_findFirst_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('');
		
		
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
		$browser = $this->getBrowser('');
		
		self::assertEmpty($browser->findAll('.a', 0.0, false)->get());
	}
	
	public function test_findAll_ElementFound_ElementReturned()
	{
		$browser = $this->getBrowser('<div class="a">hello</div>');
		
		$result = $browser->findAll('.a', 0.0, false)->get();
		
		self::assertCount(1, $result);
	}
	
	public function test_findAll_ElementsFound_AllElementsReturned()
	{
		$browser = $this->getBrowser('<div class="a">hello</div><div class="a">world</div>');
		
		$result = $browser->findAll('.a', 0.0, false)->get();
		
		self::assertCount(2, $result);
	}
	
	public function test_findAll_ArrayOfSelectorsPassed_AllElementsReturned()
	{
		$browser = $this->getBrowser('<div class="a">hello</div><div class="b">world</div>');
		
		$result = $browser->findAll(['.a', '.b', '.c'], 0.0, false)->get();
		
		self::assertCount(2, $result);
		self::assertEquals('hello', $result[0]->getText());
		self::assertEquals('world', $result[1]->getText());
	}
	
	public function test_findAll_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowser('<div class="a">hello</div><div class="a">heLLo</div>');
		
		self::assertEquals('heLLo', $browser->findAll('txt:heLLo', 0.0, true)->get()[0]->getText());
	}
	
	public function test_findAll_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('');
		
		
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
		$browser = $this->getBrowser('');
		
		self::assertNull($browser->tryFind('.a', 0.0, false));
	}
	
	public function test_tryFind_MoreThanOneElementExists_ReturnNull()
	{
		$browser = $this->getBrowser('<div class="a"></div><div class="a"></div>');
		
		self::assertNull($browser->tryFind('.a', 0.0, false));
	}
	
	public function test_tryFind_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowser('<div>a</div>');
		
		self::assertNull($browser->tryFind('txt:A', 0.0, true));
		self::assertNotNull($browser->tryFind('txt:A', 0.0, false));
	}
	
	public function test_tryFind_ElementExists_ElementReturned()
	{
		$browser = $this->getBrowser('<div class="a"></div>');
		
		self::assertNotNull($browser->tryFind('.a', 0.0, false));
	}
	
	
	public function test_tryFindFirst_ElementNotFound_ReturnNull()
	{
		$browser = $this->getBrowser('');
		
		self::assertNull($browser->tryFindFirst('.a', 0.0, false));
	}
	
	public function test_tryFindFirst_ElementExists_ElementReturned()
	{
		$browser = $this->getBrowser('<div class="a"></div>');
		
		self::assertNotNull($browser->tryFindFirst('.a', 0.0, false)->getText());
	}
	
	public function test_tryFindFirst_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowser('<div>a</div>');
		
		self::assertNull($browser->tryFindFirst('txt:A', 0.0, true));
		self::assertNotNull($browser->tryFindFirst('txt:A', 0.0, false));
	}
	
	public function test_tryFindFirst_ArrayOfSelectorsPassed_ReturnFirstfound()
	{
		$browser = $this->getBrowser('<div class="a">a</div><div class="B">b</div>');
		
		self::assertEquals('b', $browser->tryFindFirst(['.c', '.b'], 0.0, false)->getText());
	}
	
	public function test_tryFindFirst_MoreThanOneElementExists_ReturnFirstElement()
	{
		$browser = $this->getBrowser('<div class="a">a</div><div class="a">b</div>');
		
		self::assertNotNull($browser->tryFindFirst('.a', 0.0, false));
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_waitForElement_ElementMissing_ExceptionThrown()
	{
		$browser = $this->getBrowser('<div>b</div>');
		
		$browser->waitForElement('txt:a', 0.0);
	}
	
	public function test_waitForElement_ElementExists_NoErrors()
	{
		$browser = $this->getBrowser('<div>a</div>');
		
		$browser->waitForElement('txt:a', 0.0);
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_waitForAnyElements_ElementMissing_ExceptionThrown()
	{
		$browser = $this->getBrowser('<div>b</div>');
		
		$browser->waitForAnyElements(['txt:a', 'txt:c'], 0.0);
	}
	
	public function test_waitForAnyElements_ElementFound_NoErrors()
	{
		$browser = $this->getBrowser('<div>a</div>');
		
		$browser->waitForAnyElements(['txt:c', 'txt:a'], 0.0);
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_waitForElements_ElementMissing_ExceptionThrown()
	{
		$browser = $this->getBrowser('<div>a</div>');
		
		$browser->waitForElements(['txt:a', 'txt:b', 'txt:c'], 0.0);
	}
	
	public function test_waitForElements_AllElementsExist_NoErrors()
	{
		$browser = $this->getBrowser('<div>a</div><div>b</div><div>b</div><div>c</div>');
		
		$browser->waitForElements(['txt:a', 'txt:b', 'txt:c'], 0.0);
	}
	
	public function test_waitForElements_QueryTimeoutUsed()
	{
		$browser = $this->getBrowser('<div class="a"></div>');
		
		
		$startTime = microtime(true);
		$browser->waitForElements('.a', 0.0, false); 
		$emptyRunTime = microtime(true) - $startTime;
		
		
		$startTime = microtime(true);
		try { $browser->waitForElements('.b', 0.12, false); } catch (ElementNotFoundException $e) {}
		$runTime = microtime(true) - $startTime;
		
		
		self::assertTrue($runTime > $emptyRunTime);
		self::assertTrue($runTime > 0.12);
	}
	
	
	public function test_waitToDisappear_ElementMissing_NoErrors()
	{
		$browser = $this->getBrowser('<div>d</div>');
		$browser->waitToDisappear('.cls', 0.0);
	}
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementStillExistsException
	 */
	public function test_waitToDisappear_ElementStillExists_ErrorThrown()
	{
		$browser = $this->getBrowser('<div>d</div>');
		$browser->waitToDisappear('txt:d', 0.0);
	}
	
	public function test_waitToDisappear_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowser('<div>d</div>');
		
		$browser->waitToDisappear('txt:D', 0.0, true);
		
		try 
		{ 
			$browser->waitToDisappear('txt:d', 0.0, true);
			self::fail();
		}
		catch (ElementStillExistsException $e)
		{
			return;
		}
	}
	
	public function test_waitToDisappear_TimeoutUsed()
	{
		$browser = $this->getBrowser('<div class="a">d</div>');
		
		
		$startTime = microtime(true);
		try { $browser->waitToDisappear('.a', 0.0, false); } catch (ElementStillExistsException $e) {} 
		$emptyRunTime = microtime(true) - $startTime;
		
		
		$startTime = microtime(true);
		try { $browser->waitToDisappear('.a', 0.12, false); } catch (ElementStillExistsException $e) {}
		$runTime = microtime(true) - $startTime;
		
		
		self::assertTrue($runTime > $emptyRunTime);
		self::assertTrue($runTime > 0.12);
	}
	
	
	public function test_waitAllToDisappear_ElementMissing_NoErrors()
	{
		$browser = $this->getBrowser('');
		$browser->waitAllToDisappear(['txt:hello', 'txt:world'], 0.0);
	}
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementStillExistsException
	 */
	public function test_waitAllToDisappear_ElementsStillExists_ErrorThrown()
	{
		$browser = $this->getBrowser('<div>d</div>');
		$browser->waitAllToDisappear(['txt:d', 'txt:a'], 0.0);
	}
	
	public function test_waitAllToDisappear_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowser('<div>d</div>');
		
		$browser->waitAllToDisappear(['txt:D'], 0.0, true);
		
		try 
		{ 
			$browser->waitAllToDisappear('txt:d', 0.0, true);
			self::fail();
		}
		catch (ElementStillExistsException $e)
		{
			return;
		}
	}
	
	public function test_waitAllToDisappear_TimeoutUsed()
	{
		$browser = $this->getBrowser('<div class="a">d</div>');
		
		
		$startTime = microtime(true);
		try { $browser->waitAllToDisappear('.a', 0.0, false); } catch (ElementStillExistsException $e) {} 
		$emptyRunTime = microtime(true) - $startTime;
		
		
		$startTime = microtime(true);
		try { $browser->waitAllToDisappear('.a', 0.12, false); } catch (ElementStillExistsException $e) {}
		$runTime = microtime(true) - $startTime;
		
		
		self::assertTrue($runTime > $emptyRunTime);
		self::assertTrue($runTime > 0.12);
	}
	
	
	public function test_waitAnyToDisappear_ElementsMissing_NoErrors()
	{
		$browser = $this->getBrowser('');
		$browser->waitAnyToDisappear(['txt:hello', 'txt:world'], 0.0);
	}
	
	public function test_waitAnyToDisappear_AtLeastOneElementMissing_NoErrors()
	{
		$browser = $this->getBrowser('<div>hello</div>');
		$browser->waitAnyToDisappear(['txt:hello', 'txt:world'], 0.0);
	}
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementStillExistsException
	 */
	public function test_waitAnyToDisappear_AllElementsStillExists_ErrorThrown()
	{
		$browser = $this->getBrowser('<div>d</div><div>a</div>');
		$browser->waitAnyToDisappear(['txt:d', 'txt:a'], 0.0);
	}
	
	public function test_waitAnyToDisappear_CaseSensitiveFlagUsed()
	{
		$browser = $this->getBrowser('<div>d</div>');
		
		$browser->waitAnyToDisappear(['txt:D'], 0.0, true);
		
		try 
		{ 
			$browser->waitAnyToDisappear('txt:d', 0.0, true);
			self::fail();
		}
		catch (ElementStillExistsException $e)
		{
			return;
		}
	}
	
	public function test_waitAnyToDisappear_TimeoutUsed()
	{
		$browser = $this->getBrowser('<div class="a">d</div>');
		
		
		$startTime = microtime(true);
		try { $browser->waitAnyToDisappear('.a', 0.0, false); } catch (ElementStillExistsException $e) {} 
		$emptyRunTime = microtime(true) - $startTime;
		
		
		$startTime = microtime(true);
		try { $browser->waitAnyToDisappear('.a', 0.12, false); } catch (ElementStillExistsException $e) {}
		$runTime = microtime(true) - $startTime;
		
		
		self::assertTrue($runTime > $emptyRunTime);
		self::assertTrue($runTime > 0.12);
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_input_ElementNotFound_ExceptionThrown()
	{
		$browser = $this->getBrowser('');
		$browser->input('.a', 'abc', 0.0);
	}
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\QueriedElementNotEditableException
	 */
	public function test_input_ElementNotInputField_ExceptionThrown()
	{
		$browser = $this->getBrowser('<div class="a"></div>');
		$browser->input('.a', 'abc', 0.0);
	}
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\MultipleElementsExistException
	 */
	public function test_input_MoreThenOneElementExists_ExceptionThrown()
	{
		$browser = $this->getBrowser('<input class="a"/><input class="a"/>');
		$browser->input('.a', 'abc', 0.0);
	}
	
	public function test_input_ValidElement_InputSet()
	{
		$str = Random::string(32);
		$browser = $this->getBrowser('<input class="a"/>');
		$browser->input('.a', $str, 0.0);
		
		self::assertEquals($str, $browser->findFirst('.a')->getValue());
	}
	
	public function test_input_TimeoutUsed()
	{
		$browser = $this->getBrowser('<input class="a"/>');
		
		
		$startTime = microtime(true);
		$browser->input('.a', 'abc', 0.0, false);
		$emptyRunTime = microtime(true) - $startTime;
		
		
		$startTime = microtime(true);
		try { $browser->input('.b', 'abc', 0.12, false); } catch (ElementNotFoundException $e) {}
		$runTime = microtime(true) - $startTime;
		
		
		self::assertTrue($runTime > $emptyRunTime);
		self::assertTrue($runTime > 0.12);
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_click_ElementNotFound_ExceptionThrown()
	{
		$browser = $this->getBrowser('');
		$browser->click('.a', 0.0);
	}
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\QueriedElementNotClickableException
	 */
	public function test_click_ElementNotClickable_ExceptionThrown()
	{
		$browser = $this->getBrowser('<div class="a"></div>');
		$browser->click('.a', 0.0);
	}
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\MultipleElementsExistException
	 */
	public function test_click_MoreThenOneElementExists_ExceptionThrown()
	{
		$browser = $this->getBrowser('<button class="a"/><button class="a"/>');
		$browser->click('.a', 0.0);
	}
	
	public function test_click_ValidElement_InputSet()
	{
		$browser = $this->getBrowser('<button class="a" onclick="this.innerHTML=\'Goodbye\';">Hello</button>');
		
		
		$browser->click('.a', 0.0);
		
		
		self::assertNull($browser->tryFind('txt:Hello', 0.0));
		self::assertNotNull($browser->findFirst('txt:Goodbye', 0.0));
	}
	
	public function test_click_TimeoutUsed()
	{
		$browser = $this->getBrowser('<button class="a">');
		
		
		$startTime = microtime(true);
		$browser->click('.a', 0.0, false);
		$emptyRunTime = microtime(true) - $startTime;
		
		
		$startTime = microtime(true);
		try { $browser->click('.b', 0.12, false); } catch (ElementNotFoundException $e) {}
		$runTime = microtime(true) - $startTime;
		
		
		self::assertTrue($runTime > $emptyRunTime);
		self::assertTrue($runTime > 0.12);
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_clickAny_ElementNotFound_ExceptionThrown()
	{
		$browser = $this->getBrowser('');
		$browser->clickAny('.a', 0.0);
	}
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\QueriedElementNotClickableException
	 */
	public function test_clickAny_ElementNotClickable_ExceptionThrown()
	{
		$browser = $this->getBrowser('<div class="a"></div>');
		$browser->clickAny('.a', 0.0);
	}
	
	public function test_clickAny_MoreThenOneElementExists_FirstElementClicked()
	{
		$browser = $this->getBrowser(
			'<button class="a" onclick="this.innerHTML=\'Goodbye 1\';">Hello</button>' . 
			'<button class="a" onclick="this.innerHTML=\'Goodbye 2\';">Hello</button>'
		);
		
		
		$browser->clickAny('.a', 0.0);
		
		
		self::assertNull($browser->tryFind('txt:Goodbye 2', 0.0));
		self::assertNotNull($browser->findFirst('txt:Goodbye 1', 0.0));
	}
	
	public function test_clickAny_NonClickableAndClickableElementsExist_FirstClickableElementClicked()
	{
		$browser = $this->getBrowser(
			'<div class="a"></div>' . 
			'<button class="a" onclick="this.innerHTML=\'Goodbye\';">Hello</button>'
		);
		
		
		$browser->clickAny('.a', 0.0);
		
		
		self::assertNull($browser->tryFind('txt:Hello', 0.0));
		self::assertNotNull($browser->findFirst('txt:Goodbye', 0.0));
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_hover_ElementNotFound_ExceptionThrown()
	{
		$browser = $this->getBrowser('');
		$browser->hover('.a', 0.0);
	}
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\MultipleElementsExistException
	 */
	public function test_hover_MoreThenOneElementExists_ExceptionThrown()
	{
		$browser = $this->getBrowser('<div class="a">abc</div><div class="a">def</div>');
		$browser->hover('.a', 0.0);
	}
	
	public function test_hover_ValidElement_InputSet()
	{
		$browser = $this->getBrowser('<div class="a" onmouseover="this.innerHTML=\'Goodbye\';">Hello</div>');
		
		
		$browser->hover('.a', 0.0);
		
		
		self::assertNull($browser->tryFind('txt:Hello', 0.0));
		self::assertNotNull($browser->findFirst('txt:Goodbye', 0.0));
	}
	
	public function test_hover_TimeoutUsed()
	{
		$browser = $this->getBrowser('<div class="a">abc</div>');
		
		
		$startTime = microtime(true);
		$browser->hover('.a', 0.0, false);
		$emptyRunTime = microtime(true) - $startTime;
		
		
		$startTime = microtime(true);
		try { $browser->hover('.b', 0.12, false); } catch (ElementNotFoundException $e) {}
		$runTime = microtime(true) - $startTime;
		
		
		self::assertTrue($runTime > $emptyRunTime);
		self::assertTrue($runTime > 0.12);
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_hoverAny_ElementNotFound_ExceptionThrown()
	{
		$browser = $this->getBrowser('');
		$browser->hoverAny('.a', 0.0);
	}
	
	public function test_hoverAny_MoreThenOneElementExists_FirstElementHovered()
	{
		$browser = $this->getBrowser(
			'<div class="a" onmouseover="this.innerHTML=\'Goodbye 1\';">Hello 1</div>' . 
			'<div class="a" onmouseover="this.innerHTML=\'Goodbye 2\';">Hello 2</div>'
		);
		
		
		$browser->hoverAny('.a', 0.0);
		
		
		self::assertNull($browser->tryFind('txt:Hello 1', 0.0));
		self::assertNotNull($browser->tryFind('txt:Hello 2', 0.0));
		
		self::assertNotNull($browser->tryFind('txt:Goodbye 1', 0.0));
		self::assertNull($browser->tryFind('txt:Goodbye 2', 0.0));	
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_hoverAndClick_ElementNotFound_ExceptionThrown()
	{
		$browser = $this->getBrowser('');
		$browser->hoverAndClick('.a', 0.0);
	}
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\MultipleElementsExistException
	 */
	public function test_hoverAndClick_MoreThenOneElementExists_ExceptionThrown()
	{
		$browser = $this->getBrowser('<div class="a">abc</div><div class="a">def</div>');
		$browser->hoverAndClick('.a', 0.0);
	}
	
	public function test_hoverAndClick_ElementExists_ElementHoveredAndClicked()
	{
		$browser = $this->getBrowser(
			'<div onmouseover="getElementById(\'btn\').style.visibility=null">' . 
				'<button onclick="this.innerHTML=\'Goodbye\';" style="visibility: hidden" id="btn">Hello</button>' . 
			'</div>'
		);
		
		
		try
		{
			$browser->click('#btn', 0.0);
			$this->fail('Element is clickable without hover.');
		}
		catch (QueriedElementNotClickableException $e)
		{
			// Sanity test
		}
		
		
		$browser->hoverAndClick('#btn');
		
		
		self::assertNull($browser->tryFind('txt:Hello', 0.0));
		self::assertNotNull($browser->tryFind('txt:Goodbye', 0.0));
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\Query\ElementNotFoundException
	 */
	public function test_hoverAndClickAny_ElementNotFound_ExceptionThrown()
	{
		$browser = $this->getBrowser('');
		$browser->hoverAndClickAny('.a', 0.0);
	}
	
	public function test_hoverAndClickAny_MultipleElementsExist_OnlyHoverableElementIsUsed()
	{
		$browser = $this->getBrowser(
			'<div class="a"></div>' . 
			'<div onmouseover="getElementById(\'btn\').style.visibility=null">' . 
				'<button class="a" onclick="this.innerHTML=\'Goodbye\';" style="visibility: hidden" id="btn">Hello</button>' . 
			'</div>'
		);
		
		
		try
		{
			$browser->clickAny('.a', 0.0);
			$this->fail('Element is clickable without hover.');
		}
		catch (QueriedElementNotClickableException $e)
		{
			// Sanity test
		}
		
		
		$browser->hoverAndClickAny('#btn');
		
		
		self::assertNull($browser->tryFind('txt:Hello', 0.0));
		self::assertNotNull($browser->tryFind('txt:Goodbye', 0.0));
	}
	
	
	public function test_while_SanityTest()
	{
		$counter = 0;
		$calledCounter = 0;
		$browser = $this->getBrowser('');
		
		$endTime = microtime(true) + 1.0;
		
		$browser->while(function() 
				use (&$counter, $endTime) 
			{
				$counter++;
				return ($endTime > microtime(true) + 0.1);
			},
			0.1,
			1.0)
			->execute(function ()
				use (&$calledCounter)
			{
				$calledCounter++;
			});
		
		self::assertEquals(1, $calledCounter);
		self::assertTrue($counter >= 8 && $counter <= 10);
	}
}