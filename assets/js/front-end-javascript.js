(function($){
    function findexistids(){
        var ids = adminlocaljs.ids
        console.log(ids)
        for(var k in ids){
            var post = ids[k]
            if($('#'+post.title+" a").length>0){
                $("body").on("click",'#'+post.title+" a",function(e){
                    console.log('working')
                    //e.preventDefault()
                    postData = {
                        action:'cj_add_visit_label',
                        postid: k
                    }
                    $.ajax({
                        url: adminlocaljs.ajaxUrl,
                        type:'post',
                        data:postData,
                        beforeSend: function() {
                            //$('.loader').show()
                        },
                        success:function(responce){
                            //$('.loader').hide()
                        },
                        error:function(error){
                            console.log(error);
                        }
                    })
                })
            }
            if($('#'+post.title+" .elementor-custom-embed-play").length>0){
                $("body").on("click",'#'+post.title+" .elementor-custom-embed-play",function(e){
                    //e.preventDefault()
                    postData = {
                        action:'cj_add_visit_label',
                        postid: k
                    }
                    $.ajax({
                        url: adminlocaljs.ajaxUrl,
                        type:'post',
                        data:postData,
                        beforeSend: function() {
                            //$('.loader').show()
                        },
                        success:function(responce){
                            //$('.loader').hide()
                        },
                        error:function(error){
                            console.log(error);
                        }
                    })
                })
            }
        }
    }
    $('document').ready(function(){
        findexistids()
    })
})(jQuery);