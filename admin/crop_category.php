<!--
/*********************
 * Cropping Tool v1.0
 * Author: Deepak
 * Date: 06/08/2015
 *********************/
 -->

<html>
<head>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/cropper.min.css" rel="stylesheet">
  <style>

    body{
      font-family: 'Trebuchet MS', serif;
    }

    .container{
    /* width: 800px;
    margin: 0 auto; */
  }


  .no-js #loader { display: none;  }
  .js #loader { display: block; position: absolute; left: 100px; top: 0; }
  .se-pre-con {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url(assets/img/preloader.gif) center no-repeat #fff;
  }
  .se-pre-upload {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url(assets/img/upload-preloader.gif) center no-repeat rgba(255, 255, 255, 0.9);
  }


  .crop-box{
    max-height: 350px;
  }

  ul.tabs{
    margin: 0px;
    padding: 0px;
    list-style: none;
  }
  ul.tabs li{
    background: none;
    color: #222;
    display: inline-block;
    padding: 10px 15px;
    cursor: pointer;
  }

  ul.tabs li.current{
    background: #ededed;
    color: #222;
    font-weight: bold;
  }

  .tab-content{
    display: none;
    background: #ededed;
    padding: 15px;
  }

  .tab-content.current{
    display: inherit;
    padding-bottom: 370px;
  }
  .cropButton{
    margin-top:10px;
  }


</style>
</head>
<body>

  <div class="se-pre-upload"></div>
  <div class="se-pre-con"></div>
  <div class="container">

    <ul class="tabs tabResolutionsNav">
    </ul>
    <div class="tabResolutionsContent">

    <?php 
     
      // load basic configrations
      require_once 'config_category.php';
     $imagePath = IMAGE_PATH;
       //$imagePath = 'uploads/users/';
      //neethu
      // $crop_user_id = $_REQUEST['userId'];
        $lan_id = $_REQUEST['lan_id']; 
        $flx_id = $_REQUEST['flx_id']; 
         $langId = $_REQUEST['lanId'];
      //neethu
      if($_REQUEST['image']){
        $imageToCrop = $_REQUEST['image']; 
        $imageName1 = explode(".", $imageToCrop);
      }else{
        $imageToCrop =  '';
      }
     
      $fullPath = explode('/' ,"http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
      array_pop($fullPath);
      $fullPath = implode('/', $fullPath);
      $resolutionData = file_get_contents($fullPath.'/processor_category.php?mode=1');


      $data = json_decode($resolutionData);
      $tabId = 0;

      if($tabId == 0){
        $current = 'current';
      }else{
        $current ='';
      }

      foreach ($data as $item) {
        echo '
        <div id="tab-'.$tabId.'" class="tab-content crop-box '.$current.'">
          <div class="row">
            <div class="col-lg-6 ">
              <div class="cropper-tab-'.$tabId.' crop-box">
              <img src="'.$imagePath.$imageToCrop.'" alt="Invalid Picture">
              </div>
            </div>
          </div>
        </div>
        <div id="msg" ></div>';
        $tabId += 1;
      }

      ?>

      <!--   <img src="assets/img/pic1-1920x1080.jpg" alt="Picture"> -->
        <input type="hidden" name="lan_id" id="lan_id" value="<?php echo $lan_id; ?>">
        <input type="hidden" name="flx_id" id="flx_id" value="<?php echo $flx_id; ?>">
	  <input type="hidden" name="imageToCrop" id="imageToCrop" value="<?php echo $imageName1[0]; ?>">
   <input type="hidden" name="dbimage" id="dbimage" value="<?php echo $imageToCrop; ?>">
    <input type="hidden" name="langId" id="langId" value="<?php echo $langId; ?>">
<!--
   <input type="button" id="cropit1" value="Crop All Images!">
-->
    <input type="button" id="cropit1" value="Crop All Images!" >
    
  </div><!-- container -->

  <div>
  </div>

  <!-- jQuery & Bootstrap -->
  <script type="text/javascript" src="assets/js/jquery.min.js"></script>
  <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="assets/js/cropper.min.js"></script>
  <script type="text/javascript" src="assets/js/main_category.js"></script>
   <script type="text/javascript" src="assets/js/canvas-to-blob.min.js"></script>

</body>
</html>
