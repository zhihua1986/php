<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Install\Action;
use Think\Action;
use Think\Storage;

class IndexAction extends Action{
    //安装首页
    public function index(){
        if(is_file(ROOT_PATH . '/data/Config/db.php')){
            // 已经安装过了 执行更新程序
            //session('update',true);
            $msg = '请删除data/install.lock文件后再运行升级!';
        }else{
            $msg = '已经成功安装了推券客cms，请不要重复安装!';
        }
        if(Storage::has(ROOT_PATH . '/data/install.lock')){
            $this->error($msg);
        }
        $this->display();
    }

    //安装完成
    public function complete(){
        $step = session('step');

        if(!$step){
            $this->redirect('index');
        } elseif($step != 3) {
            $this->redirect("Install/step{$step}");
        }

        // 写入安装锁定文件
        Storage::put(ROOT_PATH . '/data/install.lock', 'lock');
        if(!session('update')){
            //创建配置文件
            $this->assign('info',session('config_file'));
        }
        session('step', null);
        session('error', null);
        session('update',null);
        $this->display();
    }
}