<?php
namespace SeTaco\Repeat;


use SeTaco\Config\QueryConfig;


require_once '../AbstractBrowserTestCase.php';


class RepeaterTest extends \AbstractBrowserTestCase
{
	private function getConfig(): QueryConfig
	{
		return new QueryConfig();
	}
	
	
	/**
	 * @expectedException \SeTaco\Exceptions\RepeaterException
	 */
	public function test_while_TimedOut_ThrowException()
	{
		$subject = new Repeater($this->getBrowserToFile('file1'), $this->getConfig());
		
		$subject->while(
				function ()
				{
					return true;
				},
				0.1,
				1
			)
			->execute(function () {});
	}
	
	public function test_while_StopOnFalse()
	{
		$subject = new Repeater($this->getBrowserToFile('file1'), $this->getConfig());
		
		$count = 0;
		
		$subject->while(
				function () use (&$count)
				{
					$count++;
					return $count < 2;
				},
				0.1,
				1
			)
			->execute(function () {});
		
		self::assertEquals(2, $count);
	}
	
	public function test_whileNull_StopOnNotNull()
	{
		$subject = new Repeater($this->getBrowserToFile('file1'), $this->getConfig());
		
		$count = 0;
		
		$subject->whileNull(
				function () use (&$count)
				{
					$count++;
					return $count < 2 ? null : true;
				},
				0.1,
				1
			)
			->execute(function () {});
		
		self::assertEquals(2, $count);
	}
	
	public function test_whileNotNull_StopOnNull()
	{
		$subject = new Repeater($this->getBrowserToFile('file1'), $this->getConfig());
		
		$count = 0;
		
		$subject->whileNotNull(
				function () use (&$count)
				{
					$count++;
					return $count < 2 ? false : null;
				},
				0.1,
				1
			)
			->execute(function () {});
		
		self::assertEquals(2, $count);
	}
	
	public function test_whileEquals_StopOnNotEquals()
	{
		$subject = new Repeater($this->getBrowserToFile('file1'), $this->getConfig());
		
		$count = 0;
		
		$subject->whileEquals(
				function () use (&$count)
				{
					$count++;
					return $count < 2 ? '1' : false;
				},
				1,
				0.1,
				1
			)
			->execute(function () {});
		
		self::assertEquals(2, $count);
	}
	
	public function test_whileNotEquals_StopOnEquals()
	{
		$subject = new Repeater($this->getBrowserToFile('file1'), $this->getConfig());
		
		$count = 0;
		
		$subject->whileNotEquals(
				function () use (&$count)
				{
					$count++;
					return $count < 2 ? false : '1';
				},
				1,
				0.1,
				1
			)
			->execute(function () {});
		
		self::assertEquals(2, $count);
	}
	
	public function test_whileSame_StopOnNotIdentical()
	{
		$subject = new Repeater($this->getBrowserToFile('file1'), $this->getConfig());
		
		$count = 0;
		
		$subject->whileSame(
				function () use (&$count)
				{
					$count++;
					return $count < 2 ? 1 : '1';
				},
				1,
				0.1,
				1
			)
			->execute(function () {});
		
		self::assertEquals(2, $count);
	}
	
	public function test_whileNotSame_StopOnIdentical()
	{
		$subject = new Repeater($this->getBrowserToFile('file1'), $this->getConfig());
		
		$count = 0;
		
		$subject->whileNotSame(
				function () use (&$count)
				{
					$count++;
					return $count < 2 ? '1' : 1;
				},
				1,
				0.1,
				1
			)
			->execute(function () {});
		
		self::assertEquals(2, $count);
	}
	
	public function test_whileThrowing_StopOnNotException()
	{
		$subject = new Repeater($this->getBrowserToFile('file1'), $this->getConfig());
		
		$count = 0;
		
		$subject->whileThrowing(
				function () use (&$count)
				{
					$count++;
					
					if ($count < 2)
						throw new \Exception();
					
					return false;
				},
				0.1,
				1
			)
			->execute(function () {});
		
		self::assertEquals(2, $count);
	}
	
	
	public function test_whileElementExists_StopOnElementDisappears()
	{
		$browser = $this->getBrowserToFile('ElementRemoveOnClick');
		
		$subject = new Repeater($browser, $this->getConfig());
		
		$subject->whileElementExists(
				'#div_4',
				0.1,
				1
			)
			->execute(function () use ($browser)
			{
				$browser->click('button');
			});
		
		self::assertTrue($browser->exists('#div_1'));
		self::assertTrue($browser->exists('#div_2'));
		self::assertTrue($browser->exists('#div_3'));
		self::assertFalse($browser->exists('#div_4'));
		self::assertFalse($browser->exists('#div_5'));
	}
	
	public function test_whileElementMissing_StopOnElementAppear()
	{
	
	}
	
	public function test_whileAnyElementExists_StopOnAllElementsDisappear()
	{
	
	}
	
	public function test_whileAnyElementMissing_StopOnAllElementsAppear()
	{
	
	}
	
	public function test_whileAllElementsExist_StopOnFirstElementDisappear()
	{
	
	}
	
	public function test_whileAllElementsMissing_StopOnFirstElementAppear()
	{
	
	}
}