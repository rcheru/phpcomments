<?php
/*
 *
 *    PHP script comments.
 *    Version: 1.0 (beta)
 *    Late: 06.11.2012
 *    Autor: Chernyshov Roman
 *    Site: http://rche.ru
 *    E-mail: houseprog@ya.ru
 *
 */

defined('_RCHE') or die('Restricted access');

class Registry implements ArrayAccess{
        private $vars = array();

	function set($key, $var) {
	        if (isset($this->vars[$key]) == true) {
                throw new Exception('Unable to set var `' . $key . '`. Already set.');
        	}
        $this->vars[$key] = $var;
        return true;
	}

	function get($key) {
	        if (isset($this->vars[$key]) == false) {
                return null;
	        }
        return $this->vars[$key];
	}

	function remove($var) {
	        unset($this->vars[$key]);
		}
	function offsetExists($offset) {
	        return isset($this->vars[$offset]);
	}
	function offsetGet($offset) {
	        return $this->get($offset);
	}
	function offsetSet($offset, $value) {
	        $this->set($offset, $value);
	}
	function offsetUnset($offset) {
	        unset($this->vars[$offset]);
	}
}
