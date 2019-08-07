jQuery(function($){

	$(document).ready(function(){
		
		
		
		$(".tab_content_login").hide();
		$("ul.tabs_login li:first").addClass("active_login").show();
		$(".tab_content_login:first").show();
		$("ul.tabs_login li").click(function() {
			$("ul.tabs_login li").removeClass("active_login");
			$(this).addClass("active_login");
			$(".tab_content_login").hide();
			var activeTab = $(this).find("a").attr("href");
			if ($.browser.msie) {$(activeTab).show();}
			else {$(activeTab).show();}
			return false;
		});
		
		
		
		$( ".mv-button-show" ).click(function() {
			
			var listId = $(this).data('filelist');
			
			$( "#" + listId ).toggle( 'fold', '', 500 );
			$(this).hide();
			$(this).next().show();
			
			return false;
			
		});
		
		
		$( ".mv-button-hide" ).click(function() {
			
			var listId = $(this).data('filelist');
			
			$( "#" + listId ).toggle( 'fold', '', 500 );
			$(this).hide();
			$(this).prev().show();
			
			return false;
			
		});
		
		
	});	
}); 
