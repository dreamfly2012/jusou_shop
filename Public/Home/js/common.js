//设置折扣
$(document).ready(function() {
    //TODO:延迟加载图片
    //$(".scrollLoading").scrollLoading();
    
    //导航设置
    $('.cateMenu').hide();
    $('.all_category').hover(function(){
        $('.cateMenu').show();
    },function(){
        $('.cateMenu').hide();
    });

    $('.cateMenu ul li').mouseover(function(){
        var i = $('.cateMenu ul li').index($(this));
        var topcss = i*30 + 'px';
        $('.list-item').css('top','0').hide();
        $(this).find('.list-item').css('top',topcss).show();
    });
    $('.cateMenu').mouseleave(function(){
        $('.list-item').hide();
    });
 
    //添加购物车
    $(".add_to_cart").click(function() {
        var goods_id = $(this).attr('data-goods-id');
        var aj = $.ajax({
            url: __ADD_TO_AJAX_CART_URL__,    
            data: {
                goods_id: goods_id,
            },
            type: 'post',
            dataType: 'json',
            success: function(data) {
                if (data.info == 0) {
                    $('#loginform').modal();
                } else if(data.info=1||data.info==2){
                    alert('添加到购物车成功');
                } else{
                    alert('添加购物车失败');
                }
            },
            error: function() {
                alert("网络异常！");
            }
        });
    });

    //添加收藏
    $(".add_to_favourite").click(function() {
        var goods_id = $(this).attr('data-goods-id');
        var aj = $.ajax({
            url: __ADD_GOODS_TO_COLLECT_URL,    
            data: {
                goods_id: goods_id,
            },
            type: 'post',
            dataType: 'json',
            success: function(data) {
                if (data.info == 0) {
                    $('#loginform').modal();
                } else if(data.info == 1) {
                    alert('您已经收藏过！');
                } else if(data.info == 2){
                    alert('添加收藏成功!');
                } else{
                    alert('添加收藏失败!');
                }
            },
            error: function() {
                alert("网络异常！");
            }
        });
    });

    
    //详情页商品数量修改
    $(".cart-plus").click(function() {
        var cart_val = $(this).prev('.cart-input').val();
        $(this).prev('.cart-input').val(parseInt(cart_val) + 1);
    });

    $(".cart-minus").click(function() {
        var cart_val = $(this).next('.cart-input').val();
        if (cart_val >= 2) {
            $(this).next('.cart-input').val(parseInt(cart_val) - 1);
        }
    });

    //返回顶部代码
    $(window).scroll(function() {
        if ($(window).scrollTop() > 100) {
            $("#back-to-top").fadeIn(1500);
        } else {
            $("#back-to-top").fadeOut(1500);
        }
    });
    //当点击跳转链接后，回到页面顶部位置
    $("#back-to-top").click(function() {
        $('body,html').animate({
            scrollTop: 0
        }, 1000);
        return false;
    });
});