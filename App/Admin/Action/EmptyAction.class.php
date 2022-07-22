<?php
namespace Admin\Action;
use Common\Action\FirstendAction;
class EmptyAction extends FirstendAction{
    public function _empty() {
        send_http_status(404);
        $this->display('404');
    }
}