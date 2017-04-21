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
		$where = array('magnet_id' => $magnet_i);
		$old = $this->file_list_tbl->where($where)->find();	
		if (empty($old)) {
			$da = array('data' => $data,);
			$this->file_list_tbl->where($where)->data($da)->add();
		}
	}

	private function encode($data)
	{
		if (isset($data['data'])) { $data['data'] = json_encode($data['data']); }

		return $data;
	}

	private function decode($data)
	{
		if (isset($data['data']) AND !empty($data['data'])) { $data['data'] = json_decode($data['data'], true); }
		else { $data['data'] = array(); }

		return $data;
	}
}
