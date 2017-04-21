<?php
namespace Home\Model;

use Think\Model;

use Org\WeiXin\Encrypt;
class BaseModel extends Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function encrypt_movie_id($id)
	{
		$encrypt = new Encrypt();
		$string = rand(100,90000).'|'.time().'|'.$id.'|'.rand(100,90000);
		return $encrypt->setKey(ENCRYPT_MOVIE_KEY)->encrypt($string);
	}

	public function decrypt_movie_id($string)
	{
		$encrypt = new Encrypt();
		$code = $encrypt->setKey(ENCRYPT_MOVIE_KEY)->decrypt($string);
		return explode('|',$code)[2];
	}
}
