<?php
/**************************************************************************** 
   Project Name	::> Jiwok 
   Module 	::> Admin-CMS Management
   Programmer	::> Ajith
   Date		::> 05/01/2009
   
   DESCRIPTION::::>>>>
   Admin can edit the faqs .. 
*****************************************************************************/
	include_once('includeconfig.php');
	include_once('../includes/classes/class.CMS.php');
	include_once('../FCKeditor/fckeditor.php');
	
	$heading = "Content management";
	$errorMsg	=	array();
	//---------code for polish admin view 
	if($page_name!= "")
	{
		include_once('admin_languageProvision.php');
	}
	else
	{
		if($_REQUEST['langId'] != 0)
		{
			$lanId = $_REQUEST['langId'];
		}
		else
		{
			$lanId = 1;
		}
	}
	//echo $_REQUEST['permsnMsg'];exit;
	
	if(base64_decode($_REQUEST['permsnMsg']) != ''){ 
		$permsnMsg = base64_decode($_REQUEST['permsnMsg']);
	}
	/* Take all the languages to an array. */
	$languageArray = $siteLanguagesConfig;
						 
	/*
	 Instantiating the classes.
	*/
	$objContents = new CMS($lanId);
	$objGen   	 =	new General();
	
	if($_POST['title']){
		$contentTitle			=	$_POST['title'];
		$contentDisplayTitle	=	$_POST['displayTitle'];
		$body					=	$_POST['content'];
		$metaKeywords			=	$_POST['metaKeywords'];
		$browserTitle			=	$_POST['browserTitle'];
		
	}else
		$contentTitle			=	'ABOUTUS';
		
					
	if($_POST['update']){
		assert(isset($_POST['metaKeywords']));
		if(trim($_POST['displayTitle'])=='')
				$errorMsg[] = "Display Title required";
				
		if(trim($_POST['content'])=='')
				$errorMsg[] = "Description required";
		
		if(count($errorMsg)==0)	{
		// check whether title is already exixting while updating
		$query		=	"select * from contents where content_title_id='".$contentTitle."' AND language_id = {$lanId}";
		$result 	= $GLOBALS['db']->getAll($query,DB_FETCHMODE_ASSOC);
		if(isset($_POST['metaKeywords'])){
			$elmts['content_keywords']	= $objGen->_clean_data($_POST['metaKeywords']);
		}
		if(isset($_POST['browserTitle'])){
			$elmts['content_browser_title']	= $objGen->_clean_data($_POST['browserTitle']);
		}
		$elmts['content_body'] 			= $objGen->_clean_data($_POST['content']);
		$elmts['content_display_title'] = $objGen->_clean_data($_POST['displayTitle']);

			if(count($result)>0){
					assert(isset($elmts['content_keywords']));
					$result 	= 	$objContents->_updateContent($contentTitle,$lanId,$elmts,1);
					$updateMsg	=	"Record Updated successfully";
			}
			else{
					assert(isset($elmts['content_keywords']));
					$elmts['content_title_id'] 	= 	$objGen->_clean_data($_POST['title']);
					$elmts['language_id'] 		= 	$_POST['langId'];
					$result 					= 	$objContents->_insertContent($elmts,1);
					$updateMsg					=	"Record Updated successfully";
			}
		}			
	}
	
$result = $objContents->_getContent($contentTitle,$lanId);

if($result and count($errorMsg == 0)){
	$contentTitle 			= $result['content_title_id'];
	$contentDisplayTitle	= $result['content_display_title'];
	$lanId					= $result['language_id'];
	$body 					= $result['content_body'];
	$metaKeywords			= $result['content_keywords'];
	$browserTitle			= $result['content_browser_title'];
}
if($result['content_display_title'] =="" and count($errorMsg) == 0){
	$contentDisplayTitle = "";
}
if($result['content_body'] =="" and count($errorMsg) == 0){
	$body = "";
}
	
	
?>
<HTML><HEAD><TITLE><?=$admin_title?></TITLE>
<? include_once('metadata.php');?>
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
				
				  <table width="75%" border="0" align="center" cellpadding="0" cellspacing="0">
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
                       
			  			   <form name="frmcontents" action="contents.php" method="post">
                      
				  <table class="paragraph2" cellspacing=0 cellpadding=0 width=553 border=0>
				  <tr>
						<td height="50" align="center" valign="bottom" class="sectionHeading"><?=$heading;?></td>
					</tr>
                    
					<?php 
						if($errorMsg){ ?>$updateMsg
					<tr>
						<td align="center"><? print $objGen->_adminmessage_box($errorMsg,$mode='error',$url='normal') ?></td>
					</tr>
					<?php } ?>
					<?php 
						if($updateMsg){ ?>
					<tr>
						<td align="center" class="successAlert"><?=$updateMsg;?></td>
					</tr>
					<?php } ?>
                    <!-------------------------->
                    <?php 
						if($permsnMsg != ""){ ?>
					<tr>
						<td align="center" class="successAlert"> <font color="#CC0000"><?=$permsnMsg;?></font></td>
					</tr>
					<?php } ?>
					<!------------------------->
					
					<TR height="20"><TD align="left">&nbsp;</TD></TR>
					
				  </table>
                              
				  <TABLE  cellSpacing=1 cellPadding=0 width="100%" bgcolor="#EFEFDE">
				   <TBODY> 
				   <tr >
				     <td>&nbsp;</td>
				     <td>&nbsp;</td>
				     </tr>
				   <tr>
				     <td width="17%" >&nbsp;Content Title</td>
						
                                      <td width="83%"  >:
                                        <select name="title" class="paragraph" onChange="this.form.submit();">
                                          <?php
										 
										foreach($siteContentConfig as $key=>$data){
								?>
                                          <option value="<?=$key;?>" <? if($key==$contentTitle) echo 'selected';?>>
                                          <?=$data;?>
                                          </option>
                                          <?php
										}
								?>
                                        </select> </td>
                                    </tr>
					<tr >
					  <td  >&nbsp;Language</td>
					  <td  >:
					    <select name="langId" class="paragraph" onChange="this.form.submit();">
                            <?php
										while(list($key,$val) = each($languageArray)){
								?>
                            <option value="<?=$key;?>" <? if($key==$lanId) echo 'selected';?>>
                            <?=$val;?>
                            </option>
                            <?php
										}
								?>
                          </select>
                      </td>
					  </tr>
					<tr height="25" >
					  <td  >&nbsp;Display Title </td>
					  <td  >:
                        <input type="text" name="displayTitle" size="55" value="<?=$objGen->_output($contentDisplayTitle)?>">
</td>
					  </tr>
					<tr>
					  <td>&nbsp;Keywords</td>
					  <td>:
                      	<input type="text" name="metaKeywords" size="55" value="<?=$objGen->_output($metaKeywords)?>">
</td>
					</tr>
                    <tr>
					  <td>&nbsp;Browser Title</td>
					  <td>:
                      	<input type="text" name="browserTitle" size="55" value="<?=$objGen->_output($browserTitle)?>">
</td>
					</tr>
                    
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
					<script src="../ckeditornew/ckeditor/ckeditor.js"></script>
                    <script src="../ckeditornew/ckeditor/adapters/jquery.js"></script>
                    <script>
					CKEDITOR.disableAutoInline = true;
					$( document ).ready( function() {
							$( '#content' ).ckeditor(); // Use CKEDITOR.replace() if element is <textarea>.
					});
							
					</script>
					<tr><td> &nbsp;Description </td> </tr>
                                    <tr>
									   <td colspan="2">
										 <?
						 			/*$oFCKeditor = new FCKeditor('content') ;	

									$oFCKeditor->BasePath = '../FCKeditor/' ;

									$oFCKeditor->Width	= '100%' ;

									$oFCKeditor->Height	= '400' ;

									if ( isset($_GET['Toolbar']) )

									$oFCKeditor->ToolbarSet = $_GET['Toolbar'] ;

									$oFCKeditor->Value = stripslashes($body);

									$oFCKeditor->create();*/

								?>
                                <textarea name="content" id="content" ><?php echo stripslashes($body); ?></textarea>
										 
										 </td> 
                                    </tr>
                                    <tr> 
                                      <td colspan="3" align="center"> <input name="update" type="submit" value="&nbsp;Update&nbsp;">	
                                      </td>
					</tr>
				    </tbody>
			 	  </table>
                        </form>
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