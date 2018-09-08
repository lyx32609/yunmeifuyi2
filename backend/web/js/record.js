$(function(){
   $(".staff_tit a").first().addClass('current');
   $(".staffmain").first().show();
   $(".staff_tit a").click(function(){
	  $(this).addClass("current").siblings().removeClass("current");
	  var num=$(this).index();
	  $(".staffmain").eq(num).stop().show().siblings().stop().hide();
	   showChart();	
	  });
	 
})