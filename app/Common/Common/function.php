<?php
define('SOURCE_TYPE_ZHONGZISO', 1);
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
