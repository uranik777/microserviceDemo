<?php

class Worker
{
	private $jsonObj;
	private $methods;
	private $jsonResult;
	
    public function __construct()
    {
    	$this->methods = new Methods();
    }

	private function doWork($inJson):string {
		if(gettype($inJson)=='string'){
			$this->jsonObj=json_decode($inJson,true);
		}
		if(gettype($inJson)=='array'){
			$this->jsonObj=$inJson;
		}
		
		// check incoming data
		if( isset($this->jsonObj['job']['text'])==false ){
			throw new InvalidArgumentException("Error Processing Request , no job-text data", 1);
		}
		if( gettype( $this->jsonObj['job']['text'] )!=='string' ){
			throw new InvalidArgumentException("Error Processing Request , no job-text must be string", 1);
		}
		if( isset($this->jsonObj['job']['methods'])==false ){
			throw new InvalidArgumentException("Error Processing Request , no methods", 1);
		}
		if(gettype($this->jsonObj['job']['methods'])!=='array'){
			throw new InvalidArgumentException("Error Processing Request , methods must be array", 1);
		}
		

		$methods=$this->getMethods();
		$tempresult=$this->jsonObj['job']['text'];
		foreach ($methods as $method) {
			$this->callMethod($method,$tempresult);
		}
		return $tempresult;
	}

	private function getMethods():array {
		return $this->jsonObj['job']['methods'];
	}

	private function callMethod(string $method, string &$text){
		if( method_exists( $this->methods , $method) ){
	        $this->methods->$method($text);
		}
	}

	public function getResult($inJson):string {
        $result = $this->doWork($inJson);
        $this->jsonResult = json_encode(array('text' => $result));
		return $this->jsonResult;
	}

}


class Methods
{
	public function stripTags(string &$text){
		$text=strip_tags($text);
	}

	public function removeSpaces(string &$text){
		$text = str_replace(' ', '', $text);
	}

	public function replaceSpacesToEol(string &$text){
		$text = str_replace(' ', "\n", $text);
	}

	public function htmlspecialchars(string &$text){
		$text = htmlspecialchars($text, ENT_QUOTES);
	}

	public function removeSymbols(string &$text){
		$text = preg_replace("/[\[\.,\/!@#$%^&*()\]]/", "", $text);
	}

	public function toNumber(string &$text){
		preg_match_all('!\d+!', $text, $matchesArr);
		if( count($matchesArr[0])>0 ){
			// get only first number from string
			$text=$matchesArr[0][0];
		}
	}

	// next 100+ methods..
}
