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
  url: 'processor_category.php',
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
      htmlData += '<div id="tab-'+tabId+'" class="tab-content crop-box '+isActive+'"><div class="row"><div class="col-lg-6 "><div class="cropper-tab-'+tabId+' crop-box"><img src="crop/assets/img/pic1-1920x1080.jpg" alt="Picture"></div></div></div></div>';
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
});



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
$('#cropit1').click(function(){  
 

  $(".se-pre-upload").fadeIn("slow");

  var tabId = 0;
  var successCount = 0;
  var lan_id       = $('#lan_id').val(); 
  var flx_id       = $('#flx_id').val(); 
  var langId = $('#langId').val();
$(coreData).each(function(index, item){ 
	var imageName = $('#imageToCrop').val()+'img-'+item.width+'x'+item.height;
   //var imageName  = $('#imageToCrop').val();
      var dbimage  = $('#dbimage').val();
  var selectorId = '.cropper-tab-'+tabId+' > img';

  console.log('ID: '+selectorId);

$(selectorId).cropper('getCroppedCanvas').toBlob(function (blob) { 
 window.imageBlob = '';
 var reader = new window.FileReader();
 reader.readAsDataURL(blob); 
 reader.onloadend = function() {
  base64data = reader.result;   
  imageBlob = base64data; 
  console.log(base64data ); 
  
  $.ajax('uploader_category.php', {
    method: "POST",
    data: {'imageBlob': imageBlob, 'imageWidth': item.width, 'dbimage': dbimage,'imageHeight': item.height, 'imageName': imageName,'lan_id':lan_id,'flx_id':flx_id},
    async: false,
    success: function (response) { //alert(response);return false;
		 window.location.href = "http://beta.jiwok.com/admin/program_cat_img.php?langId="+langId;

     
     
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

});

