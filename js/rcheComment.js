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


$().ready(function() {
		$('textarea.tinymce').tinymce({
			// Location of TinyMCE script
			script_url : 'js/tiny_mce/tiny_mce.js',
			relative_urls : false,
			// General options
			theme : "advanced",language:"en",
			plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,emotions,image",
			theme_advanced_buttons3 : "",
			theme_advanced_buttons4 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,

			// Example content CSS (should be your site CSS)
			content_css : "",

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js",

			// Replace values for the template plugin
			template_replace_values : {
				username : "Some User",
				staffid : "991234"
			}
		});
	});

$(document).ready(function(e){
	$(".delComment").live("click", function() {
		if (!confirm('You are sure to delete?')) return false;
		var getvalue = $(this).attr('rel');
		var passport = $(this).attr('passport');
		var dataString = 'id=' + getvalue + '&passport=' + passport + '&eventComments=del';
		$.ajax({
			type: "POST",
			url: "",
			data: dataString,
			cache: false,
			success: function(html){
				if(html=='OK')
					{
					$("#itemComment-" + getvalue).remove();
					}
			$("#ajaxComment").html(html);
			}
                 });
	e.preventDefault();
	});

	$(".replyComment").live("click", function() {
		var getvalue = $(this).attr('rel');
		var post_url = $("#posturlComment").val();
		var dataString = 'replyid=' + getvalue + '&eventComments=reply' + '&posturlComment=' + post_url;
		$.ajax({
			type: "POST",
			url: "",
			data: dataString,
			cache: false,
			success: function(html){
				$("#RformComment").fadeOut(2000).remove();
				$("#itemComment-" + getvalue).append(html);

			$('#RformComment textarea.tinymce').tinymce({
				// Location of TinyMCE script
				script_url : 'js/tiny_mce/tiny_mce.js',
				relative_urls : false,
				// General options
				theme : "advanced",language:"en",
				plugins : "autolink,style,save,emotions,paste,xhtmlxtras,template,advlist",
	
				// Theme options
				theme_advanced_buttons1 : "cut,copy,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,emotions,image",
				theme_advanced_buttons2 : "",
				theme_advanced_buttons3 : "",
				theme_advanced_buttons4 : "",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,

				// Example content CSS (should be your site CSS)
				content_css : "",

				// Drop lists for link/image/media/template dialogs
				template_external_list_url : "lists/template_list.js",
				external_link_list_url : "lists/link_list.js",
				external_image_list_url : "lists/image_list.js",
				media_external_list_url : "lists/media_list.js",

				// Replace values for the template plugin
				template_replace_values : {
					username : "Some User",
					staffid : "991234"
				}
				});
			}
                 });
	return false;//e.preventDefault();
	});

	$(".submitComment").live("click", function(e) {

	var name = $("#nameComment").val();
	var email = $("#emailComment").val();
	var addComment = $("#addComment").val();

	var RreplyComment = $("#RreplyComment").val();

	if(typeof name === 'undefined') name='';
	if(typeof email === 'undefined') email='';
	if(typeof RreplyComment === 'undefined') RreplyComment='';

	if(RreplyComment !== "" && RreplyComment !== 0 && RreplyComment !== "0" && RreplyComment !== null &&
		 RreplyComment !== false && typeof RreplyComment !== 'undefined') {
		var loginComment = $("#RloginComment").val();
		var comment = $("#RtextComment").val();
		var post_url = $("#RposturlComment").val();
		var postO_url = $("#RposturlOpenComment").val();
		var persona= $("#RpersonaComment").val();
		var checked = $("#RcheckedComment").val();
		var cap = $("#RnameCommentCap").val();
		var capcha = $("#Rcapcha").val();
		}
		else {		
		var loginComment = $("#loginComment").val();
		var comment = $("#textComment").val();
		var post_url = $("#posturlComment").val();
		var postO_url = $("#posturlOpenComment").val();
		var persona= $("#personaComment").val();
		var checked = $("#checkedComment").val();
		var cap = $("#nameCommentCap").val();
		var capcha = $("#capcha").val();
		}

	var dataString = 'loginComment=' + loginComment + '&addComment=' + addComment + '&personaComment=' 
			+ persona + '&checkedComment=' + checked + '&eventComments=save&nameComment='+ name 
			+ '&emailComment=' + email + '&textComment=' + encodeURIComponent(comment) + '&posturlComment=' 
			+ post_url +'&posturlOpenComment=' + postO_url +'&replyComment=' + RreplyComment + '&nameCommentCap=' + cap
			 + '&capcha=' + capcha;
	if(post_url=='') {alert('Error')};


	if((loginComment==0 && (name=='' || email=='')) || comment==='') 
		{
		alert('Please fill in all fields');
		}
	else
	{
	$("#ajaxComment").show();
	$("#ajaxComment").fadeIn(400).html('<img src="images/comment/ajax-bar.gif" align="absmiddle">&nbsp;<span class="loading"></span>');
	$.ajax({
		type: "POST",
		url: "",
		data: dataString,
		cache: false,
		success: function(html){
			if(html!=='ERR1' && html!=='ERR2' && html!=='ERR3' && html!=='ERR4' && html!=='ERR5') {
        		   if(RreplyComment !== "" && RreplyComment !== 0 && RreplyComment !== "0" && RreplyComment !== null &&
				 RreplyComment !== false && typeof RreplyComment !== 'undefined') {
					$("#RformComment").fadeOut(2000).remove();
					$("#itemComment-" + RreplyComment).append(html);
					$("#ajaxComment").hide();
				}
				else {
					$("#allComment").append(html);
					//$("ol#update li:last").fadeIn("slow");
					$("#nameComment").val("");
					$("#emailComment").val("");
					$('#textComment').text("");
					$("#nameComment").focus();
					$("#ajaxComment").hide();
					}
				}
				else
				{
				$("#messComment").html('');
				$("#messComment").show();
				$("#ajaxComment").hide();
				if(html=='ERR1')$("#messComment").append("Error: Name must consist of more than 3 char<br/>");
				if(html=='ERR2')$("#messComment").append("Error: E-Mail in not valid<br/>");
				if(html=='ERR3')$("#messComment").append("Error: Not text comment<br/>");
				if(html=='ERR4')$("#messComment").append("Error: Error type");
				if(html=='ERR5')$("#messComment").append("Error: Capcha code is not valid");
				$("#messComment").fadeOut(3000);
				}
			}
		});
	}
	e.preventDefault();
	});

});