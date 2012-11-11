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

class Comments extends Controller_Base {

	var $path	= ''; // path to page on comments
	var $table	= 'comments'; // table comments
	var $prefix	= 'rche_'; // prefix table comments
	var $event	= '';
	var $key	= 'e34d9147f42016a32a9bab982492323547e121ce'; // sicret key for ajax
	var $login	= false; // login user or email and name
	var $user	= array(); // user info if login
	var $admin	= false; // admin option
	var $gravatar	= false; // avatar from gravatar.com
	var $capcha	= true; // enable Capcha
	var $paths	= array(); // path's

function index () {
		$this->event=@$_POST['eventComments'];
		if(@$_GET['eventComments']=='del' and @$_GET['noajax']==1)$this->event=@$_GET['eventComments'];

		if($this->event=='save') $status=$this->saveComments();
		if($this->event=='del') $status=$this->delComments();
		if($this->event=='edit') $status=$this->editComments();
		if($this->event=='reply') $status=$this->replyComments();
		if($this->event=='')$status=NULL;
		return $status;
	} 

function delComments() {
		$id	= intval($_POST['id']);
		$passport =$_POST['passport'];
		if($_GET['noajax']==1)
		{
		$id	= intval($_GET['id']);
		$passport =$_GET['passport'];
		}
		if($passport==md5($this->key.'admin')) 
			{

			$sql="SELECT {$this->prefix}{$this->table}.id, {$this->prefix}{$this->table}.reply FROM {$this->prefix}{$this->table} 
			WHERE {$this->prefix}{$this->table}.url='".$this->getUrl()."' ORDER BY {$this->prefix}{$this->table}.id ASC";
			$allComm=$this->registry['DB']->getAll($sql);
			if(count($allComm)>0):
			// subcomments
			foreach($allComm as $item):
				if($item['reply']==0)$sortcomm[$item['id']]=$item;
				if($item['reply']>0)
					{
					if(isset($path[$item['reply']]))
						{
						$str='$sortcomm';
						foreach($path[$item['reply']] as $pitem):
							$rep=$item['reply'];
							$str.="[$pitem][sub]";
						endforeach;
						$str.="[{$item['reply']}][sub]";

						$str.="[{$item['id']}]";
		                                $str.='=$item;';

						eval($str);

						foreach($path[$item['reply']] as $pitem):
							$path[$item['id']][]=$pitem;
						endforeach;

						$path[$item['id']][]=$item['reply'];
						}
						else
						{
						$sortcomm[$item['reply']]['sub'][$item['id']]=$item;
						$path[$item['id']][]=$item['reply'];
						}
					}
		        endforeach;
		        endif;

			$this->tree_delComment($sortcomm,$id);

			if($_GET['noajax']==1):
			$url=explode('?',$_SERVER['REQUEST_URI']);
			header('Location: http://'.$_SERVER['HTTP_HOST'].$url[0]);
			else: echo 'OK'; endif;
			}
	if(empty($_GET['noajax']))exit;
	}

function tree_delComment(&$a_tree,&$id=0) {
	if(count($a_tree)>0)
	foreach($a_tree as $sub)
		{
		if($sub['id']<>$id and isset($sub['sub']))$this->tree_delComment($sub['sub'],$id);
		if($sub['id']==$id)
			{
			$sql="DELETE FROM {$this->prefix}{$this->table} WHERE id = '$id' LIMIT 1";
			$this->registry['DB']->execute($sql);
			if (isset($sub['sub'])) $this->tree_delComment_process($sub['sub']);
			}
		}
	}

function tree_delComment_process(&$a_tree) {
	foreach($a_tree as $sub)
		{
		$sql="DELETE FROM {$this->prefix}{$this->table} WHERE id = '{$sub['id']}' LIMIT 1";
		$this->registry['DB']->execute($sql);
		if(isset($sub['sub']))$this->tree_delComment_process($sub['sub']);
		}
	}

function replyComments() {
	$replyid	= intval($_POST['replyid']);
	$pass_checked=md5($this->user['password'].$this->key);
	$post_url = htmlspecialchars(trim($_POST['posturlComment']));
	if(strlen($post_url)>50 or strlen($post_url)<10) return;
	$url		= $post_url;
	$urlOpen=$this->getUrl(false, 'open');

	if($this->capcha)
		{
		$capcha ='
		<br/>Input number from image: <input type="text" name="capcha" id="Rcapcha" value="" class="inputComment"/><br/>
		<img src="'.$this->paths['capcha'].'" alt="image" width="120" height="50"/>
		<br/>';
		}

	$form = '
	<form action="" method="post" id="RformComment">
		Name: <input name="nameComment" id="nameComment" value="" type="text" class="inputComment">
		<input name="nameCommentCap" id="RnameCommentCap" value="" type="text">
		E-mail: <input name="emailComment" id="emailComment" value="" type="text" class="inputComment">

		<input name="replyComment" id="RreplyComment" value="'.$replyid.'" type="hidden">
		<input name="loginComment" id="RloginComment" value="'.intval($this->login).'" type="hidden">
		<input name="posturlComment" id="RposturlComment" value="'.$url.'" type="hidden">
		<input name="personaComment" id="RpersonaComment" value="'.$this->user['userID'].'" type="hidden">
		<input name="checkedComment" id="RcheckedComment" value="'.$pass_checked.'" type="hidden">
		<input name="posturlOpenComment" id="RposturlOpenComment" value="'.$urlOpen.'" type="hidden">
		<input name="eventComments" id="ReventComment" value="save" type="hidden">
		<input name="noAjax" value="1" type="hidden">
		<textarea name="textComment" id="RtextComment" class="textareaComment tinymce"></textarea>
		'.$capcha.'
		<input value="Send" name="submit" type="submit" class="submitComment"/>
	</form>';
	echo $form;
	exit;
	}

function itemComments($username,$date,$text,$img,$id,$autor=false, $userid='') {
	$possport=md5($this->key.'admin');
//	if($this->login) 
	$reply='<a href="javascript://" rel="'.$id.'" class="replyComment" title="Ответить на комментарий: '.$username.'">Ответить</a>';
//	if($autor or $this->admin)$edit=' | <a href="javascript://" rel="'.$id.'" class="editComment" title="Редактировать комметарий">Редактировать</a>';
	if($this->admin) $del=' | <a href="?id='.$id.'&passport='.$possport.'&noajax=1&eventComments=del" onclick="return false" rel="'.$id.'" passport="'.$possport.'" class="delComment" title="Удалить комментарий">Удалить</a>';
	
	if($userid>0)$uslink="/com/profile/default/$userid/";else $uslink='#itemComment-'.$id;

	$out='<div class="itemComment" id="itemComment-'.$id.'">
		<div class="avatarComment">
			<a href="'.$uslink.'" title="See user profile: '.$username.'">
				<img src="'.$img.'" width="48" height="48" border="0" alt="User avatar: '.$username.'"/></a>
			</div>
		<div class="panelComment">
			<a class="userComment" href="'.$uslink.'" title="See user progile: '.$username.'">'.$username.'</a>
			<span class="dateComment" title="date, time comment">'.$date.'</span>
		</div>
		<div class="bodyComment">
			'.$text.'
		</div>
		<div class="footerComment">
			
			'.$reply.$edit.$del.'
		</div>
	';
	return $out;
	}

function outComments() {
	echo '<div id="rcheComments">
	<h3 class="titleComment">Comments</h3>
	<div id="allComment">';
	$sql="SELECT {$this->prefix}{$this->table}.*, rche_users.photo, rche_users.username, rche_users.userID FROM {$this->prefix}{$this->table} 
		LEFT JOIN rche_users ON {$this->prefix}{$this->table}.user =rche_users.userID
		WHERE {$this->prefix}{$this->table} .url='".$this->getUrl()."' ORDER BY {$this->prefix}{$this->table}.id ASC";
	$allComm=$this->registry['DB']->getAll($sql);

	if(count($allComm)>0):
	// subcomments
	foreach($allComm as $item):
		if($item['reply']==0)$sortcomm[$item['id']]=$item;
		if($item['reply']>0)
			{
			if(isset($path[$item['reply']]))
				{
				$str='$sortcomm';
				foreach($path[$item['reply']] as $pitem):
					$rep=$item['reply'];
					$str.="[$pitem][sub]";
				endforeach;
				$str.="[{$item['reply']}][sub]";

				$str.="[{$item['id']}]";
                                $str.='=$item;';

				eval($str);

				foreach($path[$item['reply']] as $pitem):
					$path[$item['id']][]=$pitem;
				endforeach;

				$path[$item['id']][]=$item['reply'];
				}
				else
				{
				$sortcomm[$item['reply']]['sub'][$item['id']]=$item;
				$path[$item['id']][]=$item['reply'];
				}
			}
        endforeach;
	$this->tree_print($sortcomm);

	else: echo '<p>No comments</p>'; endif;


	echo '</div>
	<div id="messComment"></div>
	<div id="ajaxComment"></div>';

	echo $this->pageComment();
	echo $this->formComment();
	}

function tree_print(&$a_tree) {
	foreach($a_tree as $sub)
		{
		$this->outItem($sub);
		if(!empty($sub['sub']))$this->tree_print($sub['sub']);
		echo "</div>";
		}
	}

function outItem($item) {
        $autor=false;
	if(intval($item['user'])==0)
		{
		if($this->gravatar)
			{
			$lowercase = strtolower($item['email']);
			$image = md5( $lowercase );
			$img="http://www.gravatar.com/avatar.php?gravatar_id=$image";
			} else $img='images/boy48.gif';
		}
		else 
		{
		$img=$item['photo'];
		$im=explode('/',$img);
                $img='/images/'.$item['user'].'/48/48/1/'.$im['4'];
		$item['name']=$item['username'];
		}
	if($item['pass']==$_COOKIE['comment'.$item['id']] and !empty($item['pass']) and ($item['date']+120)>time()) $autor=true;

	echo $this->itemComments(
		$item['name'],
		$this->get_Date($item['date']),
		html_entity_decode($item['comment']),
		$img,
		$item['id'],
		$autor,
		$item['userID']);
	}

function saveComments() {
	$name 	= trim(strip_tags($_POST['nameComment']));
	$email 	= trim($_POST['emailComment']);
	$text 	= PHP_slashes(htmlspecialchars(markhtml(trim(rawurldecode($_POST['textComment'])))));
	$post_url = htmlspecialchars(trim($_POST['posturlComment']));
	$urlOpen = htmlspecialchars(trim($_POST['posturlOpenComment']));
        $error	= false;
	$login = intval($_POST['loginComment']);
	$replyComment = intval($_POST['replyComment']);
	$cap=$_POST['nameCommentCap'];

	if($this->capcha) {
		if($_SESSION['captha_text']!=$_POST['capcha']) {
			echo 'ERR5';
			exit;
	         	}
		}
	if($login==1)
		{
		$persona= intval($_POST['personaComment']);
		$checked= htmlspecialchars(trim($_POST['checkedComment']));
		if($persona>0 and $checked>'')
			{
			$sql="SELECT rche_users.* FROM rche_users
				WHERE rche_users.userID='$persona' LIMIT 1";
			$user=$this->registry['DB']->getAll($sql);
			if(md5($user[0]['password'].$this->key)==$checked)
				{
				$this->login=true;
				$this->user=$user[0];
				}
			}
			else 
			{
			echo 'ERR4';
			exit;
			}
		}

	if(!$this->login)
		{
		if(strlen($name)<3) {$error=true;$msg=1;}
		if(!$this->emailCheck($email) or strlen($name)>100){$error=true;$msg=2;}
		$img='images/boy48.gif';
		}
		else 
		{
		$img=$this->user['photo'];
		$im=explode('/',$img);
                $img='/images/'.$this->user['userID'].'/48/48/1/'.$im['4'];
		$name=$this->user['username'];
		$user=$this->user['userid'];
		}
	if(strlen($text)==0){$error=true;$msg=3;}
	if(strlen($post_url)>50 or strlen($post_url)<10){$error=true;$msg=4;}

	if($error)
		{
		echo 'ERR'.$msg;
		exit;
		}
	
	$pass=$this->generate_password(8);

	$date=$this->get_Date();
	$time=time();
	if($cap=='') {
		$sql="INSERT INTO {$this->prefix}{$this->table} (`reply`,`user`,`name`,`email`,`comment`,`date`,`url`,`pass`,`urlOpen`)
			VALUE ('$replyComment','{$this->user['userID']}','$name','$email','$text','$time','$post_url','$pass','$urlOpen')";
		$this->registry['DB']->execute($sql);
		}
	$lastId=$this->registry['DB']->id;
	setcookie('comment'.$lastId,$pass,$time+120,'/');

	if(intval($_POST['noAjax'])<>1):
	echo $this->itemComments(
		$name,
		$date,
		html_entity_decode($text),
		$img,
		$lastId,
		true,
		$user);

		exit;
	endif;
	}
function formComment($replyid=0)
	{
	global $user;

	if($this->login)
		{
		$pass_checked=md5($this->user['password'].$this->key);
		}
		else 
		{
		$name='
		<tr><td class="section-one">Name</td><td>
		<input name="nameComment" id="nameComment" value="" type="text" class="inputComment">
		<input name="nameCommentCap" id="nameCommentCap" value="" type="text" class="nameCommentCap">
		</td></tr>
		<tr><td class="section-one">E-mail</td><td><input name="emailComment" id="emailComment" value="" type="text" class="inputComment"></td></tr>';
		}

	$url=$this->getUrl();
	$urlOpen=$this->getUrl(false, 'open');

	if($this->capcha)
		{
		$capcha ='
		<tr><td class="section-one">Insert number from image:<br/><img src="'.$this->paths['capcha'].'" alt="картинка" width="120" height="50"/></td><td>
		<input type="text" name="capcha" id="capcha" value="" class="inputComment"/></td></tr>';
		}
	
	$form = '<h3 id="newComment">Send comment </h3>
	<form action="" method="post" id="formComment">
		<input name="addComment" id="addComment" value="1" type="hidden">
		<input name="loginComment" id="loginComment" value="'.intval($this->login).'" type="hidden">
		<input name="posturlComment" id="posturlComment" value="'.$url.'" type="hidden">
		<input name="posturlOpenComment" id="posturlOpenComment" value="'.$urlOpen.'" type="hidden">
		<input name="personaComment" id="personaComment" value="'.@$this->user['userID'].'" type="hidden">
		<input name="checkedComment" id="checkedComment" value="'.@$pass_checked.'" type="hidden">
		<input name="eventComments" id="eventComment" value="save" type="hidden">
		<input name="noAjax" value="1" type="hidden">
		<table id="tableComment">
		'.$name.'
		<tr><td class="section-one">Text comment</td><td><textarea name="textComment" id="textComment" class="textareaComment tinymce"></textarea></td></tr>
		'.$capcha.'
		</table>
		<input value="Send" name="submit" type="submit" class="submitComment"/>
	</form>';
	$form.='</div><p><a href="http://rche.ru" style="font:11px tahoma;color:#999;text-decoration:none">&copy; www.rche.ru</a></p>';
	return $form;
	}

function pageComment() {
	//return $out;
	}

function getUrl($explode=false, $open = '') {
		$url=$_SERVER["REQUEST_URI"];
		if($this->admin==true) {
		   $u=explode('?',$url);
		   $e=explode('&',$u[1]);
		   $i=0;
		   foreach($e as $item)
			{
			$i++;
			$data=explode('=',$item);
			if($data[0]=='pass') continue;
			$newQuery.=$item;
			if($i<count($e))$newQuery.='&';
			$newQuery='?'.$newQuery;
			if(substr($newQuery, -1)=='&')$newQuery=substr($newQuery, 0, strlen($newQuery)-1);
			}
		   $url="{$u[0]}{$newQuery}";
		}
		if($explode)
			{
			$url=explode('?',$_SERVER['REQUEST_URI']);
			$url=$url[0];
			}
		if($open=='open')return urlencode($url);
		return md5($url);
	}

function emailCheck($email) {
	   if (!preg_match("/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i",trim($email)))
		{
		 return false;
		}
		else return true;
	}

function get_Date($shtamp='') {
		if($shtamp=='')$shtamp=time();
		$date 	= date('d.m:Y H:i',$shtamp);
		return $date;
	}

	function generate_password($number)
	  {
	    $arr = array('a','b','c','d','e','f',
                 'g','h','i','j','k','l',
                 'm','n','o','p','r','s',
                 't','u','v','x','y','z',
                 'A','B','C','D','E','F',
                 'G','H','I','J','K','L',
                 'M','N','O','P','R','S',
                 'T','U','V','X','Y','Z',
                 '1','2','3','4','5','6',
                 '7','8','9','0'); /*,'.',',',
                 '(',')','[',']','!','?',
                 '&','^','%','@','*','$',
                 '<','>','/','|','+','-',
                 '{','}','`','~');*/
	    $pass = "";
	    for($i = 0; $i < $number; $i++)
	    {   	
	      $index = rand(0, count($arr) - 1);
	      $pass .= $arr[$index];
	    }
	    return $pass;
	  }

}
