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


For administarte comments, add in URL, GET var: pass=12345 (password setup in config.php)
Example admin: 
	http://example.com/comments.php?pass=12345
	http://example.com/components/articles/?pass=12345
	http://example.com/?pass=12345