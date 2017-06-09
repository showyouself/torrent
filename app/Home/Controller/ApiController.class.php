<?php
namespace Home\Controller;
use Think\Controller;
class ApiController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->magnet = D("magnet");
    }

    public function index()
    {
    }

    public function sync_magnet()
    {
        /*$data = array(
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
         */
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $ret = array('err' => 0);
        do {

            if (I('get.sign') != API_SIGN) {
                $ret['err'] = 1;
                $ret['msg'] = "invalid request";
                logger("ERR", print_r($ret, true));
                break;
            }

            if (empty($data)) {
                $ret['err'] = 10;
                $ret['msg'] = "empty post";
                logger("ERR", print_r($ret, true));
                break;
            }

            if (!$this->magnet->syncMagnet($data, $ret)) {
                logger("ERR", "更新失败:" . print_r($ret, true));
                break;
            }
            $ret["msg"] = "success";
        } while (0);

        $this->ajaxReturn($ret);
    }

    public function sync_hot()
    {
        $this->hot_movies = D("hot_movies");
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
       /* $data = array(
            'name' => 'X特遣队',
            'logo_url' => 'http://www.id97.com/static/images/default.png',
        );*/
        $ret = array('err' => 0);
        do {

            if (I('get.sign') != API_SIGN) {
                $ret['err'] = 1;
                $ret['msg'] = "invalid request";
                logger("ERR", print_r($ret, true));
                break;
            }

            if (empty($data)) {
                $ret['err'] = 10;
                $ret['msg'] = "empty post";
                logger("ERR", print_r($ret, true));
                break;
            }

            if (!$this->hot_movies->syncHotMovies($data, $ret)) {
                logger("ERR", "更新失败:" . print_r($ret, true));
                break;
            }
            $ret["msg"] = "success";
        } while (0);

        $this->ajaxReturn($ret);
    }

}
