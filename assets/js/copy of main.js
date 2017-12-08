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
    dataCount = data.length
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



// Upload cropped image to server
$('.cropper-tab-0 > img').cropper('getCroppedCanvas').toBlob(function (blob) {
  //var formData = new FormData();

  //formData.append('croppedImage', blob);
  //console.log(blob);
/*
      var data = new FormData();
      data.append('file', blob);
      $.ajax({
        url :  "uploader.php",
        type: 'POST',
        data: data,
        contentType: false,
        processData: false,
        success: function(response) {
          console.log(response);
          alert("boa!");
        },    
        error: function() {
          alert("not so boa!");
        }
      });
*/
 window.imageBlob = '';
 var reader = new window.FileReader();
 reader.readAsDataURL(blob); 
 reader.onloadend = function() {
                base64data = reader.result;   
                imageBlob = base64data;
                console.log(base64data );
  }

/*
// Create Base64 Object
var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}

// Encode the String
var encodedString = Base64.encode('abced');
console.log(encodedString); // Outputs: "SGVsbG8gV29ybGQh"
*/
alert(1);
console.log('data:- '+imageBlob);
  $.ajax('uploader.php', {
    method: "POST",
    data: {'imageBlob': imageBlob},
    success: function (response) {
      console.log(response);
      console.log('Upload success');
    },
    error: function () {
      console.log('Upload error');
    }
  });


});


});


//---

});

$(window).load(function() {
    $("#tab-link-0").trigger("click");

$(".se-pre-con").fadeOut("slow");;

  })
