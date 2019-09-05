<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class DaemonTest extends TestCase {

    protected $fixture;

	protected function callCurl($post_data)
	{
		$url='http://127.0.0.1:85/test2019/daemon.php';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; SMART_API PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$res = curl_exec($ch);
		return $res;
	}

	public function testDaemon()
	{
		$post_data ='{ "job": {"text": "Привет, мне на <a href=\"test@test.ru\">test@test.ru</a> пришло приглашение встретиться, попить кофе с <strong>10%</strong> содержанием молока за <i>$5</i>, пойдем вместе!",
				"methods": [
					"stripTags", "removeSpaces", "replaceSpacesToEol", "htmlspecialchars", "removeSymbols", "toNumber"
				]
		}}';
		$res=$this->callCurl($post_data);
	    $this->assertEquals('{"text":"10"}', $res );
	}

	public function testDaemon_stripTags()
	{
		$post_data ='{ "job": {"text": "<a href=\"test@test.ru\">test@test.ru</a><strong>10%</strong><i>$5</i>",
				"methods": [ "stripTags" ]
		}}';
	    $this->assertEquals('{"text":"test@test.ru10%$5"}', $this->callCurl($post_data) );
	}

	public function testDaemon_removeSpaces()
	{
		$post_data ='{ "job": {"text": "aaa bbb ccc",
				"methods": [ "removeSpaces"	]
		}}';
	    $this->assertEquals('{"text":"aaabbbccc"}', $this->callCurl($post_data) );
	}

	
	public function testDaemon_replaceSpacesToEol()
	{
		$post_data ='{ "job": {"text": "aaa bbb ccc",
				"methods": [ "replaceSpacesToEol" ]
		}}';
	    $this->assertEquals('{"text":"aaa\nbbb\nccc"}', $this->callCurl($post_data) );
	}


	public function testDaemon_htmlspecialchars()
	{
		$post_data ='{ "job": {"text": "<a href=\'test\'>Test</a>",
				"methods": [ "htmlspecialchars" ]
		}}';
	    $this->assertEquals('{"text":"&lt;a href=&#039;test&#039;&gt;Test&lt;\/a&gt;"}', $this->callCurl($post_data) );
	}

	public function testDaemon_removeSymbols()
	{
		$post_data ='{ "job": {"text": "[.,/!@#$%^&*()]",
				"methods": [ "removeSymbols" ]
		}}';
	    $this->assertEquals('{"text":""}', $this->callCurl($post_data) );
	}

	public function testDaemon_toNumber()
	{
		$post_data ='{ "job": {"text": "aaa10bbb",
				"methods": [ "toNumber" ]
		}}';
	    $this->assertEquals('{"text":"10"}', $this->callCurl($post_data) );
	}

	public function testDaemonFirstNumberFromString()
	{
		$post_data ='{ "job": {"text": "Test 10 and 20 and 30 and 40 and 50",
				"methods": [
					"stripTags", "removeSpaces", "replaceSpacesToEol", "htmlspecialchars", "removeSymbols", "toNumber"
				]
		}}';
	    $this->assertEquals('{"text":"10"}', $this->callCurl($post_data) );
	}
	
	public function testDaemonNoMethods()
	{
		$post_data ='{ "job": {"text": "sometext"} }';
		$res=$this->callCurl($post_data);
		if(strpos($res,'InvalidArgumentException')===false){
			$check=false;
		}else{
			$check=true;
		}
	    $this->assertTrue($check);
	}

	public function testDaemonBadIncomingData()
	{
		$post_data ='{}';
		$res=$this->callCurl($post_data);
		if(strpos($res,'InvalidArgumentException')===false){
			$check=false;
		}else{
			$check=true;
		}
	    $this->assertTrue($check);
	}
	
	public function testDaemonNoIncomingData()
	{
		$post_data ='';
		$res=$this->callCurl($post_data);
		if(strpos($res,'InvalidArgumentException')===false){
			$check=false;
		}else{
			$check=true;
		}
	    $this->assertTrue($check);
	}
	

}
