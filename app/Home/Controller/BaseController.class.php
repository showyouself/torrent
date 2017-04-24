<?php
namespace Home\Controller;
use Think\Controller;
use Org\WeiXin\EncryptUtil;
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

}
