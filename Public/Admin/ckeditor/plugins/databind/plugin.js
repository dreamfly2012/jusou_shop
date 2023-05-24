(function(){ 
    //Section 1 : 按下自定义按钮时执行的代码 
    var a= { 
        exec:function(editor){ 
            $(".fancybox").click();
        } 
    }, 
    //Section 2 : 创建自定义按钮、绑定方法 
    b='databind'; 
    CKEDITOR.plugins.add(b,{ 
        init:function(editor){ 
            editor.addCommand(b,a); 
            editor.ui.addButton('databind',{ 
                label:'绑定数据', 
                icon: this.path + 'logo_ckeditor.png', 
                command:b 
            }); 
        } 
    }); 
})(); 