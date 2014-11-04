/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$(document).ready(function(){
    var block = $('#blockcartpopup_footer_hook');
    $(document).ajaxComplete(function( event, xhr, settings ) {
        var data = settings.data;
        if((data.indexOf("controller=cart") > -1) && (data.indexOf("add=1") > -1) && (data.indexOf("ajax=true") > -1)) {
            $.fancybox(block,
            {
                maxWidth	: 840,
		maxHeight	: 400,
		fitToView	: false,
		width		: '70%',
		height		: '70%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
            });
        }
    });
    $(document).on('click', '#blockcartpopup_footer_hook a.closefancybox', function(){
        $.fancybox.close();
        return false;
    });
});