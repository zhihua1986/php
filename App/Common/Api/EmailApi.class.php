<?php
namespace Common\Api;

use Org\Util\Smtp;

class EmailApi
{
    public static function client()
    {
        $email = new Smtp();
      	$email -> smtp(C('SMTP_SERVER'), C('SMTP_PORT'), true, C('SMTP_USER'), C('SMTP_PASS'));
        $email->debug = false;
        return $email;
    }
    
    public static function sendCode($email, $code)
    {
        $content = str_replace(array(
            '{email}',
            '{code}'
        ), array(
            $email,
            $code
        ), C('EMAIL_CODE_TEMPLATE'));
        
        return self::client()->sendmail($email, C('SMTP_USER'), C('EMAIL_CODE_TITLE'), $content, "HTML");
    }
    
    public static function sendchuli($email,$nick,$sn)
    {
        $content = str_replace(array(
            '{email}',
            '{sn}'
        ), array(
            $nick,
            $sn
        ), C('CHULI_MODEL')); 
        return self::client()->sendmail($email, C('SMTP_USER'), C('EMAIL_CODE_TITLE'), $content, "HTML");
    }
    public static function sendchongzhi($email,$nick,$sn)
    {
        $content = str_replace(array(
            '{email}',
            '{sn}'
        ), array(
            $nick,
            $sn
        ), C('CHONFZHI_MODEL')); 
        return self::client()->sendmail($email, C('SMTP_USER'), C('EMAIL_CODE_TITLE'), $content, "HTML");
    }
    
    public static function sendchajia($email,$nick,$sn)
    {
        $content = str_replace(array(
            '{email}',
            '{sn}'
        ), array(
            $nick,
            $sn
        ), C('CHAJIA_MODEL')); 
        return self::client()->sendmail($email, C('SMTP_USER'), C('EMAIL_CODE_TITLE'), $content, "HTML");
    }
    public static function sendyundan($email,$nick,$sn)
    {
        $content = str_replace(array(
            '{email}',
            '{sn}'
        ), array(
            $nick,
            $sn
        ), C('YUNDAN_MODEL')); 
        return self::client()->sendmail($email, C('SMTP_USER'), C('EMAIL_CODE_TITLE'), $content, "HTML");
    }
    
    public static function sendchongshibai($email,$nick,$sn)
    {
    	$content = str_replace(array(
            '{email}',
            '{sn}'
        ), array(
            $nick,
            $sn
        ), C('SHIBAI_MODEL')); 

        return self::client()->sendmail($email, C('SMTP_USER'), C('EMAIL_CODE_TITLE'), $content, "HTML");
    }
    
     public static function sendruku($email,$nick,$sn)
     {
     	$content = str_replace(array(
            '{email}',
            '{sn}'
        ), array(
            $nick,
            $sn
        ), C('RUKU_MODEL')); 

        return self::client()->sendmail($email, C('SMTP_USER'), C('EMAIL_CODE_TITLE'), $content, "HTML");
     }
    
    public static function sendfahuo($email,$nick,$sn)
     {
     	$content = str_replace(array(
            '{email}',
            '{sn}'
        ), array(
            $nick,
            $sn
        ), C('FAHUO_MODEL')); 

        return self::client()->sendmail($email, C('SMTP_USER'), C('EMAIL_CODE_TITLE'), $content, "HTML");
     }
    
    public static function sendquxiao($email,$nick,$sn)
     {
     	$content = str_replace(array(
            '{email}',
            '{sn}'
        ), array(
            $nick,
            $sn
        ), C('QUXIAO_MODEL')); 

        return self::client()->sendmail($email, C('SMTP_USER'), C('EMAIL_CODE_TITLE'), $content, "HTML");
     }
    
}