//设置折扣



$(document).ready(function(){
    //批量编辑checkbox
    $('.check_all').on('click',function(){
        if($(this).prop('checked')==true){
            $(".single_check").prop('checked','checked');
        }else{
            $(".single_check").prop('checked','');
        }
    });
    

    //保证后台页面左右高度一致
    if($(".right_content").height()<1200){
        $(".right_content").height(1200);
    }
    $(".left_content").height($(".right_content").height());
});



