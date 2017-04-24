<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController {
	public function __construct(){
		parent::__construct();
		$this->magnet = D("magnet");
		$this->file_list = D('file_list');
	}

	public function index(){
	}

	public function kw()
	{
		$kw = I('get.kw');
		$ret = array( 'err' => 0 );
		do {
			if (empty($kw)) {
				$ret['err'] = 1;
				$ret['msg'] = 'empty request';
				logger("ERR","空的请求".print_r($kw, true));
				break;
			}

			$limit = I('get.limit');
			$offset = I('get.offset');
			$lim = NULL;

			if (isset($limit) AND isset($offset) AND is_numeric($limit) AND is_numeric($offset)) {
				$lim = "$offset, $limit";
			}else { $lim = "0,10"; }

			if (!$this->magnet->searchByKw($kw, $ret, $lim)) {
				$ret['err'] = 2;
				$ret['msg'] = "search failed";
				logger("ERR", "搜索失败".print_r($kw, true));
				break;
			}
			if (!empty($ret['list'])) {
				$ret['list'] = $this->filterMagnetList($ret['list']);
			}

		}while(0);

		$this->ajaxReturn($ret);
	}

	private function filterMagnetList($list)
	{
		$ret = array();
		$format = array(
				'id' => 0,
				'title' => '',
				'create_time' => 0,
				'file_size' => 0,
				'check_times' => 0,
				'is_banned' => 0,
				);
		foreach($list as $v)
		{
			$tmp = filterIssetAndType($format, $v);
			$tmp['id'] = $this->encode_id($tmp['id']);
			$tmp['file_size'] = kb2SizeName($tmp['file_size']);
			if ($tmp['is_banned'] == 0 ) { $ret[] = $tmp; }
		}
		return $ret;
	}

	public function dt()
	{
		$id = I('get.sign');
		do {
			if (empty($id)) {
				$ret['err'] = 1;
				$ret['msg'] = "sgin为空";
				break;
			}

			$id = $this->decode_id($id);
			if (empty($id) OR !is_numeric($id)) {
				$ret['err'] = 2;
				$ret['msg'] = "invaild sign";
				break;
			}

			$detail = $this->magnet->getMagnetById($id);	
			if (empty($detail)) {
				$ret['err'] = 2;
				$ret['msg'] = "根据相关法律法规,资源已被禁用";
				break;
			}	

		}while(0);

		if ($ret['err'] != 0) {
			echo $ret['msg'];
			return ;
		}

		$format = array(
				'id' => 0,
				'title' => '',
				'hash_value' => '',
				'create_time' => 0,
				'file_size' => 0,
				'file_count' => 0,
				'check_times' => 0,
				'tags' => array(),
				'is_banned' => 0,
				'file_list' => array(),
				);
		$detail = filterIssetAndType($format, $detail);
		$tmp['id'] = $this->encode_id($tmp['id']);
		$detail['file_size'] = kb2SizeName($detail['file_size']);	

		$detail['file_list'] = $this->file_list->getFileListByMagnetId($detail['id']);
		if (!empty($detail['file_list'])) {
			foreach($detail['file_list'] as &$v) { $v['size'] = kb2SizeName($v['size']); }
		}
		var_dump($detail);
	}
}
