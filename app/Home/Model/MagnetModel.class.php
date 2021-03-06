<?php
namespace Home\Model;

use Think\Model;

use Org\WeiXin\Encrypt;
class MagnetModel extends BaseModel
{
	const ZHONGZISO_TAGS = "ZhongzisoProcess_tags";
	public function __construct()
	{
		parent::__construct();
		$this->magnet_tbl = M('magnet');
	}

	public function syncMagnet($new, &$ret)
	{
		if (empty($new['hash_value']) OR empty($new['title']) OR preg_match('/^[a-zA-Z0-9]$/i', $new['hash_value'])) {
			$ret['msg'] = "hash_value，title不能为空";
			$ret['err'] = 100;
			logger("ERR", print_r($ret, true));
			return false;
		}

                $new['hash_value'] = strtolower($new['hash_value']);

		$old = $this->tryGetMagnet($new);
		$update = array();
		if (!$old) {
			$ret['msg'] = "tryGetMagnet失败";
			$ret['err'] = 200;
			logger("ERR", print_r($ret, true));
			return false;
		}

		checkUpdateInt($old, 'create_time', $new, 'create_time', $update);
		checkUpdateInt($old, 'file_size', $new, 'file_size', $update);
		checkUpdateInt($old, 'file_count', $new, 'file_count', $update);
		checkUpdateInt($old, 'source_id', $new, 'source_id', $update);
		checkUpdateInt($old, 'source_type', $new, 'source_type', $update);
		checkUpdate($old, 'tags', $new, 'tags', $update);


		if (isset($new['file_list'])) {
			D('file_list')->updateFileList($old['id'], $new['file_list']);
		}

		if (empty($update)) { 
			$ret['msg'] = "更新的数据为空";
			logger("ERR", "更新的数据为空".print_r($new, true));
			return false; 
		}
		$tmp = $this->updateMagnetByid($old['id'], $this->encode($update));

		if (isset($update['tags'])) { D('tags')->pushNewTags($update['tags']); }

		return $tmp;
	}


	public function tryGetMagnet($m)
	{
		$old = $this->getMagnetByHash($m['hash_value']);
		if (empty($old)) {
			if (!$this->magnet_tbl->data(array('hash_value' => $m['hash_value'], 'title' => $m['title']))->add()) 
			{ 
				logger("ERR", "插入数据失败".print_r($m, true)); 
				return false;
			}
		}
		return $this->getMagnetByHash($m['hash_value']); 
	}

	public function getMagnetByHash($hash)
	{
		$ret = $this->magnet_tbl->where(array('hash_value' => $hash))->find();
		if (!empty($ret)) { return $this->decode($ret); }
		return $ret;
	}

	public function getMagnetById($id)
	{
		$ret = $this->magnet_tbl->where(array('id' => $id))->find();
		if (!empty($ret)) { return $this->decode($ret); }
		return $ret;
	}

	public function updateMagnetByid($id, $data)
	{
		return $this->magnet_tbl->where(array('id' => $id))->save($data);
	}

	public function searchByKw($kw, &$ret, $lim = NULL)
	{
		if (empty($kw)) {
			$ret['msg'] = "empty kw";
			$ret['err'] = 210;
			return false;
		} 

		$kws = $this->filter_kw($kw);

		if (!empty($ret['total'])) {
			$ret['msg'] = "success";
			$ret['list'] = $this->searchByKwLike($kws, $lim);
		}else {
			$ret['list'] = array();
		}

		return $ret;
	}

	private function filter_kw($kw) 
	{
		//仅查询前2个字段
		$kw = preg_replace('/\s+/',' ', $kw);
		$kw = explode(' ', $kw);
		if (count($kw) > 1) { $kws[] = $kw[0]; $kws[] = $kw[1]; }
		else { $kws[] = $kw[0]; }
		return $kws;
	}

	public function searchKwCount($kws)
	{
		$kws = $this->filter_kw($kws);

		//读取redis
		$count = $this->tryGetByRedis($kws, REDIS_SEARCH_TOTAL_PREFIX);
		if (!empty($count)) { return $count; }
		
		foreach($kws as $kw) { $where['title'][] = array('like',"%$kw%"); }
		$where['title'][] = 'and'; 

		$count = (int)$this->magnet_tbl->where($where)->count();

		//无结果就压入搜索队列
		if ($count === 0) {
		    unset($kw);

		    foreach ($kws as $kw) {
                        $list = $this->redis_instance()->lrange(self::ZHONGZISO_TAGS, 0, 100);
                        if (empty($list) OR !in_array($kw, $list)) { $this->redis_instance()->rpush(self::ZHONGZISO_TAGS, $kw); }
		    }
		}

		//设置redis
		$this->trySetRedis($kws, $count, REDIS_EXPIRE_TIME, REDIS_SEARCH_TOTAL_PREFIX);

		return $count; 
	}

	private function searchByKwLike($kws, $lim)
	{

		//读取redis
		$list = $this->tryGetByRedis($kws, REDIS_SEARCH_PREFIX);
		if (!empty($list)) { return json_decode($list, true); }

		$where = array();
		foreach($kws as $kw) { $where['title'][] = array('like',"%$kw%"); }
		$where['title'][] = 'and'; 
		$sql = $this->magnet_tbl->where($where);
		if (!empty($lim)) { $sql = $sql->limit($lim); }
		$list = $sql->select();

		//设置redis
		$this->trySetRedis($kws, json_encode($list), REDIS_EXPIRE_TIME, REDIS_SEARCH_PREFIX);

		return $list;

	}

	private function tryGetByRedis($kws, $pre_fix = "")
	{
		$kws = $pre_fix . implode("_", $kws);
		return $this->get_string_redis($kws);
	}

	private function trySetRedis($kws, $string, $expire = NULL, $pre_fix = NULL)
	{
		$kws = $pre_fix . implode("_", $kws);
		return $this->set_string_redis($kws, $string, $expire);
	}


	private function encode($data)
	{
		if (isset($data['tags'])) { $data['tags'] = json_encode($data['tags'], JSON_UNESCAPED_UNICODE); }
		return $data;
	}

	private function decode($data)
	{
		if (isset($data['tags']) AND !empty($data['tags'])) { $data['tags'] = json_decode($data['tags'], true); }
		else { $data['tags'] = array(); }

		return $data;
	}

}
