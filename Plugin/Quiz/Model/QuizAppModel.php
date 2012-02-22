<?php

class QuizAppModel extends AppModel {

	protected function implodeOptions($options){
		if(isset($options) && !empty($options)){
			$res = '';
			foreach($options as $var => $value){
				$res .= "&$var=$value";
			}
			return $res;
		}
		return '';
	}
	
	/**
	 * Convert SimpleXMLElement object to array
	 * Added a is_object check
	 * Copyleft GPL license
	 *
	 * @param string $xml 
	 * @return void
	 * @author Copyright Daniel FAIVRE 2005 - www.geomaticien.com
	 */
	protected function simplexml2array($xml) {		
	   if (is_object($xml) && get_class($xml) == 'SimpleXMLElement') {
	       $attributes = $xml->attributes();
	       foreach($attributes as $k=>$v) {
	           if ($v) $a[$k] = (string) $v;
	       }
	       $x = $xml;
	       $xml = get_object_vars($xml);
	   }
	   if (is_array($xml)) {
	       if (count($xml) == 0) return (string) $x; // for CDATA
	       foreach($xml as $key=>$value) {
	           $r[$key] = $this->simplexml2array($value);
	       }
	       if (isset($a)) $r['@'] = $a;    // Attributes
	       return $r;
	   }
	   return (string) $xml;
	}

	protected function buildCacheName($method,$options){
		return strtolower($this->name) . '_' . $method . '_' . (isset($options) && !empty($options) ? implode('_',$options) . '_' : '') . $this->username; 
	}
	

}


