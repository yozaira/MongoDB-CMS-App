
$(document).ready(function() {
     // add a function that captures the click of any of our delete post links in our application
     
     // By using live, jQuery allows us to define a selector and applies a rule to all current and future 
     // items that match that selector. 
	 // PHP and CouchDB Development, page 233
	 
    $('.delete').live( 'click', function(event){
         event.preventDefault();  // prevents the link from taking us to a new page, as it normally does
         
         // save the href attribute of the clicked link into a variable called location, so that we can use it in our AJAX call:
         // var location = $(this).attr('href');
         
       // create a basic AJAX request that will call our application and delete the post for us
       $.ajax({
          // means that we want to use the DELETE HTTP method for our request.
          type: 'DELETE',  
           // means that we are going to use the href attribute of the clicked link for our request. 
           // This will make sure the correct post is deleted.
           url: $(this).attr('href'),
           // is the object that will be used for all AJAX callbacks. So, in this example, all the code that is inside the 
           // success option of this call will use the clicked link as the context for all calls.
           context: $(this),
           // is called whenever our AJAX request is complete.
           success: function(){
           // This means that we're going to look two HTML levels up from the clicked link. This means that we're going to look 
           // for <div class="post-item row"> of the post, and we're going to fade it out of view.
           $(this).parent().fadeOut();
           
           // made our #post_count element dynamic so that each time a post is deleted, the post count changes accordingly.
           $('#post_count').text(parseInt($('#post_count').text()) -1);
           
           },         
           // is run whenever an error occurs in your code. Right now, we're just displaying an alert box, which is not the most 
           // elegant approach, especially since we aren't supplying the user with details of what happened.
           error: function (request, status, error) {
           alert('An error occurred, please try again.');
           }
           
       });
       
    });
 
    
    $('#more_posts').bind( 'click', function(event){
      event.preventDefault();
      // In order for us to call the /user/:username/:skip route, we will need to use a JavaScript function 
      // called window.location.pathname to grab the current URL of the page. Then, we'll append the number 
      // of post items at the end of the string so that we skip the number of posts that are currently displayed on the page.
      
       var location = window.location.pathname + "/" + $('#post_list').children().size();
       $.ajax({
              type: 'GET',
              url: location,
              context: $('#post_list'),
              success: function(html){
                 $(this).append(html);
                 if ($('#post_list').children().size() <= parseInt($('#post_count').text())) {
                     $('#load_more').hide();
                 }

              },
              error: function (request, status, error) {
              alert('An error occurred, please try again.');
              }
       });
       
     });
    
    
});

// Now that we are correctly using DELETE as our HTTP method through our AJAX call, we need to update our routes, 
// so our code knows how to handle the route.



