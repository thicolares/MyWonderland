<?php
/**
 * Logs every payment transaction
 *
 * @package default
 * @author Thiago Colares
 */
class Artist extends QuizAppModel {
    public $name = 'Artist';
    public $useTable = false;

	public $artist;
	
	public function get($method, $options = null){

		$options = !is_array($options)  ? array() : $options;
		$options = array_merge($options,array('artist' =>  Inflector::slug($this->artist, '_' )));
		
		
		$res = Cache::read($this->buildCacheName($method,$options),'_cake_array_');
		if($res === false){

			$url = "http://ws.audioscrobbler.com/2.0/?method=artist.$method&artist=$this->artist&api_key=" . 
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