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
       // htmlData += '<li role="presentation" class="'+isActive+'"><a href="#tab'+tabId+'" aria-controls="tab'+tabId+'" role="tab" data-toggle="tab">'+item.name+'</a></li>';
     htmlData += '<li id="tab-link-'+tabId+'" class="tab-link '+isActive+'" data-tab="tab-'+tabId+'">'+item.name+'</li>';
       // htmlData += '<li role="presentation" class="'+isActive+'"><a href="#tab'+tabId+'" aria-controls="tab'+tabId+'" role="tab" data-toggle="tab">'+item.name+'</a></li>';
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
   //htmlData += '<div class="cropper-tab-'+tabId+' crop-box"><img src="assets/img/pic1-1920x1080.jpg" alt="Picture"></div>';

     // htmlData += '<div role="tabpanel" class="tab-pane fade '+isActive+'" id="tab'+tabId+'"><div class="cropper-tab-'+tabId+' crop-box"><img src="assets/img/pic1-1920x1080.jpg" alt="Picture"></div></div>';
    
  htmlData += '<div id="tab-'+tabId+'" class="tab-content crop-box '+isActive+'"><div class="row"><div class="col-lg-6 "><div class="cropper-tab-'+tabId+' crop-box"><img src="assets/img/pic1-1920x1080.jpg" alt="Picture"></div></div></div></div>';


    //  htmlData += '<div role="tabpanel" class="tab-pane fade '+isActive+'" id="tab'+tabId+'"><div class="row"><div class="col-lg-6 "><div class="cropper-tab-'+tabId+' crop-box"><img src="assets/img/pic1-1920x1080.jpg" alt="Picture"></div></div></div></div>';
      tabId += 1;

    });
   // $('.tdata').append(htmlData);
  // $('.tabResolutionsContent').append(htmlData);



  }
});

$(document).on('click','.checkResolution',function(){
  $('.resolutionCheck:checked').each(function(){
    //console.log($(this).val());

  });
});

/*
$(document).on('click', '#submitUser', function(e) {
       var fname = $("#fname").val();
       $("#theresult").text(fname);
       e.preventDefault();
    });
*/


  $('ul.tabs li').click(function(){
    var tab_id = $(this).attr('data-tab');

    $('ul.tabs li').removeClass('current');
    $('.tab-content').removeClass('current');

    $(this).addClass('current');
    $("#"+tab_id).addClass('current');
  })





//----
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




//---

$(document).on('click', '.cropButton', function(){

var tabId = 0;
var asyncCalls;

$(coreData).each(asyncCalls = function(index, item){ // loop

var imageName = 'img-'+tabId;
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
                  data: {'imageBlob': imageBlob, 'imageWidth': item.width, 'imageHeight': item.height, 'imageName': imageName},
                  async: false,
                  success: function (response) {

                    console.log('Upload success');
                  },
                  error: function () {
                    console.log('Upload error');
                  }
                });
              }
            });

tabId += 1;
});// end loop

asyncCalls.done( function() {
   alert("sadasassa");
}).fail( function(x, s, e) {
   alert(s+": "+e);
});


}); // end crop button


//---

});

$(window).load(function() {
    $("#tab-link-0").trigger("click");

$(".se-pre-con").fadeOut("slow");;

  })
