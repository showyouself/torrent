<?php
namespace Home\Model;

use Think\Model;

use Org\WeiXin\Encrypt;
class MagnetModel extends BaseModel
{

	public function __construct()
	{
		parent::__construct();
		$this->magnet_tbl = M('magnet');
	}

	public function syncMagnet($new, &$ret)
	{
		if (empty($new['hash_value']) OR empty($new['title'])) {
			$ret['msg'] = "hash_value，title不能为空";
			$ret['err'] = 100;
			logger("ERR", print_r($ret, true));
			return false;
		}

		$old = $this->tryGetMagnet($new);
		$update = array();
		if (!$old) {
			$ret['msg'] = "tryGetMagnet失败";
			$ret['err'] = 200;
			logger("ERR", print_r($ret, true));
			return false;
		}

		checkUpdate($old, 'title', $new, 'title', $update);
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
			logger("ERR", "更新的数据为空".print_r($m, true));
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

		//仅查询前2个字段
		$kw = preg_replace('/\s+/',' ', $kw);
		$kw = explode(' ', $kw);
		if (count($kw) > 1) { $kws[] = $kw[0]; $kws[] = $kw[1]; }
		else { $kws[] = $kw[0]; }
		
		$ret['total'] = $this->searchKwCount($kws);

		if (!empty($ret['total'])) {
			$ret['msg'] = "success";
			$ret['list'] = $this->searchByKwLike($kws, $lim);
		}else {
			$ret['list'] = array();
		}

		return $ret;
	}

	private function searchKwCount($kws)
	{
		foreach($kws as $kw) { $where['title'][] = array('like',"%$kw%"); }
		$where['title'][] = 'and'; 
		return (int)$this->magnet_tbl->where($where)->count();
	}

	private function searchByKwLike($kws, $lim)
	{
		$where = array();
		foreach($kws as $kw) { $where['title'][] = array('like',"%$kw%"); }
		$where['title'][] = 'and'; 
		$sql = $this->magnet_tbl->where($where);
		if (!empty($lim)) { $sql = $sql->limit($lim); }
		return $sql->select();
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
