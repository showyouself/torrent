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

	public function redis_instance(&$ret)
	{
		if (!empty($this->redis)) { return true; }
		$this->redis = new \Redis(); 
		if (!$this->redis->connect(REDIS_HOST, REDIS_PORT)) { $ret['msg'] = "connect to redis failed， check host and port first"; 
			$ret['err'] = REDIS_PORT;
			return false;
		}else { return true; }
	}

	public function get_string_redis($key) { return $this->redis->get($key);	}

	public function set_string_redis($key, $string, $expire = NULL) { 
		if (!$this->redis->set($key, $string)) 
		{ logger("ERROR", "reids key is already set：$key", array(__CLASS__, __FUNCTION__)); return false; } 

		if (!empty($expire) AND is_numeric($expire) AND !$this->set_expire_redis($key, $expire))
		{ logger("ERROR", "reids set expire failed key：$key", array(__CLASS__, __FUNCTION__)); return false;}

		return true;
	}

	public function set_expire_redis($key, $expire){ return $this->redis->expire($key, $expire); }

}
