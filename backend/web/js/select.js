$.fn.divSelect = function(){
	return this.each(function(index){
		var $this = $(this),
		$cite = $this.find("cite"),    //选择 div
		$list = $this.find("ul"),		//选中ul
		$input = $this.find(".group_select");	 //获取input 选中的值

		$this.on("click","cite",function(){
		    $list.is(":hidden") ? $list.slideDown("fast") : $list.slideUp("fast");
		    return false
        });
		$list.on("click","a",function(){
			var $this = $(this);
			$cite.text($this.text());
			$input.val($this.attr("selectid"));
			$list.hide();
			return false
		});
		$(document).on("click.select"+index,function(){
		    $list.hide();
		});
   })
};

$(function(){
//调用 支持jquery连缀语法
    $(".group_select").divSelect();
    $(".groupsub").first().css('display','block');
    $(".group_select ul li").on('click',function(){
    	var num=$(this).index();
    	$(".groupsub").eq(num).stop().css('display','block').siblings().stop().css('display','none');    	
    	$('body,html').animate({scrollTop:0},0);        
        ajaxpost();
    })

});