<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
		$magnet = D("magnet");
		$data = array(
				'hash_value' => 'DFA1A22C56D561D9FAEE2F4D5DA1A214BF161CC1',
				'create_time' => 1492766457,
				'file_size' => 12455745,
				'file_count' => 1,
				'tags' => array("速度", "激情", "速度与激情"),
				'title' => "速度与激情",
				'file_list' => array(
					array(
						"name" => "速度与激情请8",
						"size" => 10232,
						),
					),
				);
		$ret['err'] = 0;
		if ($magnet->syncMagnet($data, $ret)) {
			echo "success";
		}else {
			var_dump($ret);
		}
		
    }
}
