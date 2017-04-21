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
		checkUpdate($old, 'tags', $new, 'tags', $update);
		//checkUpdate($old, 'file_list', $new, 'file_list', $update);

		if (empty($update)) { 
			logger("ERR", "更新的数据为空".print_r($m, true));
			return true; 
		}
		return $this->updateMagnetByid($old['id'], $this->encode($update));
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

	public function updateMagnetByid($id, $data)
	{
		return $this->magnet_tbl->where(array('id' => $id))->data($data)->save();
	}

	private function encode($data)
	{
		if (isset($data['tags'])) { $data['tags'] = json_encode($data['tags']); }
		if (isset($data['file_list'])) { $data['file_list'] = json_encode($data['file_list']); }

		return $data;
	}

	private function decode($data)
	{
		if (isset($data['tags']) AND !empty($data['tags'])) { $data['tags'] = json_decode($data['tags'], true); }
		else { $data['tags'] = array(); }

		if (isset($data['file_list'])) { $data['file_list'] = json_decode($data['file_list'], true); }
		else { $data['file_list'] = array(); }

		return $data;
	}

}
