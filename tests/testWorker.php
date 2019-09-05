<?php
declare(strict_types=1);
require_once '../worker.php';
use PHPUnit\Framework\TestCase;

final class WorkerTest extends TestCase {

    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new Worker();
    }

    protected function tearDown()
    {
        $this->fixture = NULL;
    }
    		
	public function testWorkerOk()
	{
		$json ='{ "job": {"text": "Привет, мне на <a href=\"test@test.ru\">test@test.ru</a> пришло приглашение встретиться, попить кофе с <strong>10%</strong> содержанием молока за <i>$5</i>, пойдем вместе!",
				"methods": [
					"stripTags", "removeSpaces", "replaceSpacesToEol", "htmlspecialchars", "removeSymbols", "toNumber"
				]
		}}';
	    $this->assertEquals('{"text":"10"}', $this->fixture->getResult($json) );
	}

	public function testWorker_stripTags()
	{
		$json ='{ "job": {"text": "<a href=\"test@test.ru\">test@test.ru</a><strong>10%</strong><i>$5</i>",
				"methods": [ "stripTags" ]
		}}';
	    $this->assertEquals('{"text":"test@test.ru10%$5"}', $this->fixture->getResult($json) );
	}

	public function testWorker_removeSpaces()
	{
		$json ='{ "job": {"text": "aaa bbb ccc",
				"methods": [ "removeSpaces"	]
		}}';
	    $this->assertEquals('{"text":"aaabbbccc"}', $this->fixture->getResult($json) );
	}

	
	public function testWorker_replaceSpacesToEol()
	{
		$json ='{ "job": {"text": "aaa bbb ccc",
				"methods": [ "replaceSpacesToEol" ]
		}}';
	    $this->assertEquals('{"text":"aaa\nbbb\nccc"}', $this->fixture->getResult($json) );
	}


	public function testWorker_htmlspecialchars()
	{
		$json ='{ "job": {"text": "<a href=\'test\'>Test</a>",
				"methods": [ "htmlspecialchars" ]
		}}';
	    $this->assertEquals('{"text":"&lt;a href=&#039;test&#039;&gt;Test&lt;\/a&gt;"}', $this->fixture->getResult($json) );
	}

	public function testWorker_removeSymbols()
	{
		$json ='{ "job": {"text": "[.,/!@#$%^&*()]",
				"methods": [ "removeSymbols" ]
		}}';
	    $this->assertEquals('{"text":""}', $this->fixture->getResult($json) );
	}

	public function testWorker_toNumber()
	{
		$json ='{ "job": {"text": "aaa10bbb",
				"methods": [ "toNumber" ]
		}}';
	    $this->assertEquals('{"text":"10"}', $this->fixture->getResult($json) );
	}

	public function testWorkerFirstNumberFromString()
	{
		$json ='{ "job": {"text": "Test 10 and 20 and 30 and 40 and 50",
				"methods": [
					"stripTags", "removeSpaces", "replaceSpacesToEol", "htmlspecialchars", "removeSymbols", "toNumber"
				]
		}}';
	    $this->assertEquals('{"text":"10"}', $this->fixture->getResult($json) );
	}
	
	public function testWorkerNoMethods()
	{
		$json ='{ "job": {"text": "sometext"} }';
		$this->expectException(InvalidArgumentException::class);
		$this->fixture->getResult($json);
	}

	public function testWorkerBadIncomingData()
	{
		$json ='{}';
		$this->expectException(InvalidArgumentException::class);
		$this->fixture->getResult($json);
	}
	
	public function testWorkerNoIncomingData()
	{
		$json ='';
		$this->expectException(InvalidArgumentException::class);
		$this->fixture->getResult($json);
	}
}
