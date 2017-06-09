<?php
/**
 * User: ben
 * Date: 2017/6/9
 * Time: 16:06
 */
namespace Home\Controller;
use Think\Controller;
class MediaController extends BaseController {
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $uid = I('uid');
        $file = file_get_contents(PIC_BASE_PATH . $uid . '.jpg');
        if (!$file) { $file = file_get_contents(PIC_BASE_PATH . PIC_DEFAULT . '.jpg'); }
        header('Content-type: image/jpg');
        echo $file;
    }
}