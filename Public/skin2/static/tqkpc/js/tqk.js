layui.define(['layer','cookie'], function(exports){
//;!function(exports){
var $ = layui.$
,form = layui.form
,flow = layui.flow
,util = layui.util
,laytpl = layui.laytpl
,cookie = layui.cookie
,element = layui.element
,carousel = layui.carousel;

 var scrChange = function(){
    var scr = $(document).scrollTop()
    ,height = document.body.offsetHeight - 140 + $(document).scrollTop();  
    if(document.body.clientWidth >= 751){
      $("#silde").css("top", height);
    }else{
      $("#silde").css("top", "auto");
    }
  }

form.on('submit(TqkFillPhone)',function(data){
$.ajax({
            url:'/index.php?c=login&a=fillphone',
            type:'post',
            data:data.field,
            dataType:"json",
            success:function(data){
             if(data.status == 1){
                layer.msg(data.msg, {icon:6});
								setTimeout(function(){
									window.location.href = data.data;
								},1000);
                }
                else{
                   layer.msg(data.msg)
                }
            },
            error:function(e){
            layer.alert("操作失败,请刷新页面重试！")
            },
            
        });
   	 return false; 
   
  });

form.on('submit(TqkFindpwd)',function(data){
$.ajax({
            url:'/index.php?c=login&a=findpwd',
            type:'post',
            data:data.field,
            dataType:"json",
            success:function(data){
             if(data.status == 1){
                layer.msg(data.msg, {icon:6});
								setTimeout(function(){
									window.location.href = data.data;
								},1000);
                }
                else{
                   layer.msg(data.msg)
                }
            },
            error:function(e){
            layer.alert("操作失败,请刷新页面重试！")
            },
            
        });
   	 return false; 
   
  });

$('.signin').on('click',function(){
layer.confirm('需要登录后才可以领券哟!', {
  btn: ['立即登录', '我知道啦'] 
}, function(index, layero){
window.location.href="/index.php?c=login&a=index";
}, function(index){
layer.closeAll();
return false
});

})

form.on('submit(TqkRegister)',function(data){
$.ajax({
            url:'/index.php?c=login&a=register',
            type:'post',
            data:data.field,
            dataType:"json",
            success:function(data){
             if(data.status == 1){
                   layer.msg(data.msg, {icon:6});
								setTimeout(function(){
									window.location.href = data.data;
								},1000);
                }
                else{
                   layer.msg(data.msg)
                }
            },
            error:function(e){
            layer.alert("操作失败,请刷新页面重试！")
            },
            
        });
   	 return false; 
   
  });

form.on('submit(Tqktixian)',function(data){
	
	 var DISABLED = 'layui-btn-disabled';
	  $(':button').addClass(DISABLED); 
	  $(':button').attr('disabled', 'disabled');
	
$.ajax({
            url:'/index.php?c=user&a=tixian',
            type:'post',
            data:data.field,
            dataType:"json",
            success:function(data){
              if(data.status == 1){
                layer.msg(data.msg, {icon:6});
								setTimeout(function(){
									window.location.href = data.data;
								},1000);
                }
                else{
			$(':button').removeClass(DISABLED);
			$(':button').removeAttr('disabled');
                   layer.msg(data.msg)
                }
            },
            error:function(e){
			$(':button').removeClass(DISABLED);
			$(':button').removeAttr('disabled');
            layer.alert("操作失败,请刷新页面重试！")
            },
            
        });
   	 return false; 
   
  });

form.on('submit(TqkSuborder)',function(data){
$.ajax({
            url:'/index.php?c=user&a=suborder',
            type:'post',
            data:data.field,
            dataType:"json",
            success:function(data){
              if(data.status == 1){
                layer.msg(data.msg, {icon:6});
								setTimeout(function(){
									window.location.href = data.data;
								},1000);
                }
                else{
                   layer.msg(data.msg)
                }
            },
            error:function(e){
            layer.alert("操作失败,请刷新页面重试！")
            },
            
        });
   	 return false; 
   
  });

form.on('submit(TqkLogin)',function(data){
$.ajax({
            url:'/index.php?c=login&a=login',
            type:'post',
            data:data.field,
            dataType:"json",
            success:function(data){
             if(data.status == 1){
                 window.location.href=data.data;
                }
                else{
                   layer.msg(data.msg)
                }
            },
            error:function(e){
            layer.alert("操作失败,请刷新页面重试！")
            },
            
        });
   	 return false; 
   
  });


        var countdown=60;
		var timer;
		function settime(obj) {
		    if (countdown == 0) {
		        obj.removeAttribute("disabled");
		        obj.value="获取验证码";
		       // obj.removeAttribute('style');
		        countdown = 60;
		        return;
		    } else { 
		        obj.setAttribute("disabled", true);
		        obj.value="重新发送(" + countdown + ")";
		        obj.style.backgroundColor= "#d1d1d1";
		        obj.style.borderColor= "#d1d1d1";
		        countdown--;
		    } 
		    timer = setTimeout(function() { 
		    	settime(obj);
		    },1000) 
		}
		
		$('#sendCode').click(function(){
			if($('input[name=phone]').val() == ''){
				layer.msg('请输入手机号码', {icon:5});
				return false;
			}
			settime(this);
			var _this = this;
			var tempid = $(this).attr('rel');
			var smstpl = $(this).attr('smstpl');
			$.post('/index.php?c=login&a=pwdcode',{
				phone:$('input[name=phone]').val(),
				tempid:tempid,
				__hash__:$('input[name=__hash__]').val(),
				ac:smstpl
			}, function(json){
			 if(json.status == 1){
			  layer.msg(json.msg, {icon:6});
			 }
			 if(json.status != 1){
					clearTimeout(timer);
					_this.removeAttribute("disabled");
					_this.value="获取验证码";
					//_this.removeAttribute('style');
			        countdown = 60;
			        layer.msg(json.msg, {icon:5});
				}
			})
		});

$(function(){
	var vartar = $.cookie('nickname');
	if(vartar!=null && typeof(vartar) != "undefined") {
		
var loginTxt = '<p>欢迎，<a href="/index.php?c=user&a=ucenter" class="tqk-c-red">'+vartar+'</a>\
	 |<a href="/index.php?c=login&a=logout" class="c-main">退出登录</a>\
	  |<a target="_blank" rel="nofollow" href="/index.php?c=help&a=index&id=contactus">联系客服</a></p>';
	$('#islogin').html(loginTxt);
	  } else {
	   var  loginTxt = '<a class="tqk-c-red" href="/index.php?c=login&a=index">亲,请登录</a> 或\
		<a href="/index.php?c=login&a=register">免费注册</a> |\
		<a target="_blank" rel="nofollow" href="/index.php?c=help&a=index&id=contactus">联系客服</a>';
	   $('#islogin').html(loginTxt);
	  }


  $('.jumptopdd').on('click',function(){
  var goods_name=$(this).attr('goods_name');
  var coupon_discount=$(this).attr('coupon_discount');
  var goods_thumbnail_url=$(this).attr('goods_thumbnail_url');
  var min_normal_price=$(this).attr('min_normal_price');
  var min_group_price=$(this).attr('min_group_price');
  var coupon_end_time = $(this).attr('coupon_end_time');
  var promotion_rate = $(this).attr('promotion_rate');
  var goods_id = $(this).attr('goods_id');
  var sold_quantity = $(this).attr('sold_quantity');
  var search_id = $(this).attr('search_id');
  $.ajax({
  	type:"post",
  	data:{search_id:search_id,sold_quantity:sold_quantity,goods_id:goods_id,promotion_rate:promotion_rate,coupon_end_time:coupon_end_time,goods_name:goods_name,coupon_discount:coupon_discount,goods_thumbnail_url:goods_thumbnail_url,min_normal_price:min_normal_price,min_group_price:min_group_price,coupon_end_time:coupon_end_time},
  	url:"/index.php?c=pdditem&a=jumpclick",
  	dataType: 'json',
  	success:function(json){
  		if(json.status==1){
  			window.location.href=json.urls;
  		}
  	}
  });
  	
  });

  $('.jumptojd').on('click',function(){
  var comments=$(this).attr('comments');
  var skuname=$(this).attr('skuname');
  var couponprice=$(this).attr('couponprice');
  var quan=$(this).attr('quan');
  var skuid=$(this).attr('skuid');
  var link = $(this).attr('link');
  var commission_rate = $(this).attr('commission_rate');
  $.ajax({
  	type:"post",
  	data:{commission_rate:commission_rate,comments:comments,skuname:skuname,coupon_price:couponprice,quan:quan,skuid:skuid,link:link},
  	url:"/index.php?c=jditem&a=jumpclick",
  	dataType: 'json',
  	success:function(json){
  		if(json.status==1){
  			window.location.href=json.urls;
  		}
  	}
  });
  	
  });

  $('.jump').on('click',function(){
  var num_id=$(this).attr('data-cnzz');
  var quan=$(this).attr('rel');
  var quanid=$(this).attr('quanid');
  var quanurl=$(this).attr('quanurl');
  var trackurl=$(this).attr('trackurl');
  var rate = $(this).attr('data-rate');
  $.ajax({
  	type:"post",
  	data:{numid:num_id,quan:quan,quanurl:quanurl,rate:rate,quanid:quanid},
  	url:"/index.php?c=jumpto&a=jumpclick",
  	dataType: 'json',
  	success:function(json){
  		if(json.status==1){
  			window.location.href=json.urls+trackurl;
  		}
  	}
  });
  	
  });

$('#verifyImg').on('click',function(){
	 var verifyimg = $('#verifyImg').attr("src");
	 $(this).attr("src", verifyimg + '?random=' + Math.random());
})


$('#getdesc').on('click',function(){
//var num_iid = $(this).attr('rel');
	var num_iid = $(this).val();
	$.ajax({
	     url:"/index.php?c=item&a=productinfo&numiid="+num_iid,
	     type:'get',
	     dataType:'json',
	     success: function(data){
	     	var imglist=data.content;
	     $('#images-content').html(imglist);
	     }
	})
	
});

$('.tqk-filter-checkbox').on('change',function(){
if($(this).is(':checked')){
location.href=$(this).attr('value');

}else{

location.href=$(this).attr('val');

}


	
});

	
flow.lazyimg({
    elem: 'img.load'
  });
  util.fixbar({
    bar1: false
  });

carousel.render({
    elem: '#notice'
    ,width: '674px'
    ,height: '30px'
    ,anim: 'updown'
    ,arrow:'none'
    ,indicator: 'none'
  });

 carousel.render({
    elem: '#test1'
    ,width: '674px'
    ,height: '300px'
    ,arrow: 'always'
  });
	$('#search-hd .search-input').on('input propertychange',function(){
				var val = $(this).val();
				if(val.length > 0){
					$('#search-hd .pholder').hide(0);
				}else{
					var index = $('#search-bd li.selected').index();
					$('#search-hd .pholder').eq(index).show().siblings('.pholder').hide(0);
				}
			})
		$('#search-bd li').click(function(){
			var index = $(this).index();
			$('#sourl').val($(this).attr('rel'));
			$('#search-hd .pholder').eq(index).show().siblings('.pholder').hide(0);
			$('#search-hd .search-input').eq(index).show().siblings('.search-input').hide(0);
			$(this).addClass('selected').siblings().removeClass('selected');
			$('#search-hd .search-input').val('');
		});


  });


form.on('submit(SoSo)',function(data){

var url = data.field.url;
 if(data.field.tb){
	window.location.href=url + "?k=" + data.field.tb ;
	return false;
 }
 if(data.field.jd){
 	window.location.href=url + data.field.jd ;
 	return false;
 }
 if(data.field.pdd){
 	window.location.href=url + "?k=" + data.field.pdd ;
 	return false;
 }
 layer.msg('搜索词不能为空！');
return false;
// var loadIndex=layer.load(1,{shade:[0.5,'#000']});
// $.ajax({
//             url:'/index.php?c=cate&a=so',
//             type:'post',
//             data:data.field,
//             dataType:"json",
//             success:function(data){
// 	       if(data.status === 0){
// 	       layer.msg(data.msg);
// 	       }
// 	       if(data.code == 200 && data.msg == 'jump'){
// 	       	window.location.href=data.result;
// 	       	return false;
// 	       }
// 	      layer.close(loadIndex);
//            }
//             
//         });
//    	 return false; 
   
  });

  exports('tqk', {}); 
 //}();
})