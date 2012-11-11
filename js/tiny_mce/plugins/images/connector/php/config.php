<?php
session_start();

if($_SESSION['userID']>0)$path=$_SESSION['userID']; else $path='_other';

//Корневая директория сайта
define('DIR_ROOT',	$_SERVER['DOCUMENT_ROOT']);
//Директория с изображениями (относительно корневой)
define('DIR_IMAGES',	'/img/uploads/'.$path.'/');
//Директория с файлами (относительно корневой)
define('DIR_FILES',	'/img/uploads/'.$path.'/files');

if (!is_dir(DIR_FILES)) {
//echo DIR_FILES;
@mkdir('/var/www/detsk/data/www/detskydoctor.ru'.DIR_FILES, 0777, true);}

//Высота и ширина картинки до которой будет сжато исходное изображение и создана ссылка на полную версию
define('WIDTH_TO_LINK', 500);
define('HEIGHT_TO_LINK', 500);

//Атрибуты которые будут присвоены ссылке (для скриптов типа lightbox)
define('CLASS_LINK', 'lightview');
define('REL_LINK', 'lightbox');

date_default_timezone_set('Asia/Yekaterinburg');

?>
