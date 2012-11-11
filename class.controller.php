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

abstract class Controller_Base {

        protected $registry;

        final function __construct($registry) {
                $this->registry = $registry;
        }

	function getDate ($arg='d m Y H i') {
	$line=explode(' ',$arg);
	$date=array();
	foreach($line as $l):
		$date[$l]=date($l);
	endforeach;
	return $date;
	}
        abstract function index();
}
?>