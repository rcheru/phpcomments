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


Install:

1. Create table in Data Base, import in DB install.sql
2. Seup connection to DB in file config.php
3. Insert to page code(for output comments):


	<?php
	// At beginning php script
	include("config.php");
	?>

	<?php
	// output comments and form
	$comments->outComments();
	?>

4. For work Ajax comment and output WISIWIG editor. Download and exract this addition package
http://rche.ru/exaples/commentsENG/js.zip


For administarte comments, add in URL, GET var: pass=12345 (password setup in config.php)
Example admin: 
	http://example.com/comments.php?pass=12345
	http://example.com/components/articles/?pass=12345
	http://example.com/?pass=12345


Files description:


/.install.sql  			mySQL dump for install
/capcha.php 			this file is out generate capcha in class.capcha.php
/class.captcha.php 		this class for generate capcha
/class.comments.php 		This class is generate comments form, output comments list. Is primary class in this package
/class.controller.php 		for init registry class
/class.dbsql.php 		class worked with BD MySQL
/class.registry.php 		Registry class. this class is save vars and objects
/config.php 			Configurate class
/functions.php 			Support functions for comments class
/index.php 			Example
/markdown.php 			A text-to-HTML conversion tool for web writers
/markhtml.php 			Clear html cod, delete XSS code
/readme.txt 			Installation instruction
/css/rcheComment.css 		CSS Style for output forms
/js/rcheComment.js 		JavaScript for Ajax and output WISIWIG