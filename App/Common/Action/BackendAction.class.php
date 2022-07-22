<?php
namespace Common\Action;
use Common\Model;
/**
 * 后台控制器基类
 */
class BackendAction extends TopAction
{
    protected $_name = '';
    protected $menuid = 0;
    public function _initialize() {
       parent::_initialize();
	    $this->_name= CONTROLLER_NAME == 'Banner'?'ad':CONTROLLER_NAME;
        $this->check_priv();
        $this->menuid = I('menuid','0','trim');
        if ($this->menuid) {
            $sub_menu = D('menu')->sub_menu($this->menuid, $this->big_menu);
            $selected = '';
            foreach ($sub_menu as $key=>$val) {
                $sub_menu[$key]['class'] = '';
                if (MODULE_NAME == $val['module_name'] && ACTION_NAME == $val['action_name'] && strpos(__SELF__, $val['data'])) {
                    $sub_menu[$key]['class'] = $selected = 'on';
                }
            }
	
            if (empty($selected)) {
                foreach ($sub_menu as $key=>$val) {
                    if (MODULE_NAME == $val['module_name'] && ACTION_NAME == $val['action_name']) {
                        $sub_menu[$key]['class'] = 'on';
                        break;
                    }
                }
            }
			
		
		
            $this->assign('sub_menu', $sub_menu);
        }
	
$this->assign('menuid', $this->menuid);



}



/*
* 获取淘宝系统时间
*/

protected function GetTbTime(){
	vendor("taobao.taobao");
	$appkey=trim(C('yh_taobao_appkey'));
	$appsecret=trim(C('yh_taobao_appsecret'));
	$c = new \TopClient();
	$c->appkey = $appkey;
	$c->secretKey = $appsecret;
	$req = new \TimeGetRequest();
	$resp = $c->execute($req);
	$resp = json_decode(json_encode($resp), true);
	if($resp['time']){
		return $resp['time'];
	}
	return false;
}


    /**
     * 列表页面
     */
    public function index(){
		$map = $this->_search();
        $mod = D($this->_name);
		
      !empty($mod)&$this->_list($mod, $map);
	  
        $this->display();
    }
    

    public  function getpic($content){
        $soContent = $content;
        $soImages = '~<img [^>]* />~';
        preg_match_all( $soImages, $soContent, $thePics);
        $allPics = count($thePics[0]);
        preg_match('/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|png|jpeg))\"?.+>/i',$thePics[0][0],$match);
        if($allPics>0 && $match[1]!="null"){
   return $match[1];//获取的图片名称
}else{
    return "/Public/static/images/nopic.jpg";
}


}

    /**
     * 添加
     */
    public function add() {
        $mod = D($this->_name);
        
        if (IS_POST) {
            if (false === $data = $mod->create()) {
                IS_AJAX && $this->ajaxReturn(0, $mod->getError());
                $this->error($mod->getError());
            }
            if (method_exists($this, '_before_insert')) {
                $data = $this->_before_insert($data);
                
                
            }

              if(!empty($data['info'])){
             	$data['info']= stripslashes(htmlspecialchars_decode($data['info']));
			   $data['info']=$this->clearhtml($data['info']); // 过滤掉文章中的 html 
			   $data['pic']?$data['pic']:'/Public/static/images/nopic.jpg';
			}
			
			if(!empty($data['content'])){
            	$data['content']= stripslashes(htmlspecialchars_decode($data['content']));
			}
			
			if(!empty($data['url'])){
				$data['url']= stripslashes(htmlspecialchars_decode($data['url']));
			}
			
			
			if(!empty($data['username'])){
               $data['nickname'] = $data['username'];
           }
		   
		   
		   if($data['pid']){
		   	
		   	$data['pid'] = trim($data['pid']);
		   }
           
       if($data['beginTime']){
        	 $data['beginTime'] = strtotime($data['beginTime']);
        }
         if($data['endTime']){
        	 $data['endTime'] = strtotime($data['endTime']);
        }

           if( $mod->add($data) ){
            if( method_exists($this, '_after_insert')){
                $id = $mod->getLastInsID();
                $this->_after_insert($id);
            }
            IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'add');
            $this->success(L('operation_success'));
        } else {
            IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
            $this->error(L('operation_failure'));
        }
    } else {
        $this->assign('open_validator', true);
        if (IS_AJAX) {
            $response = $this->fetch();
            $this->ajaxReturn(1, '', $response);
        } else {
            $this->display();
        }
    }
}

public function clearhtml($str){
    $str = preg_replace( "@<script(.*?)</script>@is", "", $str ); 
    $str = preg_replace( "@<iframe(.*?)</iframe>@is", "", $str ); 
    $str = preg_replace( "@<style(.*?)</style>@is", "", $str ); 

    return $str;	
}



    /**
     * 修改
     */
    public function edit()
    {
        $mod = D($this->_name);
        $pk = $mod->getPk();
        if (IS_POST) {

            if (false === $data = $mod->create()) {
                IS_AJAX && $this->ajaxReturn(0, $mod->getError());
                $this->error($mod->getError());
            }
        
            if (method_exists($this, '_before_update')) {
                $data = $this->_before_update($data);
                
                
            }
            
            if(!empty($data['info'])){
			  
			  $data['info']= htmlspecialchars_decode($data['info']);
			   $data['info']=$this->clearhtml($data['info']); //fre 添加 过滤掉文章中的 html
			   $data['pic']?$data['pic']:'/Public/static/images/nopic.jpg';
            }
            
        if($data['beginTime']){
        	 $data['beginTime'] = strtotime($data['beginTime']);
        }
         if($data['endTime']){
        	 $data['endTime'] = strtotime($data['endTime']);
        }
			
			if(!empty($data['content'])){
            	$data['content']= stripslashes(htmlspecialchars_decode($data['content']));
			}
            if(!empty($data['url'])){
            	$data['url']= stripslashes(htmlspecialchars_decode($data['url']));
            }
            if(!empty($data['password'])){
                $data['password'] = md5($data['password']);
            }else{
                unset($data['password']);
            }
			
			
			if($data['pid']){
				
				$data['pid'] = trim($data['pid']);
			}
            
            if (false !== $mod->save($data)) {
                if( method_exists($this, '_after_update')){
                    $id = $data['id'];
                    $this->_after_update($id);
                }
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'edit');
                $this->success(L('operation_success'));
            } else {
                IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                $this->error(L('operation_failure'));
            }
        } else {

            $id = I($pk,'0','intval');
            $info = $mod->find($id);
            $this->assign('info', $info);
            $this->assign('open_validator', true);
            if (IS_AJAX) {
                $response = $this->fetch();
                $this->ajaxReturn(1, '', $response);
            } else {
                $this->display();
            }
        }
    }

    /**
     * ajax修改单个字段值
     */
    public function ajax_edit()
    {
        //AJAX修改数据
        $mod = D($this->_name);
        $pk = $mod->getPk();
        $id = I($pk,'0','intval');
        $field = I('field','','trim');
        $val = I('val','','trim');
        //允许异步修改的字段列表  放模型里面去 TODO
        $mod->where(array($pk=>$id))->setField($field, $val);
        $this->ajaxReturn(1);
    }

    /**
     * 删除
     */
    public function delete()
    {
        $mod = D($this->_name);
        $pk = $mod->getPk();
        $ids = trim(I($pk), ',');
        if ($ids) {
            if (false !== $mod->delete($ids)) {
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'));
                $this->success(L('operation_success'));
            } else {
                IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                $this->error(L('operation_failure'));
            }
        } else {
            IS_AJAX && $this->ajaxReturn(0, L('illegal_parameters'));
            $this->error(L('illegal_parameters'));
        }
    }

    /**
     * 获取请求参数生成条件数组
     */
    protected function _search() {
        //生成查询条件
        $mod = D($this->_name);
        $map = array();
        foreach ($mod->getDbFields() as $key => $val) {
            if (substr($key, 0, 1) == '_') {
                continue;
            }
            if (I($val)) {
                $map[$val] = I($val);
            }
        }
        return $map;
    }

    /**
     * 列表处理
     *
     * @param obj $model  实例化后的模型
     * @param array $map  条件数据
     * @param string $sort_by  排序字段
     * @param string $order_by  排序方法
     * @param string $field_list 显示字段
     * @param intval $pagesize 每页数据行数
     */
    protected function _list($model, $map = array(), $sort_by='', $order_by='', $field_list='*', $pagesize=50)
    {
        //排序
    
        $mod_pk = $model->getPk();
        
        if (I("sort",'','trim')) {
            $sort = I("sort", '','trim');
        } else if (!empty($sort_by)) {
            $sort = $sort_by;
        } else if ($this->sort) {
            $sort = $this->sort;
        } else {
            $sort = $mod_pk;
        }
		
        if (I("order",'','trim')) {
            $order = I("order",'','trim');
        } else if (!empty($order_by)) {
            $order = $order_by;
        } else if ($this->order) {
            $order = $this->order;
        } else {
            $order = 'DESC';
        }

        if ($pagesize) {
            $count = $model->where($map)->count($mod_pk);
            $pager = new \Think\Page($count, $pagesize);
        }
        $select = $model->field($field_list)->where($map)->order($sort . ' ' . $order);
        $this->list_relation && $select->relation(true);
        
        if ($pagesize) {
            $select->limit($pager->firstRow.','.$pager->listRows);
            $page = $pager->show();
            $this->assign("page", $page);
        }
        $list = $select->select();
        foreach ($list as $k => $v) {
            if($v['uid']){
                $nick = M('user')->field('phone,username')->where("id='".$v['uid']."'")->find();
                $list[$k]['nick'] = $nick['username'];
				$list[$k]['phone'] = $nick['phone'];
            }
        }
        $this->assign('count', $count);
        $this->assign('list', $list);
        $this->assign('list_table', true);
    }


 /**
     * 前台分页统一
     */
    protected function _pager($count, $pagesize, $path = null)
    {
        $pager = new Page($count, $pagesize);
        if($path){
            $pager->path = $path;
        }
        $pager->rollPage = 3;
        $pager->setConfig('header', '条记录');
        $pager->setConfig('prev', '上一页');
        $pager->setConfig('next', '下一页');
        $pager->setConfig('first', '第一页');
        $pager->setConfig('last', '最后一页');
        $pager->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
        return $pager;
    }

    public function check_priv() {
        if (MODULE_NAME == 'attachment') {
            return true;
        }
		
        if (!session('admin.id') && !in_array(ACTION_NAME, array('login','verify_code')) ) {
            $this->redirect('index/login');
        }
		
		if(in_array(ACTION_NAME, array('login','verify_code'))){
			return true;
		}
		
        if(session('admin.role_id') == 1) {
            return true;
        }
		
        if (in_array(MODULE_NAME, explode(',', 'index'))) {
            return true;
        }
        $menu_mod = M('menu');
        $menu_id = $menu_mod->where(array('module_name'=>strtolower(CONTROLLER_NAME), 'action_name'=>ACTION_NAME))->getField('id');
	    $priv_mod = M('adminauth');
        $r = $priv_mod->where(array('menu_id'=>$menu_id, 'role_id'=>session('admin.role_id')))->count();
	    if (!$r && ACTION_NAME!='left' && ACTION_NAME!='panel' && ACTION_NAME!='logout') {
        $this->error(L('_VALID_ACCESS_'));
        }
    }
    
    protected function update_config($new_config, $config_file = '') {
        !is_file($config_file) && $config_file = CONF_PATH . 'index/config.php';
       
        if (is_writable($config_file)) {
            $config = require $config_file;
            $config = array_merge($config, $new_config);
            file_put_contents($config_file, "<?php \nreturn " . stripslashes(var_export($config, true)) . ";", LOCK_EX);
            @unlink(RUNTIME_FILE);
            return true;
        } else {
            return false;
        }
    }
}