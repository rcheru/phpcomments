<?php
/**
 * MarkHtml v1.0
 * =============
 *
 * Чистка HTML кода от зловредного для сайта кода.
 * Убирает известный XSS код, запрещает использовать стили, которые могут
 * повредить внешнему виду сайта.
 *
 * Используется внешний Markdown для форматирования текста.
 * 
 * Код основан на [html_filter](http://savvateev.org/blog/36/)
 *
 * Использование:
 * include ('markhtml.php');
 * echo markhtml($text);
 * echo markhtml($text, true); // XHTML
 * 
 * @license: LGPL
 * @date: 2011/04/05
 * @author: Vladimir Romanovich <ibnteo@gmail.com>
 */


function markhtml($text, $xhtml = false) {

    // MarkHtml
    $markhtml = new MarkHtml($text, $xhtml);

    $markhtml->markdown();
    $markhtml->filter();

    return $markhtml->text;
}

include (dirname(__FILE__).'/markdown.php');
class MarkHtml {

    public $tags = array();
    public $tags_closed = array();
    public $tags_attr = array();
    public $xhtml = false;
    public $text = '';

    public function markdown() {
        $this->text = Markdown($this->text);
    }

    public function MarkHtml($text = '', $xhtml = false) {
        $this->text = $text;
        $this->xhtml = $xhtml;
        // Одиночны теги
        $this->tags_closed = array('img', 'br', 'hr', 'param', 'input', 'area', 'col', 'isindex');
        // Запрещенные теги
        $this->tags = array('script', 'meta', 'link', 'style', 'iframe', 'frameset', 'frame', 'layer', 'xml', 'base', 'bgsound', 'basefont', 'body', 'html', 'head', 'title');
        // Запрещенные атрибуты
        $this->tags_attr = array(
            'style' => '\\(|\\\\|position:|margin',
            '.*' => 's\s*c\s*r\s*i\s*p\s*t\s*:',
            '^on' => '',
            'src' => 'm\s*h\s*t\s*m\s*l\s*:',
            'type' => 'scriptlet',
            'allowscriptaccess' => 'always|samedomain'
        );
    }

    // Фильтрация HTML
    public function filter() {
        
        $open_tags_stack = array();
        $code = false;
        $link = false;

        // Разбиваем полученный код на учатки простого текста и теги
        $seg = array();
        while(preg_match('/<[^<>]+>/siu', $this->text, $matches, PREG_OFFSET_CAPTURE)){
            if ($matches[0][1]) $seg[] = array('seg_type'=>'text', 'value'=>substr($this->text, 0, $matches[0][1]));  
            $seg[] = array('seg_type'=>'tag', 'value'=>$matches[0][0]);
            $this->text = substr($this->text, $matches[0][1]+strlen($matches[0][0]));
        }
        if ($this->text != '') $seg[] = array('seg_type'=>'text', 'value'=>$this->text);

        // Обрабатываем полученные участки
        for ($i=0; $i<count($seg); $i++) {
            //Если участок является простым текстом экранируем в нем спец. символы HTML
            if ($seg[$i]['seg_type'] == 'text') {
                // Мягко убираем лишние &amp;
                $seg[$i]['value'] = preg_replace('/&amp;([a-z#0-9]+;)/ui', '&$1', htmlentities($seg[$i]['value'], ENT_QUOTES, 'UTF-8'));
            // Тег
            } elseif ($seg[$i]['seg_type'] == 'tag') {
            
                // Тип тега: открывающий/закрывающий, имя тега, строка атрибутов
                preg_match('#^<\s*(/)?\s*([a-z]+:)?([a-z0-9]+)(.*?)>$#siu', $seg[$i]['value'], $matches);
                if (count($matches)==0) {
                    $seg[$i]['seg_type']='text';
                    $i --;
                    continue;
                } elseif ($matches[1]) {
                    $seg[$i]['tag_type']='close';
                } else {
                    $seg[$i]['tag_type']='open';
                }
                if ($seg[$i]['tag_type'] != 'text') {
                    $seg[$i]['tag_ns'] = $matches[2];
                    $seg[$i]['tag_name'] = $matches[3];
                    $seg[$i]['tag_name_lc'] = strtolower($matches[3]);
                }
                
                if (($seg[$i]['tag_name_lc']=='code') and ($seg[$i]['tag_type']=='close')) {
                    $code = false;
                }
                if (($seg[$i]['tag_name_lc']=='a') and ($seg[$i]['tag_type']=='close')) {
                    $link = false;
                }
                
                // Тег внутри <code> превращаем в текст
                if ($code) {
                    $seg[$i]['seg_type'] = 'text';
                    $i--;
                    continue;
                }

                // Открывающий тег
                if ($seg[$i]['tag_type'] == 'open') {

                    // Недопустимый тег показываем как текст
                    if (array_search($seg[$i]['tag_name_lc'], $this->tags) !== false) {
                        $seg[$i]['action'] = 'show';
                    }
                    // Допустимый тег
                    else {
                        if ($seg[$i]['tag_name_lc'] == 'code') $code = true;
                        if ($seg[$i]['tag_name_lc'] == 'a') $link = true;
                        
                        // Если тег не одиночный, записываем его в стек открывающих тегов
                        if (array_search($seg[$i]['tag_name_lc'], $this->tags_closed) === false) {
                            array_push($open_tags_stack, $seg[$i]['tag_ns'].$seg[$i]['tag_name']);
                        }
                    }

                    // Обработка атрибутов
                    preg_match_all('#([a-z]+:)?([a-z]+)(\s*=\s*[\"]\s*(.*?)\s*[\"])?(\s*=\s*[\']\s*(.*?)\s*[\'])?(=([^\s>]*))?#siu', $matches[4], $attr_m, PREG_SET_ORDER);
                    $attr = array();
                    foreach($attr_m as $arr) {
                        $attr_ns = $arr[1];
                        $attr_key = $arr[2];
                        $attr_val = $arr[count($arr)-1];
                        $is_attr = true;
                        if (!(isset($seg[$i]['action']) and $seg[$i]['action'] == 'show')) {
                            // Поиск неправильных атрибутов
                            foreach ($this->tags_attr as $key=>$val) {
                                if (preg_match('/'.$key.'/ui', $attr_key)) {
                                    if ($val == '' or preg_match('/'.$val.'/ui', html_entity_decode($attr_val, ENT_QUOTES, 'UTF-8'))) {
                                        $is_attr = false;
                                        break;
                                    }
                                }
                            }
                        }
                        if ($is_attr) {
                            $attr[$attr_ns.$attr_key] = $attr_val;
                        }
                    }
                    $seg[$i]['attr'] = $attr;
     
                }
                
                // Закрывающий тег
                else {
                    // Недопустимый закрывающий тег
                    if (array_search($seg[$i]['tag_name_lc'], $this->tags) !== false) {
                        // Удаляем лишний, показываем запрещенный
                        $seg[$i]['action'] = (array_search($seg[$i]['tag_name_lc'], $this->tags_closed) !== false) ? 'del' : 'show';
                    }
                    // Закрывают одиночный тег
                    elseif (array_search($seg[$i]['tag_name_lc'], $this->tags_closed) !== false) {
                        $seg[$i]['action'] = 'del';
                    }
                    // Допустимый закрывающий тег
                    else {
                        
                        if ($seg[$i]['tag_name_lc'] == 'code') $code = false;
                        if ($seg[$i]['tag_name_lc'] == 'a') $link = false;
                        
                        // Стек открывающих тегов пуст
                        if (count($open_tags_stack) == 0) {
                            $seg[$i]['action'] = 'del';
                        }
                        else {
        
                            // Закрывающий тег не соответствует открывающему, добавляем закрывающий
                            $tn = array_pop($open_tags_stack);
                            if ($seg[$i]['tag_ns'].$seg[$i]['tag_name'] != $tn) {
                                array_splice($seg, $i, 0, array(array('seg_type'=>'tag', 'tag_type'=>'close', 'tag_name'=>$tn, 'action'=>'add')));  
                            }   
                        }
                    }
                }
            }
        }
                                                                   
        // Закрываем оставшиеся в стеке теги
        foreach (array_reverse($open_tags_stack) as $value) {
            array_push($seg, array('seg_type'=>'tag', 'tag_type'=>'close', 'tag_name'=>$value, 'action'=>'add'));
        }
        
        // Собираем профильтрованный код и возвращаем его
        $this->text = '';
        foreach ($seg as $segment) {
            if ($segment['seg_type'] == 'text') $this->text .= $segment['value'];
            
            elseif (($segment['seg_type'] == 'tag') and !(isset($segment['action']) and $segment['action'] == 'del')) {
                // Тег будет показан, или выведен как был
                if ((isset($segment['action']) and $segment['action'] == 'show')) {
                    $st = '&lt;';
                    $et = '&gt;';
                } else {
                    $st = '<';
                    $et = '>';
                }
                // Открывающий тег
                if ($segment['tag_type'] == 'open') {
                    $this->text .= $st.$segment['tag_ns'].$segment['tag_name'];
                    if (isset($segment['attr']) and is_array($segment['attr'])) {
                        foreach ($segment['attr'] as $attr_key=>$attr_val) {
                            // Убираем лишние &amp;
                            $attr_val = preg_replace('/&amp;([a-z#0-9]+;)/ui', '&$1', htmlentities($attr_val, ENT_QUOTES, 'UTF-8'));
                            $this->text .= ' '.$attr_key.(($this->xhtml or $attr_key != $attr_val) ? '="'.$attr_val.'"' : ''); 
                        }
                    }
                    // Закрыть одиночный тег
                    if ($this->xhtml and array_search($segment['tag_name'], $this->tags_closed) !== false) $this->text .= " /";
                    $this->text .= $et;
                }
                // Закрывающий тег
                elseif ($segment['tag_type'] == 'close') {
                    $this->text .= $st.'/'.(isset($segment['tag_ns'])?$segment['tag_ns']:'').$segment['tag_name'].$et;
                }
            }
        }
    }           
};
