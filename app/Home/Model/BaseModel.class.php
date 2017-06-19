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

	public function redis_instance()
	{
		if (!empty($this->redis)) { return $this->redis; }
		$this->redis = new \Redis(); 
		if (!$this->redis->connect(REDIS_HOST, REDIS_PORT)) {
			logger("ERR", "connect to redis failed， check host and port first");
			return false;
		}else { return $this->redis; }
	}

	public function get_string_redis($key) 
	{ 
		if (!$this->redis_instance()) { return ""; }
		return $this->redis->get($key);	
	}

	public function set_string_redis($key, $string, $expire = NULL) 
	{ 
		if (!$this->redis_instance()) { return false; }
		if (!$this->redis->set($key, $string)) 
		{ logger("ERR", "reids key is already set：$key"); return false; } 

		if (!empty($expire) AND is_numeric($expire) AND !$this->set_expire_redis($key, $expire))
		{ logger("ERR", "reids set expire failed key：$key"); return false;}

		return true;
	}

	public function set_expire_redis($key, $expire)
	{ 
		if (!$this->redis_instance()){ return false; }
		return $this->redis->expire($key, $expire); 
	}

}
