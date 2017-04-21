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
		$old = $this->file_list_tbl->where(array('magnet_id' => $magnet_i))->find();	
		if (empty($old)) {
			$data = array(
					'data' => $data,
					);
		}
	}

}
