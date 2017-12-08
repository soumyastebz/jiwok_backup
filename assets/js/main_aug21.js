/*********************
 * Cropping Tool v1.0
 * Author: Deepak
 * Date: 06/08/2015
 *********************/

$(document).ready(function(){ 

// global variables
window.dataCount = 0;
window.coreData = 0;

$.ajax({
  url: 'processor.php',
  type: 'POST',
  async: false,
  data: {'mode': 1},
  success: function(response){
    var data = $.parseJSON(response);
    dataCount = data.length;
    coreData = data;

      //append tab nav
      var htmlData = '';
      var tabId = 0;
      $(data).each(function(index, item){
        var isActive = '';
        if(tabId == 0){
          isActive = 'current';
        }
        htmlData += '<li id="tab-link-'+tabId+'" class="tab-link '+isActive+'" data-tab="tab-'+tabId+'">'+item.name+'</li>';
        tabId += 1;
      });
      $('.tabResolutionsNav').append(htmlData);

    //append tab content
    var htmlData = '';
    var tabId = 0;
    $(data).each(function(index, item){
      var isActive = '';
      if(tabId == 0){
        isActive = 'active';
      }
      htmlData += '<div id="tab-'+tabId+'" class="tab-content crop-box '+isActive+'"><div class="row"><div class="col-lg-6 "><div class="cropper-tab-'+tabId+' crop-box"><img src="assets/img/pic1-1920x1080.jpg" alt="Picture"></div></div></div></div>';
      tabId += 1;
    });

  }
});



$('ul.tabs li').click(function(){
  var tab_id = $(this).attr('data-tab');

  $('ul.tabs li').removeClass('current');
  $('.tab-content').removeClass('current');

  $(this).addClass('current');
  $("#"+tab_id).addClass('current');
})



var tabId = 0;
$(coreData).each(function(index, item){

  $('.cropper-tab-'+tabId+' > img').cropper({
    aspectRatio: item.width / item.height,
    autoCropArea: 0.65,
    strict: false,
    guides: false,
    highlight: false,
    dragCrop: false,
    cropBoxMovable: true,
    cropBoxResizable: false

  });

  tabId += 1;
});
$('#cropit').click(function(){
	
//~ alert("jj");return false;
//~ });
//~ $(document).on('click', '.cropButton', function(){ 

  $(".se-pre-upload").fadeIn("slow");;

  var tabId = 0;
  var successCount = 0;
  var crop_user_id       = $('#crop_user_id').val();
$(coreData).each(function(index, item){ // loop
// var imageName = 'img-'+item.width+'x'+item.height;
   var imageName  = $('#imageToCrop').val(); 
   
  //var imageName  = 'abc';
  //~ var imageName = <?php echo $currentImage?>;
  var selectorId = '.cropper-tab-'+tabId+' > img';

  console.log('ID: '+selectorId);
// Upload cropped image to server
$(selectorId).cropper('getCroppedCanvas').toBlob(function (blob) {
 window.imageBlob = '';
 var reader = new window.FileReader();
 reader.readAsDataURL(blob); 
 reader.onloadend = function() {
  base64data = reader.result;   
  imageBlob = base64data;
  console.log(base64data );
 
  $.ajax('uploader.php', {
    method: "POST",
    data: {'imageBlob': imageBlob, 'imageWidth': item.width, 'imageHeight': item.height, 'imageName': imageName,'crop_user_id':crop_user_id},
    async: false,
    success: function (response){  
		console.log($(".test1").html());
		$('#msg').html("success");
		  //~ $.ajax({ 
            //~ type	: "POST",
            //~ url	    : "edit_profile.php",
            //~ data	: "$userId="+crop_user_id,
            //~ dataType:"html",
           //~ success: function(data1)
          //~ {
			  //~ alert("here");return false;
//~ 
             //~ $('#newww').html(data1);
             //~ $("#modes").hide();
          //~ 
           //~ }        
           //~ });
      successCount += 1;
      console.log('Upload success');
      console.log('Response: '+response); 
      // all uploads completed
      if(successCount == dataCount){
       $(".se-pre-upload").fadeOut("slow");
      
        
      
     }  
     //window.location.href = "http://10.0.0.8/jiwokv3/edit_profile.php";
     //window.top.location.href = "http://www.example.com"; 
   },
   error: function () {
    console.log('Upload error');
  }
});
}
});

tabId += 1;
});// end loop


}); // end crop button

}); // end document.ready

$(window).load(function() {
  $("#tab-link-0").trigger("click");

  $(".se-pre-upload").fadeOut("slow");;
  $(".se-pre-con").fadeOut("slow");;

})

