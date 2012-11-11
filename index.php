<?include("config.php");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="ru-RU">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Comments on PHP, jQuery, Ajax, mySQL - rche.ru</title>
<script type="text/javascript" src="js/jquery-1.4.3.min.js"></script>
<!-- Load TinyMCE -->
<script type="text/javascript" 
	src="js/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript" 
	src="js/rcheComment.js"></script>
<link href="css/rcheComment.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?$comments->outComments();?>
</body>
</html>