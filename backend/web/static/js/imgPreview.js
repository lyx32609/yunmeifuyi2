$(function(){
    $("table tr td").on('click','img.img_box',function(){
        $(".imgCopy").css('display','block');
        $(".imgCopy").append($(this).clone());
    });
    $(".imgCopy").on('click',function(){
        $(this).empty();
        $(this).css('display','none');
    })
})