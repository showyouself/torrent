<?php
define('SOURCE_TYPE_ZHONGZISO', 1);
define('REDIS_EXPIRE_TIME', 300);
define('REDIS_SEARCH_TOTAL_PREFIX','search_kw_total_');
define('REDIS_SEARCH_PREFIX', 'search_kw_');


define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', '6379');

function logger($type, $msg)
{
	\Think\Log::write($msg,$type);
}

//将source数据根据format格式过滤后返回
function filterIssetAndType($format, $souce)
{
	$ret = array();
	if (!is_array($format) OR !is_array($souce))  { return array(); }
	foreach ($format as $k => $v){
		if (is_string($v)) {
			if (isset($souce[$k])) { $ret[$k] = $souce[$k]; }
			else { $ret[$k] = $v; }
		}else if(is_numeric($v)){
			if (isset($souce[$k]) AND is_numeric($souce[$k])) {
				if (is_float($v)) {  $ret[$k] = (float)$souce[$k]; }
				else { $ret[$k] = (int)$souce[$k]; }
			} else {
				if (is_float($v)) {  $ret[$k] = (float)$v; }
				else { $ret[$k] = (int)$v; }
			}
		}else if(is_array($v) OR is_object($v)){
			if (isset($souce[$k]) AND is_array($souce[$k])) {
				if (!empty($v)) { $ret[$k] = filterIssetAndType($v, $souce[$k]); }
				else { $ret[$k] = (array)$souce[$k]; }
			}else { $ret[$k] = $v; }
		}else {
			$ret[$k] = $v;
		}
	}
	return $ret;
}

function checkUpdate($old, $old_name, $new, $new_name, &$update, $enable_empty = false)
{
	if (!isset($old[$old_name]) OR ( empty($new[$new_name]) AND $enable_empty == false) ) { return false; }
	if ($old[$old_name] != $new[$new_name]) { $update[$new_name] = $new[$new_name]; }
	return true;
}

function checkUpdateInt($old, $old_name, $new, $new_name, &$update, $enable_empty = false)
{
	if (!isset($old[$old_name]) OR ( empty($new[$new_name]) AND $enable_empty == false) ) { return false; }
	$new[$new_name] = (int)trim($new[$new_name]);
	if (!is_numeric($new[$new_name])) { return false; }
	if ($old[$old_name] != $new[$new_name]) { $update[$new_name] = $new[$new_name]; }
	return true;
}

function kb2SizeName($size){
	if ($size < 1024) {
		return $size.'KB';
	}else if($size < 1048576) {
		return sprintf("%.2f",$size/1024)."MB";
	}else {
		return sprintf("%.2f",$size/1048576)."GB";
	}

}

function trimall($str)
{
	$str = preg_replace('/\/\*.*\*\//','',$str);
	$qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
	return str_replace($qian,$hou,$str);
}


function page2limit($total_item, $each_page_item, $page = 1)
{
	$page = (int)$page;
	$total_item= (int)$total_item;
	$each_page_item = (int)$each_page_item;

	$p['page'] = $page;
	if (empty($p['page']) OR !is_numeric($p['page'])) { $p['page'] = 1; }

	$p['offset'] = ($page - 1) * $each_page_item;
	if ($p['offset'] >= $total_item) { $p['offset'] = $total_item - $each_page_item; }
	if ($p['offset'] < 0) { $p['offset'] = 0; }
	$p['limit'] = $each_page_item + $page['offset'];

	$total_page = ceil($total_item/$each_page_item);
	$first_page = $page - 3;
	if ($first_page < 1) { $first_page = 1; }
	$end_page = $page + 3;
	if ($end_page > $total_page) { $end_page = $total_page; }

	$p['page_num'] = range($first_page, $end_page);
	$p['page_total'] = range(1, $total_page);

	return $p;
}
