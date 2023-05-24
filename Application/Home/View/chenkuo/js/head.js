/*品牌列表*/
var brandArea = function () {
    $(".brand_class li").each(function (i) {
        $(this).mouseenter(function () {
            $(this).find("div").stop().animate({ "top": -50 }, 300);
        })
        $(this).mouseleave(function () {
            $(this).find("div").stop().animate({ "top": 0 }, 300);
        })
    });
}


$(function () {
    //搜索
    $("#btnsch").click(function () {
        window.location = SiteUrl + '/product/search_jump.htm?key=' + encodeURIComponent($("#sch").val());
    });

    //读取购物车数量
    var dd = new Date();
    $("#head_cart_no").load("/ajax/head/shoppingcart.htm?act=getcount&d=" + escape(dd));

    //head 弹出菜单部分
    var cateMenu = function () {
        var cateLiNum = $(".cateMenu li").length;
        $(".cateMenu li").each(function (index, element) {
            if (index < cateLiNum - 1) {
                $(this).mouseenter(function () {
                    var ty = $(this).offset().top - 250;
                    var obj = $(this).find(".list-item");
                    var sh = document.documentElement.scrollTop || document.body.scrollTop;
                    var oy = ty + (obj.height() + 30) + 236 - sh;
                    var dest = oy - $(window).height()
                    if (oy > $(window).height()) {
                        ty = ty - dest - 10;
                    }
                    if (ty < 0) ty = 0;
                    $(this).addClass("on");
                    obj.show();
                    $(".cateMenu li").find(".list-item").stop().animate({ "top": ty });
                    obj.stop().animate({ "top": ty });
                })
                $(this).mouseleave(function () {
                    $(this).removeClass("on");
                    $(this).find(".list-item").hide();
                })
            }
        });

        $(".navCon_on").hover(function () {
            $(".cateMenu").show();
        },
		function () {
		    $(".cateMenu").hide();
		})

    } ();

    var miniMenu = function () {
        /*购物列表*/
        $(".miniMenu").find(".m1").hover(
			function () {
			    $(this).addClass("on");
			    $(this).find(".mini-cart").show();
			    var dd = new Date();
			    $("#head_cart").load("/ajax/head/shoppingcart.htm?act=getitems&d=" + escape(dd));
			},
			function () {
			    $(this).removeClass("on");
			    $(this).find(".mini-cart").hide();
			}
		)
        /*用户中心*/
        $(".miniMenu").find(".m3").hover(
			function () {
			    $(this).addClass("cur");
			    $(this).find(".miniMenu-child").show();
			},
			function () {
			    $(this).removeClass("cur");
			    $(this).find(".miniMenu-child").hide();
			}
		)
    } ();


//    /*topBar置顶*/
//    var positionMenu = function (id) {
//        var mc = document.getElementById(id);
//        var minNumber = mc ? mc.offsetTop : 0 ;
//        var isIE6 = navigator.appVersion.indexOf("MSIE 6") > -1;
//
//        $(window).scroll(function () {
//            var sh = document.documentElement.scrollTop || document.body.scrollTop;
//            var th = document.documentElement.clientHeight;
//            if (sh > minNumber) {
//                mc.style.position = !isIE6 ? "fixed" : "absolute";
//                mc.style.top = !isIE6 ? "0px" : sh + "px";
//            } else {
//                mc.style.position = "static";
//                mc.style.top = minNumber + "px"
//            }
//        })
//    } ("topBar")
})