(function($){
    'use strict';
    $(document).on("change",".wpaptw_post_types_box",function(){
    	var post_type=$(this).val();
    	$.get(ajaxurl,{action:'wpaptw_get_category',post_type:post_type},function(res){
    		$(".wpaptw_category_combo").html(res);
    	});
    });
})(jQuery);