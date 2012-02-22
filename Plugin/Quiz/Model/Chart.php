<?php
/**
 * Logs every payment transaction
 *
 * @package default
 * @author Thiago Colares
 */
class Chart extends QuizAppModel {
    public $name = 'Chart';
    public $useTable = false;

	public $username;
	
	public function get($method, $options = null){
		$res = Cache::read($this->buildCacheName($method,$options),'_cake_array_');
		if($res === false){

			$url = "http://ws.audioscrobbler.com/2.0/?method=chart.$method&api_key=" . 
			Configure::read('LastFM.APIKey') .
			$this->implodeOptions($options);
			
			$xml = simpleXML_load_file($url,"SimpleXMLElement",LIBXML_NOCDATA);
			$xml = $this->simplexml2array($xml);
			// Key, value, duration
			Cache::write($this->buildCacheName($method,$options), $xml, '_cake_array_');

			return $xml;
		} else {
			return $res;
		}
	}
}