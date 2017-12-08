<?php
$redirect_arr	=	array("search.php","search_result.php","contents.php");
$base_url		=	basename($_SERVER['REQUEST_URI']);
if (strpos($base_url,'?') !== false) 
	$basear		=   explode("?",$base_url);
else
	$basear[0]	=	$base_url;
/*if($_REQUEST['categoryName']	==	""	||	$_REQUEST['[program_title_url']	==	"")
{
*/	if(in_array($basear[0],$redirect_arr))
	{
		//header('location:'.ROOT_JWPATH.'entrainement');
	}
//}
?>