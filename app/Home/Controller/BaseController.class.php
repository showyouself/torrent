<?php
namespace Home\Controller;
use Think\Controller;
use Org\WeiXin\EncryptUtil;
use Org\WeiXin\Curl;
class BaseController extends Controller {
	public function __construct(){
		parent::__construct();
		$this->encrypt = new EncryptUtil();
	}

	protected function encode_id($id)
	{
		return $this->encrypt->mk_alias($id);
	}

	protected function decode_id($id)
	{
		return $this->encrypt->de_alias($id);
	}

	public function torrent_total()
	{
		$curl = new Curl();
		$total = $curl->rapid("http://127.0.0.1:9502?type=torrent_total");
		if (!empty($total)) { return $total; }
		return 0;
	}

}
