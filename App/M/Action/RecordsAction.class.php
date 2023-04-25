<?php
namespace M\Action;

use Common\Action\FuncAction;
use Common\Model\recordsModel;
use Common\Model\pidModel;
use Common\Model\userModel;
class RecordsAction extends FuncAction
{
    private $itemid;

    public function content($itemid, $uid)
    {
        //如果用户做了渠道备案

        if (C('yh_bingtaobao') == 2 && $this->memberinfo['webmaster'] == 1 && $this->memberinfo['webmaster_pid']) {

            $josn = array(
                'pid' => trim(C('yh_taobao_pid')),
            );

            return $josn;
        }

        $data = $this->CreatePid($uid);
        $pid = $data['pid'];
        if ($pid) {
            $apppid = explode('_', $pid);
            $AdzoneId = $apppid[3];
            $mod = new recordsModel();
            $isExist = $mod->where(array('uid' => $uid, 'itemid' => $itemid))->field('id')->find();
            // if ($isExist && strlen($itemid) > 7) {
            if ($isExist) {
                $res = $mod->where(array('id' => $isExist['id']))->save(
                    array(
                        //'create_time'=>time(),
                        'ad_id' => $AdzoneId,
                    )
                );
            } else {
                $res = $mod->add(
                    array(
                        'create_time' => time(),
                        'itemid' => $itemid,
                        'uid' => $uid,
                        'ad_id' => $AdzoneId,
                    )
                );
            }

        } else {

            $pid = trim(C('yh_taobao_pid'));
        }


        $josn = array(
            'pid' => $pid,
            'rate' => $data['rate']
        );

        return $josn;
    }

    /**
     * @param $userid
     * @return array|mixed
     */
    private function CreatePid($userid)
    {
        $CacheName = 'userpid_' . $userid;
        $UserPid = S($CacheName);
        if (!$UserPid && $userid) {
            $mod = new pidModel();
            $Result = $mod->where(array('status' => 1))->field('pid,id')->order('update_time ASC')->limit(1)->find();
            $usermod = new userModel();
            $rate = $usermod->where(array('id' => $userid))->getField('webmaster_rate');
            $data = array(
                'pid' => $Result['pid'],
                'rate' => $rate
            );
            S($CacheName, $data, 7200);
            $mod->where(array('id' => $Result['id']))->save(array('update_time' => NOW_TIME));
            $UserPid = $data;
        }

        return $UserPid;

    }


}
