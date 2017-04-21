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
		$where['name'] = array('IN',implode(',', $tags));
		$old = $this->tags_tbl->where($where)->getField('name,id');
		$data = array();
		foreach ($tags as $v) { 
			if (!array_key_exists($v, $old)) { $data[] = array('name' => $v); }
		}
		return $this->tags_tbl->addAll($data);
	}
}
