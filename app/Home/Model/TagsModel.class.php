<?php
namespace Home\Model;

use Think\Model;

use Org\WeiXin\Encrypt;
class  TagsModel extends BaseModel
{

	public function __construct()
	{
		parent::__construct();
		$this->tags_tbl = M('tags');
	}

	public function pushNewTags($tags)
	{
		if (empty($tags) OR !is_array($tags)) { return true; }
		$tags = array_unique($tags);
		$where['name'] = array('IN',implode(',', $tags));
		$old = $this->tags_tbl->where($where)->select();
		$data = array();
		foreach ($tags as $v) { 
			$need = true;
			foreach ($old as $o) 
			{
				if (strtoupper($v) == strtoupper($o['name'])) { $need = false; break; }
			}

			if ($need) { $data[] = array('name' => $v); } 
		}
		return $this->tags_tbl->addAll($data);
	}
}
