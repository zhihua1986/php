layui.define(['carousel','form','ClipboardJS','element'], function(exports){
  var $ = layui.$
   ,form = layui.form
   ,ClipboardJS = layui.ClipboardJS
  ,carousel = layui.carousel;
  
  $(function(){

      $('#dataList').on('click','.search-title',function (){
          var title = $(this).attr('rel');
          var copysearch = new ClipboardJS(".search-title", {
              text: function() {
                  return title;
              }
          });

          layer.open({
              title: '系统提示'
              ,type: 0
              ,content: '商品标题复制成功，现在去打开【手机淘宝】搜索下单吧！'
              ,btn: ['我知道了']
          });


          // copysearch.on('success', function(e) {
          //     layer.msg('标题复制成功，现在打开淘宝App搜索下单吧！', {time:2000});
          // });
      });


var clipboard = new ClipboardJS('.copycode');
clipboard.on('success', function(e) {
layer.msg('复制成功，现在去打开【手机淘宝】吧！'); 
    e.clearSelection();
});
clipboard.on('error', function(e) {
layer.msg('复制失败'); 
});





form.on('submit(TqkSoTaolijin)',function(data){
$.ajax({
url:'/index.php?m=m&c=taolijin&a=so',
type:'post',
data:data.field,
dataType:"json",
success:function(data){
if(data.status == 1){

var lijinhtml = '';
var quanhtml = '';
var fulihtml = '';
var btnhtml = '';
if(data.fuliprice){
	data.fuliprice = parseFloat(data.fuliprice);
fulihtml = '<p class="coupon-price">福利价￥<span class="num">'+data.fuliprice.toFixed(2)+'</span></p>';
}
if(data.lijin>1){
data.lijin = parseFloat(data.lijin);
var lijinhtml = 
'<div class="coupon-info ">'+
'<div class="info-left">'+
'<div class="icon-normal icon-redbag"></div>'+
'<div class="detail">'+
'<p class="tag">淘礼金红包</p>'+
'<p class="validate">领取当天内可用</p>'+
'</div>'+
'</div>'+
'<div class="info-right" >￥<span class="big">'+data.lijin.toFixed(2)+'</span></div>'+
'</div>';
}

if(data.quan>0){
var quanhtml = '<div class="coupon-info">'+
'<div class="info-left">'+
'<div class="icon-normal icon-coupon"></div>'+
'<div class="detail">'+
'<p class="tag">优惠券</p>'+
'<p class="validate"> </p>'+
'</div>'+
'</div>'+
'<div class="info-right">￥<span class="big">'+data.quan+'</span></div>'+
'</div>';
}

if(data.rebate){
data.rebate = parseFloat(data.rebate);
btnhtml = '<div mx-guid="gemx_3" class=" use-btn "><a href="/index.php?m=m&c=item&a=index&id='+data.num_iid+'" class="btn-text">立即下单 (预计返'+data.rebate.toFixed(2)+'元)</a></div>';
}else{
btnhtml = '<div id="canvasClose" class=" use-btn "><span class="btn-text">立即领取</span></div>';	
}


var html = '<div class="fuli-bao-wrap">'+
'<div mx-guid="g3mx_3" class="fuli-bao-content" id="mx_10">'+
'<div mx-guid="g4mx_3" class="rights-info">'+
'<p class="title">商品福利</p>'+ lijinhtml + quanhtml +
'<div class="coupon-info-tip tqk-pt-10">您今天还可以领'+ data.limit +'次淘礼金红包</div>'+btnhtml+
'<div class="radius-left-icon"></div>'+
'<div class="radius-right-icon"></div>'+
'<div class="trangle-down"></div>'+
'</div>'+
'<div class="product-info">'+
'<div class="radius-left-icon"></div>'+
'<div class="radius-right-icon"></div>'+
'<div class="product-info-title">福利仅限以下商品使用</div>'+
'<a class="product-info-detail" href="'+data.url+'"><img src="'+ data.pic_url +'" alt="">'+
'<div class="detail-right">'+
'<p class="short-name">'+data.title+'</p>'+
'<p class="basic-info"><span>现价￥'+data.price+'</span><span>已售'+data.volume+'件</span></p>'+ fulihtml
'</div>'+
'</a>'+
'</div>'+
'</div>'+   	
'</div>';

 layer.open({
  type: 1,
  title: false,
  closeBtn: 2,
  shade: 0.8,
  skin: 'myskin',
  area: ['90%', 'auto'],
  shadeClose: true,
  content: html,
  success: function(layero, index){
$("#canvasClose").on("click",function(){
layer.msg('数据获取中', {
  icon: 16
  ,shade: 0.01
});	
     $.ajax({
                url: '/index.php?m=m&c=taolijin&a=chalijin',
                type: 'post',
                dataType : 'json',
                data: {
                    numiid : data.num_iid,
                },
                success: function(res) {
                	
                	if(res.msg){
                		layer.alert(res.msg)
                		return false;
                	}
                 
 if(res.code == 200 && res.content){
 layer.closeAll();
var copyhtml = '<div class="box-kouling" style="background: #FFFFFF; padding-top: 20px;border-radius: 10px;">'+
'<img src="/Public/skin2/static/home/images/copy-lijin.png?v=1" width="100%" />'+
'<div class="copy-link">'+
 '<div class="code-android">'+
 '<textarea class="code" id="content" >'+res.content+'</textarea>'+
 '<div style="margin-left: -600px; height: 1px; overflow: hidden;">'+
'<input id="code" value="'+res.content+'" readonly="readonly" />'+
'</div>'+
 '</div>'+
'<button  data-clipboard-target="#code" data-clipboard-action="copy" class="layui-btn layui-btn-radius layui-btn-danger copycode2">一键复制</button>'+
'</div>'+
'</div>';

 layer.open({
  type: 1,
  title: false,
  closeBtn: 2,
  shade: 0.8,
  skin: 'myskin',
  area: ['90%', 'auto'],
  shadeClose: true,
  content: copyhtml,
  success: function(layero, index){

var clipboard2 = new ClipboardJS('.copycode2');
clipboard2.on('success', function(e) {
layer.msg('复制淘口令成功，现在去打开【手机淘宝APP】下单吧！'); 
    e.clearSelection();
});
clipboard2.on('error', function(e) {
layer.msg('复制失败'); 
});
	
  }
})
          	
                	
                }
                
                 
                }
            });
		
		
	})
								


								
								
								
								
									},
});
	

}
else{
	             
 layer.msg(data.msg)
}
  $("#content").val("");
},
error:function(e){
layer.alert("操作失败,请刷新页面重试！")
},

});
   	 return false; 
   
  });
  
$("#getCouponBtn").on("click",function(){
	layer.open({
		type: 1,
		title: false,
		area: '100%',
		offset: '0px',
		shadeClose: true,
		closeBtn: 0,
		scrollbar: false,
		fixed: true,
		content: $(".box-kouling"),
		skin: 'layui-layer-nobg',
		success: function(layero, index) {
			$("html,body").addClass("lock")
			$(".close-kouling").on("click", function() {
				layer.close(index)
			})
		}
	});							
});
  	
$("#silde").children("#scroll").on('click',function(){$("html,body").animate({scrollTop:0},500)}); 	

  });

  exports('tqk', {}); 
})