<?php

	session_start();

	$url="http://www.jiwok.com/";

	$lanfl="0";

	if($_SESSION['language']['langId']=='1')

	{

		$url.="en/";

		$lanfl="1";

	}

	if($lanfl!='0')

	{

		 $pattern = "/(\/)(en)(\/)/";

		 $url_execute=$_SERVER['REQUEST_URI'];

		 $check_val=preg_match($pattern,$url_execute);

		 if(!$check_val)

		 {

		  header("Location:".$url."giftafterpayment.php");exit;  

		 }

	}

	include_once('includeconfig.php');

		// create an object for manage the parsed content

		$parObj 		=   new Contents('userreg1.php');
		$objGen   		=	new General();
		$dbObj     		=   new DbAction();	
		// This takes the admin back to the admin panel after registering a member from the admin.
		if(isset($_SESSION['adm_id']) && isset($_SESSION['admin_user']))
		{

		header("location:http://www.jiwok.com/jiwokv2/admin/list_members.php?status=success_add");
		exit;
		}

		//if(!isset($_SESSION['user']['userId']))
		//{		header("location:index.php"); exit;		}

		if($lanId=="") $lanId=1;
		//collecting data from the xml for the static contents
		$returnData		= $parObj->_getTagcontents($xmlPath,'afterpayGift','label');
		$arrayData		= $returnData['general'];
		if(isset($_POST['submit'])){
		header("Location:index.php");
		}
?>

<?php
include("header.php");
?>
<!--
<style>
#contentAreaInnerContents {
    background:#fff;
    clear: both;
    margin: 0 auto 25px;
    min-height: 400px;
    *width:993px;
	#width:993px;
	_width:993px;
	width: 1006px;
	float:left;
}
.left_col_results {
   
    float: left;
    margin: 0;
    padding: 0;
    width: 570px;
}
.Giftcert_Top_Bg {
    background-image: url("../images/gift_cert_top_BG.jpg");
    background-repeat: no-repeat;
    float: left;
    height: 143px;
    padding-top: 7px;
    width: 527px;
}
.Giftcert_Btm_Bg {
    background-image: url("../images/gift_cert_btm_BG.jpg");
    background-repeat: no-repeat;
    float: left;
    height: 13px;
    width: 527px;
}
.btnNew{
    background: url("images/buttons_ylow.png") no-repeat scroll 0 -159px transparent;
    border: 0 none;
    color: #FFFFFF;
    cursor: pointer;
    font: bold 14px "Trebuchet MS";
    height: 31px;
    width: 358px;
}
#spanNew{
    color: #6699CC;
    font-family: Arial,Helvetica,sans-serif;
    font-size: 13px;
    font-weight: bold;
    height: 116px;
    padding: 34px 15px 0;
    text-align: center;
    width: 497px;
}
#HeadDivNew{
    color: #6699CC;
    font-family: Arial,Helvetica,sans-serif;
    font-size: 16px;
    font-weight: bold;
    padding: 15px;
    text-align: center;
    width: 497px;
}
#topHead{
    background: url("images/heding_bg.png") no-repeat scroll center top transparent;
    color: #FFFFFF;
    font: bold 16px "Trebuchet MS";
    height: 44px;
    margin-top: 24px;
    padding-left: 57px;
    padding-right: 20px;
    padding-top: 10px;
    position: relative;
}
</style>
-->
<!--original----->

<!--
<div id="contentAreaInnerContents">
  <div id="pageHead">
  	<div id="topHead"><?=$parObj->_getLabenames($arrayData,'leftheadtitle','name');?></div>
  </div>
  <div class="left_col_results"  style="margin-left:210px;">
	<div class="Giftcert_Main_Container" id="HeadDivNew">
<b><?=$parObj->_getLabenames($arrayData,'lefttitle','name');?></b>
	</div>
	<span class="Giftcert_Top_Bg" id="spanNew">
	<?=$parObj->_getLabenames($arrayData,'message','name');?>
	<br /><br/>
	<form name="giftafterpayForm" action="giftafterpayment.php" method="post" >
    <input type="submit" name="submit" value="<?=$parObj->_getLabenames($arrayData,'nextstep','name');?>" class="btnNew" />
	</form>
	</span>
	<span class="Giftcert_Btm_Bg"></span>
  </div>
</div>
-->

<!--original ends----->

 <div class="frame_inner" id="contentAreaInnerContents">
    <div class="row-1">
         <div class="title">
                <div id="pageHead"> <div id="topHead"><p class="Q"> <b><?=$parObj->_getLabenames($arrayData,'leftheadtitle','name');?></b></p></div></div><br><br>
           <div id="HeadDivNew"><p ><b><?=$parObj->_getLabenames($arrayData,'lefttitle','name');?></b></p></div>
      </div>
         </div>
       <div class="clear"></div>
       <div class="partners_JW">
        <div id="spanNew"><p class="TX_ylw" style="font-size: 14.39px;"><?=$parObj->_getLabenames($arrayData,'message','name');?></p></div>
       </div>  
       
    
    
    
       <form name="giftafterpayForm" action="giftafterpayment.php" method="post" >
    <input type="submit" name="submit" value="<?=$parObj->_getLabenames($arrayData,'nextstep','name');?>" class="btn_orng" />
	</form>
	
	
	
	
     </div>


<?php include('footer.php');?>

