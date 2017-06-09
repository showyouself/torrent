<?php
namespace Home\Model;

use Think\Model;

class  HotMoviesModel extends BaseModel
{

    public function __construct()
    {
        parent::__construct();
        $this->hot_movies = M('hot_movies');
    }

    public function getHotMovies()
    {
        return $this->hot_movies->select();
    }

    public function syncHotMovies($new, &$ret)
    {
        if (empty($new['name']) OR empty($new['logo_url'])) {
            $ret['msg'] = "name，logo_url 不能为空";
            $ret['err'] = 100;
            logger("ERR", print_r($ret, true));
            return false;
        }

        $logo_uid = $this->capture_pic($new['logo_url']);

        if ($this->getHotMoivesByName($new['name'])) {
            $tmp =  $this->hot_movies->where("name='{$new['name']}'")->save(array('logo_url' => $logo_uid));
        }else {
            $tmp = $this->hot_movies->data(array('name' => $new['name'] , 'logo_url' => $logo_uid))->add();
        }

        if ($tmp) {
            $ret['err'] = 0;
            $ret['msg'] = '同步成功';
        }
    }

    private function getHotMoivesByName($name)
    {
        return $this->hot_movies->where("name='$name'")->find();
    }

    public function capture_pic($url)
    {

        $curl  = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $file = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode != 200)  { return false; }

        if (!file_exists(PIC_BASE_PATH)) { mkdir(PIC_BASE_PATH, 0777, true); }

        $image_name = uniqid();
        $full_path = PIC_BASE_PATH . $image_name . '.jpg';

        file_put_contents($full_path, $file);
        return $image_name;
    }

}
