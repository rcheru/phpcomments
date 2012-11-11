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


function add_pr ($str,$count)
	{
//	$st=preg_replace("/([.,;-])/","\${".$count."}?",$str);
	$str=iconv('UTF-8','WINDOWS-1251',$str);
	$i = 0;$no_pr = 0;$j = 1;
	while ($i < strlen($str))
		{
		$text[$j] = $text[$j].$str[$i];
		if ($str[$i] == ' '){$no_pr = 0;$j = $j+1;}
		if ($str[$i] != ' '){$no_pr = $no_pr+1;}
		if ($no_pr == $count){$text[$j] = $text[$j].' ';$no_pr = 0;}
		$i = $i+1;
		}
	while ($j != 0){$st = $st.$text[$j];$j = $j-1;}
	$st=iconv('WINDOWS-1251','UTF-8',$st);
	return $st;
	}

function PHP_slashes($string,$type='add')
{
    if ($type == 'add')
    {
        if (get_magic_quotes_gpc())
        {
            return $string;
        }
        else
        {
            if (function_exists('addslashes'))
            {
                return addslashes($string);
            }
            else
            {
                return mysql_real_escape_string($string);
            }
        }
    }
    else if ($type == 'strip')
    {
        return stripslashes($string);
    }
    else
    {
        die('error in PHP_slashes (mixed,add | strip)');
    }
}
if(!function_exists('utf8_strlen'))
	{
	function utf8_strlen($s)
		{
		return preg_match_all('/./u', $s, $tmp);
		}
	}

if(!function_exists('utf8_substr'))
	{
	function utf8_substr($s, $offset, $len = 'all')
		{
		if ($offset<0) $offset = utf8_strlen($s) + $offset;
		if ($len!='all')
			{
			if ($len<0) $len = utf8_strlen2($s) - $offset + $len;
			$xlen = utf8_strlen($s) - $offset;
			$len = ($len>$xlen) ? $xlen : $len;
			preg_match('/^.{' . $offset . '}(.{0,'.$len.'})/us', $s, $tmp);
			}
			else
			{
			preg_match('/^.{' . $offset . '}(.*)/us', $s, $tmp);
			}
		return (isset($tmp[1])) ? $tmp[1] : false;
		}
	}
if(!function_exists('utf8_strpos'))
	{
function utf8_strpos($str, $needle, $offset = null)
      {
          if (is_null($offset))
          {
              return mb_strpos($str, $needle);
          }
          else
          {
              return mb_strpos($str, $needle, $offset);
          }
      }
}
function getAllcache($sql, $time=600, $filename='') {
	global $DB, $system_query_cache;
	if(!$system_query_cache)$time=0;
	$crc=md5($sql); 
	if(!empty($filename))$crc=$filename;
	$modif=time()-@filemtime ("cache/".$crc);
	if ($modif<$time)
		{
		$cache=file_get_contents("cache/".$crc);
		$cache=unserialize($cache);
		}
		else 
		{
		$cache = $DB->getAll($sql);
		$fp = @fopen ("cache/".$crc, "w");
		@fwrite ($fp, serialize($cache));
		@fclose ($fp); 
		}
        return $cache;
}
function getOnecache($sql, $time=600,$filename='') {
	global $DB, $system_query_cache;
	if(!$system_query_cache)$time=0;
	$crc=md5($sql); 
	if(!empty($filename))$crc=$filename;
	$modif=time()-@filemtime ("cache/".$crc);
	if ($modif<$time)
		{
		$cache=file_get_contents("cache/".$crc);
		$cache=unserialize($cache);
		}
		else 
		{
		$cache = $DB->getOne($sql);
		$fp = @fopen ("cache/".$crc, "w");
		@fwrite ($fp, serialize($cache));
		@fclose ($fp); 
		}
        return $cache;
}
function email_check($email) {
	if (!preg_match("/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i",trim($email)))
		{
		 return false;
		}
		else return true;
	}
function isIP($ip) 
	{
	return (bool)(ip2long($ip)>0);
	}; 
function getIP() {
   if(isset($_SERVER['HTTP_X_REAL_IP'])) return $_SERVER['HTTP_X_REAL_IP'];
   return $_SERVER['REMOTE_ADDR'];
}
