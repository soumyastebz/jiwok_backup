<?php
/**************************************************************************** 
   Project Name	::> Jiwok 
   Module 		::> Admin-program image Management
   Programmer	::> Georgina George
   Date			::> 29/06/2015
   
   DESCRIPTION::::>>>>
   This  code used to list the all program .
   Admin can  edit program's image . 
*****************************************************************************/
	include_once('includeconfig.php');
	include("../includes/classes/class.program_image.php");
	error_reporting(0);
	if($page_name!= "")
	{
		include_once('admin_languageProvision.php');
	}
	else
	{
		if($_REQUEST['langId'] != ""){
		$lanId = $_REQUEST['langId'];
		}	
		else{
		$lanId = 2;
		}
	}
	/*
	Take all the languages to an array.
	*/
	$languageArray = $siteLanguagesConfig;
	reset($languageArray);
    /*
	 Instantiating the classes.
	*/
	
	$objImg = new ProgramImage($lanId);
	$objGen   =	new General();
	
	$heading = "Program's Image Management";
	$heading_title = "Program's title";
	//Sorting field decides here
	if($_REQUEST['field']){
	
		$field = $_REQUEST['field'];
		$type = $_REQUEST['type'];
	}else{
		$field = "program_id";
		$type = "ASC";
	}
	
	//check whether the search keyword is existing
	if(trim($_REQUEST['keyword'])){ 
		
	
			//$searchQuery	= " AND d.`program_title` like '%".$cleanData."%'";
			//echo $searchQuery	= " AND (d.`program_title` LIKE '%".$cleanData."%' OR m.`program_id` LIKE  '%".$cleanData."%' )"; exit;
		 $searchQuery	= " AND (d.`program_title` LIKE '%".trim(mysql_real_escape_string($_REQUEST['keyword']))."%' OR m.`program_id` LIKE  '%".trim(mysql_real_escape_string($_REQUEST['keyword']))."%' )";
			
				
	}
	
	
	//Confirmation message generates here
	
	if($_REQUEST['status'] == "success_add"){
		$confMsg = "Successfully Added";
	}
	if($_REQUEST['status'] == "success_update"){
		$confMsg = "Successfully Updated";
	}
	
	
	
	$totalRecs = $objImg->_getTotalCount($searchQuery,$lanId);
	if($totalRecs <= 0)
		$errMsg = "No Records";
	
	##############################################################################################################
	/*                        Following Code is for doing paging                                                */
	##############################################################################################################
	if(!$_REQUEST['maxrows'])
		//~ $_REQUEST['maxrows'] = $_POST['maxrows'];
		$_REQUEST['maxrows'] = 100;
	if($_REQUEST['pageNo']){ 
		if($_REQUEST['pageNo']*$_REQUEST['maxrows'] >= $totalRecs+$_REQUEST['maxrows']){
			$_REQUEST['pageNo'] = 1;
		}
		$result =  $objImg->_showPage($totalRecs,$_REQUEST['pageNo'],$_REQUEST['maxrows'],$field,$type,$searchQuery);
	}
	else{  
	/***********************Selects Records at initial stage***********************************************/
	    $_REQUEST['pageNo'] = 1;
		$result = $objImg->_showPage($totalRecs,$_REQUEST['pageNo'],$_REQUEST['maxrows'],$field,$type,$searchQuery);
		
		if(count($result) <= 0)
			$errMsg = "No Records.";
		}
		
	if($totalRecs <= $_REQUEST['pageNo']*$_REQUEST['maxrows'])
	{
		//For showing range of displayed records.
		if($totalRecs <= 0)
			$startNo = 0;
		else
			$startNo = $_REQUEST['pageNo']*$_REQUEST['maxrows']-$_REQUEST['maxrows']+1;
		$endNo = $totalRecs;
		$displayString = "Viewing $startNo to $endNo of $endNo ";
		
	}
	else
	{
		//For showing range of displayed records.
		if($totalRecs <= 0)
			$startNo = 0;
		else
			$startNo = $_REQUEST['pageNo']*$_REQUEST['maxrows']-$_REQUEST['maxrows']+1;
		$endNo = $_REQUEST['pageNo']*$_REQUEST['maxrows'];
		$displayString = "Viewing $startNo to $endNo of $totalRecs ";
		
	}
	//Pagin 
	
	$noOfPage = @ceil($totalRecs/$_REQUEST['maxrows']); 
	if($_REQUEST['pageNo'] == 1){
		$prev = 1;
	}
	else
		$prev = $_REQUEST['pageNo']-1;
	if($_REQUEST['pageNo'] == $noOfPage){
		$next = $_REQUEST['pageNo'];
	}
	else
		$next = $_REQUEST['pageNo']+1;
	
	
		
?>
<? if($_POST['update1']){
	for($i=0;$i<100;$i++){ 
		 $val='image_'.$i;
		 $val1='pgm_id_'.$i;
		 $image_name= $_POST[$val]; 
		 $prgm_id   = $_POST[$val1];
		 //~ if(!empty($image_name))
		 //~ {
			 //~ 
		 //~ $sql = "select image_newdesign from program_master  where program_id=$prgm_id";
         //~ $result = $GLOBALS['db']->query($sql); 
         
					  $sql = "update program_master set image_newdesign='".$image_name."' where program_id=$prgm_id";
					
					 $result = $GLOBALS['db']->query($sql);
					 
					 //~ 
					//~ 
          //~ }
         
          }header('Location: http://beta.jiwok.com/admin/program_img.php');
	  }?>
		   
<HTML><HEAD><TITLE><?=$admin_title?></TITLE>
<? include_once('metadata.php');?>
<BODY class="bodyStyle">
<TABLE cellSpacing=0 cellPadding=0 width="779" align="center" border="1px" bordercolor="#E6E6E6"> 
  <TR>
    <TD vAlign=top align=left bgColor=#ffffff><? include("header.php");?></TD>
  </TR>
  <TR height="5">
    <TD vAlign=top align=left class="topBarColor">&nbsp;</TD>
  </TR>
  <TR>
    <TD vAlign="top" align="left" height="340"> 
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 class="middleTableBg">
        <TR> 
          <TD vAlign=top align=left width="175" rowSpan="2" > 
            <TABLE cellSpacing="0" cellPadding="0" width="175" border=0>
              <TR> 
                <TD valign="top">
				 <TABLE cellSpacing=0 cellPadding=2 width=175 border=0>
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
          <TD vAlign=top align=left width=0></TD>
         
        </TR>
        <TR> 
          <TD valign="top" width="1067"><!---Contents Start Here----->
		  
		  
            <TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
              <TR> 
                <TD class=smalltext width="98%" valign="top">
				
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
                      <TD vAlign=top width=564 bgColor=white> 
                       
			   <form name="frmfaqs" action=" " method="post">
                        
				  <table class="paragraph2" cellspacing=0 cellpadding=0 width=553 border=0>
				  <tr>
						<td height="50" align="center" valign="bottom" class="sectionHeading"><?=$heading;?></td>
					</tr>
					<?php if($confMsg != ""){?>
					<tr> <td align="center" class="successAlert"><?=$confMsg?></td> </tr>
					<?php }
						if($errorMsg != ""){
					?>
					<tr>
						<td align="center"  class="successAlert"><?=$errorMsg?></td>
					</tr>
					<?php } ?>
					
					<TR> 
					<TD align="left">
						
				   		<table height="50"  width="100%"class="topActions"><tr>
						<?  if($objGen->_output($_REQUEST['keyword'])){ ?>
							<td valign="middle" width="50"><a href="program_img.php?maxrows=<?=$_REQUEST['maxrows']?>"><img src="images/list.gif" alt="Listing Record">&nbsp;List </a></td>
						<? }else{ ?>
							<td valign="middle" width="50" class="noneAnchor"><img src="images/list.gif" alt="Listing Record">&nbsp;List </td>
						<? } ?>
<!--
						<td valign="middle" width="50">
							
							add&pageNo=<?=$_REQUEST['pageNo']?>&maxrows=<?=$_REQUEST['maxrows']?>&keyword=<?=$objGen->_output($_REQUEST['keyword'])?>"><img src="images/add.gif" border="0" alt="Add Record">&nbsp;Add   </a>
						</td>
-->
						<td valign="middle" class="extraLabels"  align="right">Keyword&nbsp;<input name="keyword" type="text" size="10" value="<?=$objGen->_output($_REQUEST['keyword']);?>">&nbsp;<input type="button" name="search" onClick="javascript:this.form.submit();" value="Search"></td>
						</tr></table>
					</TD>
					</TR>
					
				  </table>
                     <table  cellspacing=0 cellpadding=0 width="553" border=0 class="topColor">
                      <tbody>
	    			    	<tr> 
					   <td width="204" valign=top class="paragraph2"><?=$displayString?>
					   </td>
							
						
					   <td width="166" valign=top class="paragraph2">Language:
                         <select name="langId" class="paragraph" onChange="this.form.submit()">
                           <?
									$string = "";
									while (list ($key, $val) = each ($languageArray)) {
											$string .= "<option value={$key}";
											if($key == $lanId){
												$string .= " selected";
											}
											$string	.= ">{$val}</option>";
   									}
									echo $string;
								?>
                         </select></td>
<!--
					   <td width="183" align=right class="paragraph2"><?=$heading;?> per page: 
			            <select class="paragraph"  size=1 onChange="this.form.submit()" name="maxrows">
						<? foreach($siteRowListConfig as $key=>$data){ 
						 ?>
						  <option value="<?=$key?>" <? if($_REQUEST['maxrows']==$key) echo"selected"; ?>><?=$data;?></option>
						 <? } ?>
						</select>
					</td>
-->
				    </tr>	
			      </tbody>
                  </table>
				  <TABLE class="listTableStyle" cellSpacing=1 cellPadding=2 width="553">
				   <TBODY> 
					   <TR class="tableHeaderColor">
						<TD width="9%" align="center" >#</TD>
						<TD width="20%" >Program's Id
						<a href="program_img.php?field=program_id&type=asc&langId=<?=$lanId?>&maxrows=<?=$_REQUEST['maxrows']?>&pageNo=<?=$_REQUEST['pageNo']?>&keyword=<?=$objGen->_output($_REQUEST['keyword'])?>"><img src="images/up.gif" border="0" alt="Ascending Sort">&nbsp;</a>
						<a href="program_img.php?field=program_id&type=desc&langId=<?=$lanId?>&maxrows=<?=$_REQUEST['maxrows']?>&pageNo=<?=$_REQUEST['pageNo']?>&keyword=<?=$objGen->_output($_REQUEST['keyword'])?>"><img src="images/down.gif" border="0" alt="Descending Sort"></a>
						
						</TD>
						<TD width="47%" ><?=$heading_title;?>
						
						
						
						</TD>
						
						<TD width="47%" >image name
						</TD>
<!--
						<TD width="8%" align="center" >Status</TD>
-->
						<TD width="24%" align="center" >Action</TD>
						<input type="hidden" name="langId_new" id="langId_new" value="<?php echo $lanId; ?>">
					  </TR>
					  <?php  if($errMsg != ""){?>
						  <TR class="listingTable"> 
							<TD align="center" colspan="4" ><font color="#FF0000"><?=$errMsg?></font> 
							</TD>
						  </TR>
					 <?php }
					   	
					   	$count = $startNo;
					   	if($result){
						foreach($result as $key=>$row){
							
						?>
						    <tr class="listingTable">
						    <TD align="center"><?=$count?></TD>
						    <TD><?=stripslashes(stripslashes(stripslashes($row['program_id'])));?></TD>
							<TD><?=$row['program_title'];?></TD>
							<?if($row['image_newdesign']==''){?>
                             <TD align="center"><input type="text" name="image_<?php echo $key;?>" id="image_<?php echo $key;?>" ></TD>
                              <input type="hidden" name="pgm_id_<?php echo $key;?>" id="pgm_id_<?php echo $key;?>" value="<?php echo $row['program_id']; ?>"><?} else{?>
								 <TD><input type="text" name="image_<?php echo $key;?>" id="image_<?php echo $key;?>" value="<?=$row['image_newdesign'];?>"></TD>
								  <input type="hidden" name="pgm_id_<?php echo $key;?>" id="pgm_id_<?php echo $key;?>" value="<?php echo $row['program_id']; ?>"><?}?>
								
							<TD align="center">
<!--
<a href="crop/crop.php?image=+document.getElementById('image').value" title="Previous image"  onClick=" return myFunction()">Crop</a>
-->
<?if($row['image_newdesign']==''){?>
<input type="button" name="crop" id="crop" onClick=" return myFunction1('<?php echo $key;?>')" value="Crop" ><?} else{?>
	<input type="button" name="crop" id="crop" onClick=" return myFunction1('<?php echo $key;?>')" value="Crop" >
	<?}?>
<script type="text/javascript">
function myFunction1(res)
{  
	var image = document.getElementById('image_'+res).value ;	
	var prg_id = document.getElementById('pgm_id_'+res).value ;
	    var langId_new = document.getElementById('langId_new').value ;
	    window.location ='../admin/crop/crop.php?image='+image+'&prg_id='+prg_id+'&lanId='+langId_new;
	//var x = document.getElementById("image").value;
	
 }
 </script>	
<!--
   <a href='' onclick="this.href='updateItem?codice=${item.key.codice}&quantita='+document.getElementById('qta_field').value">update</a>
-->
   
<!--
   	<a href="crop/crop.php?image=doc <button  id="cropit" class="cropButton btn btn-danger">Crop</button></a>
-->
<!--
								<a href="crop/crop.php?image=<?php echo $currentImage; ?>" title="Previous image">Crop</a>
-->


<!--
								<a href = "addedit_program_img.php?program_id=<?=$row['program_id']?>&pageNo=<?=$_REQUEST['pageNo']?>&langId=<?=$lanId?>&maxrows=<?=$_REQUEST['maxrows']?>&field=<?=$_REQUEST['field']?>&type=<?=$_REQUEST['type']?>&keyword=<?=$objGen->_output($_REQUEST['keyword'])?>&action=edit" class="smallLink">Edit</a>&nbsp;
-->

								</TD> 
								
						    </tr>
						<?php
						$count++;
						}}else{
							echo "No Records";
							}
						?>
					</tbody>
			 	</table>
			 	
			 	
				<table cellspacing=0 cellpadding=0 width=553 border=0 class="topColor">
                                <tbody>		
					<tr>
						<td align="left" colspan = "6" class="leftmenu">
						<a href="program_img.php?pageNo=1&type=<?=$_REQUEST['type']?>&langId=<?=$lanId?>&maxrows=<?=$_REQUEST['maxrows']?>&field=<?=$_REQUEST['field']?>&keyword=<?=$objGen->_output($_REQUEST['keyword'])?>">
						<img src="images/mail-arrowfg.gif" border=0 width="20" height="20" align="absmiddle" alt="First Page"></a>
						<a href="program_img.php?pageNo=<?=$prev?>&type=<?=$_REQUEST['type']?>&langId=<?=$lanId?>&maxrows=<?=$_REQUEST['maxrows']?>&field=<?=$_REQUEST['field']?>&keyword=<?=$objGen->_output($_REQUEST['keyword'])?>">
						<img src="images/mail-arrowlg.gif" border=0 width="20" height="20" align="absmiddle" alt="Previous Page"></a>[Page 
						<select name="pageNo" class="paragraph"  onChange="form.submit()">
							<?php
							if($noOfPage){
								for($i = 1; $i <= $noOfPage; $i++){
							?>
								<option value="<?=$i?>" <? if($i==$_REQUEST['pageNo']) echo "selected";?>><?=$i?></option>
							<?php
								}
							}
							else{
								echo "<option value=\"\">0</option>";
							}
							?>
						</select>
							 of <?=$noOfPage?>]
							 <a href="program_img.php?pageNo=<?=$next?>&type=<?=$_REQUEST['type']?>&langId=<?=$lanId?>&field=<?=$_REQUEST['field']?>&maxrows=<?=$_REQUEST['maxrows']?>&keyword=<?=$objGen->_output($_REQUEST['keyword'])?>">
							<img src="images/mail-arrowrg.gif" border=0 width="20" height="20" align="absmiddle" alt="Next Page"></a>
							<a href="program_img.php?pageNo=<?=$noOfPage?>&type=<?=$_REQUEST['type']?>&langId=<?=$lanId?>&maxrows=<?=$_REQUEST['maxrows']?>&field=<?=$_REQUEST['field']?>&keyword=<?=$objGen->_output($_REQUEST['keyword'])?>">
							<img src="images/mail-arrowlastg.gif" border=0 width="20" height="20" align="absmiddle" alt="Last Page"></a>
						</td>
					</tr>
					</tbody>
			 	</table>
				<input type="hidden" name="type" value="<?=$_REQUEST['type']?>">
				<input type="hidden" name="field" value="<?=$_REQUEST['field']?>">
				<div align="center" style="padding-top:10px;"><input type="submit" name="update1" value="update"></div>
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
    <TD vAlign=top align=left class="topBarColor" colspan="3">&nbsp;</TD>
  </TR>
      </TABLE>
      
        <?php include_once("footer.php");?>
</body>
</html>
