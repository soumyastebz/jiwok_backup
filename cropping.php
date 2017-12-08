<?php

/**************************************************************************** 

   Project Name	::> Jiwok 

   Module 		::> Admin-image cropping tool

   Programmer	::> Georgina

   Date			::> 23/06/2015

   

   DESCRIPTION	::::>>>>
   
   Image cropping tool


*****************************************************************************/

	include_once('includeconfig.php');
    $heading = "Image cropping tool";

?>

<HTML><HEAD><TITLE><?=$admin_title?></TITLE>

<? include_once('metadata.php');?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script src="js/image_crop/jquery.cropit.js"></script>
<script>
	$.noConflict();
      jQuery(function() {
		jQuery('.image-editor').cropit({
		  exportZoom: 4.00,
          imageBackground: true,
          allowDragNDrop:true,
          maxZoom:1.5,
          imageBackgroundBorderWidth: 100,
          imageState: {
           src: 'images/default.jpg',
          },
         });
         jQuery('#export').click(function() {
		  var imageData = jQuery('.image-editor').cropit('export');
          window.open(imageData);
        });
      });
</script>
<style>
	<!----------->
      .cropit-image-preview {
        background-color: #f8f8f8;
        background-size: cover;
        border: 5px solid #ccc;
        border-radius: 3px;
        margin-top: 7px;
        width: 250px;
        height: 250px;
        cursor: move;
      }

      .cropit-image-background {
        opacity: .2;
        cursor: auto;
      }

      .image-size-label {
        margin-top: 10px;
      }

      input {
        /* Use relative position to prevent from being covered by image background */
        position: relative;
        z-index: 10;
        display: block;
      }
</style>
</HEAD>

<BODY  class="bodyStyle">


<TABLE cellSpacing=0 cellPadding=0 width="779" align="center" border="1px" bordercolor="#E6E6E6">

  <TR>

    <TD valign="top" align=left bgColor=#ffffff><? include("header.php");?></TD>

  </TR>

  <TR height="5">

    <TD valign="top" align=left class="topBarColor">&nbsp;</TD>

  </TR>

  

  <TR>

    <TD align="left" valign="top"> 

      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 class="middleTableBg">

        <TR> 

          <TD  valign="top" align=left width="175" rowSpan="2" > 

            <TABLE cellSpacing="0" cellPadding="0" width="175" border=0>

              <TR> 

                <TD valign="top">

				 <TABLE cellSpacing=0 cellPadding=2 width=175  border=0>

                    <TBODY> 

                    <TR valign="top"> 

                      <TD valign="top"><? include ('leftmenu.php');?></TD>

                    </TR>

                    

                    </TBODY> 

                  </TABLE>

				</TD>

              </TR>

            </TABLE>

          </TD>

          <TD valign="top" align=left width=0></TD>

         

        </TR>

        <TR> 

          <TD valign="top" width="1067"><!---Contents Start Here----->

		  

		  

            <TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>

              <TR> 

                <TD  width="98%" valign="top">

				

				  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">

<tr> 

                <td width="10" height="9"><img src="images/top_left.jpg" width="9" height="9"></td>

                <td width="543" height="9" background="images/top_mdl.jpg"><img src="images/top_mdl.jpg" width="9" height="9"></td>

                <td width="11" height="9"><img src="images/top_right.jpg" width="9" height="9"></td>

              </tr>

              <tr> 

                <td background="images/side1.jpg"><img src="images/side1.jpg" width="9" height="9"></td>

                <td valign="top"> 

				

				

				

				<TABLE cellSpacing=0 cellPadding=0 border=0 align="center">

                   <TR> 

                      <TD valign="top" width=564 bgColor=white> 

                       

			  			 
                      

				  <table class="paragraph2" cellspacing=0 cellpadding=0 width=553 border=0 style="padding:9%">

				    <tr>

						<td height="50" align="center" valign="bottom" class="sectionHeading"><?=$heading;?></td>

					</tr>
					<tr>
					<td>
					
					 <div class="image-editor">
	                 <div class="cropit-image-preview-container" style="margin-left: 0%;">
                     <div class="cropit-image-preview" style="height: 219.25px;width: 495px;">
                     </div>
                     </div>
                   
                     <div ><span><center><input type="range" class="cropit-image-zoom-input"></center></span></div>
                    <div>  <span style="float:left ;padding-right:19%;"><input type="file"  class="cropit-image-input " ></span>
                      <span style="float:left"><input type="button"  id="export" value="Crop it" ></span></div>
                     </div>
                      
                      </td>
					  </tr>
					</table>
					
					</TD>
					</TR> 
                </TABLE>

				  </td>

                <td background="images/side2.jpg">&nbsp;</td>

              </tr>

              <tr> 

                <td width="10" height="9"><img src="images/btm_left.jpg" width="9" height="9"></td>

                <td height="9" background="images/btm_mdl.jpg"><img src="images/btm_mdl.jpg" width="9" height="9"></td>

                <td width="11" height="9"><img src="images/btm_right.jpg" width="9" height="9"></td>

              </tr>

            </table>



                </TD>

              </TR>

            </TABLE>



          </TD>

        </TR>

		 <TR height="2">

    <TD valign="top" align=left class="topBarColor" colspan="3">&nbsp;</TD>

  </TR>

      </TABLE>

        <?php include_once("footer.php");?>

</body>

</html>
