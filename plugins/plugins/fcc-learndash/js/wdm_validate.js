jQuery(document).ready(function(){

    jQuery('#wdm_course_submit').click(function(){
       var title = jQuery('input[name="title"]').val();
       if(title == "" || title == null){
           alert(wdm_validate_object.wdm_enter_title);
           jQuery('input[name="title"]').focus();
           jQuery( "#accordion" ).accordion( "option", "active", 0 );
           
           return false;
       }
       // Regular Expression to remove HTML Tags
            var regX = /(<([^>]+)>)/ig;
        //  WordPress auto added  Your Wp editor id+"_ifr"
            htmlcon = jQuery('#wdm_content_ifr').contents().find("body").html();	

        // Replace HTML 

           description =  htmlcon.replace(regX, "");

        // check character limit

   // if(description.length == 0)
   //  {
      
   //         alert('Please Enter description');
   //         jQuery( "#accordion" ).accordion( "option", "active", 0 );
   //         return false;
   //     }
    });
    jQuery('#wdm_lesson_submit').click(function(){
       var title = jQuery('input[name="title"]').val();
       if(title == "" || title == null){
           alert(wdm_validate_object.wdm_enter_title);
           jQuery('input[name="title"]').focus();
          jQuery( "#accordion" ).accordion( "option", "active", 0 );
           
           return false;
       }
        // Regular Expression to remove HTML Tags
            var regX = /(<([^>]+)>)/ig;
        //  WordPress auto added  Your Wp editor id+"_ifr"
            htmlcon = jQuery('#wdm_content_ifr').contents().find("body").html();	

        // Replace HTML 

           description =  htmlcon.replace(regX, "");

        // check character limit

   // if(description.length == 0)
   //  {
   //         alert('Please Enter description');
   //         jQuery( "#accordion" ).accordion( "option", "active", 0 );
   //         return false;
   //     }
       
       
    });
    jQuery('#wdm_topic_submit').click(function(){
       var title = jQuery('input[name="title"]').val();
       if(title == "" || title == null){
           alert(wdm_validate_object.wdm_enter_title);
           jQuery('input[name="title"]').focus();
          jQuery( "#accordion" ).accordion( "option", "active", 0 );
           
           return false;
       }
       // Regular Expression to remove HTML Tags
            var regX = /(<([^>]+)>)/ig;
        //  WordPress auto added  Your Wp editor id+"_ifr"
            htmlcon = jQuery('#wdm_content_ifr').contents().find("body").html();	

        // Replace HTML 

           description =  htmlcon.replace(regX, "");

        // check character limit

   // if(description.length == 0)
   //  {
   //         alert('Please Enter description');
   //         jQuery( "#accordion" ).accordion( "option", "active", 0 );
   //         return false;
   //     }
       
       
    });
    jQuery('#wdm_quiz_submit').click(function(){
       var title = jQuery('input[name="title"]').val();
       if(title == "" || title == null){
           alert(wdm_validate_object.wdm_enter_title);
           jQuery('input[name="title"]').focus();
          jQuery( "#accordion" ).accordion( "option", "active", 0 );
           
           return false;
       }
       // Regular Expression to remove HTML Tags
            var regX = /(<([^>]+)>)/ig;
        //  WordPress auto added  Your Wp editor id+"_ifr"
            htmlcon = jQuery('#wdm_content_ifr').contents().find("body").html();	

        // Replace HTML 

           description =  htmlcon.replace(regX, "");

        // check character limit

   // if(description.length == 0)
   //  {
   //         alert('Please Enter description');
   //         jQuery( "#accordion" ).accordion( "option", "active", 0 );
   //         return false;
   //     }
       
       
    });
    jQuery('[name="featured_image"]').on('change',function (e) {
          if(this.files[0] != '') {
            var type = this.files[0].type;
            if(type != 'image/jpeg' && type != 'image/png'){
                alert('Only .jpg and .png extensions are allowed');
                jQuery(this).attr('value','');
            }
        
     }
           
       });
    
    jQuery('#wdm_test_form').on('submit', function(){
        //    WDM Test Code
        window.onbeforeunload = null;
    })
});