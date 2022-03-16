<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\index\controller;

use think\Controller;

class IndexController extends Controller{
    public function index()
    {
        $data = model('Setting')->getFieldsById();
        
        $url = "http://".$_SERVER['SERVER_NAME']."/".$data['fengge'].'/index.html';
        
     
        
        $this->redirect($url);
       
    }
}
