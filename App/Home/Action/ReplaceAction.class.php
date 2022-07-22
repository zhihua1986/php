<?php
namespace Home\Action;

use Common\Action\FuncAction;

class ReplaceAction extends FuncAction
{
    private $text;
    public function content($text)
    {
        $this->text=$text;
        return $this->replace();
    }
    private function replace()
    {
        $keywords=$this->keywords();
        $pattern =implode('|', array_keys($keywords));
        $pattern ="/((?<!<))($pattern)(?![^<>]*(?:>|<\/a>))/";
        return preg_replace_callback($pattern, [$this, callback], $this->text);
    }

    private function callback($matches)
    {
        global $log;
        if ($log[$matches[2]]) {
            return $matches[0];
        }
        $log[$matches[2]]=true;
        $keywords = $this->keywords();
        $link = $keywords[$matches[2]];
        return '<a href="'.$link.'">'.$matches[2].'</a>';
    }

    private function keywords()
    {
        $mod = M("topic");
        $Keywords = $mod->field('name,url')->select();
        $kcount=0;
        foreach ($Keywords as $key => $value) {
            if ($kcount>2) {
                break;
            }
            if (strpos($this->text, $value['name'])) {
                $m[$value['name']]=$value['url'];
                $kcount++;
            }
        }
        krsort($m);
        return $m;
    }
}
