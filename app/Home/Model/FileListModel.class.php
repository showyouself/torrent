<?php
namespace Home\Model;

use Think\Model;

use Org\WeiXin\Encrypt;
class  FileListModel extends BaseModel
{

	public function __construct()
	{
		parent::__construct();
		$this->file_list_tbl = M('file_list');
	}

	public function updateFileList($magnet_id, $data)
	{
		if (!is_array($data)) { return false; }
		$where = array('magnet_id' => $magnet_id);
		$old = $this->file_list_tbl->where($where)->find();	
		if (empty($old)) {
			$da = array('data' => $data, 'magnet_id' => $magnet_id);
			return $this->file_list_tbl->data($this->encode($da))->add();
		}else {
			$da = array('data' => $data,);
			return $this->file_list_tbl->where($where)->data($this->encode($da))->save();
		}
	}

	private function encode($data)
	{
		if (isset($data['data'])) { $data['data'] = json_encode($data['data'], JSON_UNESCAPED_UNICODE); }

		return $data;
	}

	private function decode($data)
	{
		if (isset($data['data']) AND !empty($data['data'])) { $data['data'] = json_decode($data['data'], true); }
		else { $data['data'] = array(); }

		return $data;
	}
}
