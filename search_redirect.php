<?php
/*--------------------------------------------------------------*/
// Project 		: Jiwok
// Created on	: 01-07-2013
// Created by	: Shilpa
// Purpose		: Creating SEO friendly urls for redirection
/*--------------------------------------------------------------*/

session_start();
if($_REQUEST['search'] !="")
 {
	 
				if((strpos($_REQUEST['cont_goal'],"/")!="false")||(strpos($_REQUEST['cont_level'],"/")!="false")||(strpos($_REQUEST['cont_session'],"/")!="false")||(strpos($_REQUEST['cont_sport'],"/")!="false"))
				{
					$_REQUEST['cont_goal']		=	str_replace("/","-",$_REQUEST['cont_goal']);
					$_REQUEST['cont_level']		=	str_replace("/","-",$_REQUEST['cont_level']);
					$_REQUEST['cont_session']	=	str_replace("/","-",$_REQUEST['cont_session']);
					$_REQUEST['cont_sport']		=	str_replace("/","-",$_REQUEST['cont_sport']);
				}
				$_SESSION['search_val']			=	$_REQUEST['search'];
				$_SESSION['val_goal']			=	$_REQUEST['user_goal'];
				$_SESSION['val_level']			=	$_REQUEST['user_level'];
				if($lanId==1)
				{
					$_SESSION['val_session']	=	$_REQUEST['user_no_session'];
				}
				else
				{
					$_SESSION['val_sport']		=	$_REQUEST['user_sport'];
				}
				$_SESSION['item_goal']			=	$_REQUEST['cont_goal'];
				$_REQUEST['cont_goal']			=	strtolower($_REQUEST['cont_goal']);
				$_SESSION['item_level']			=	$_REQUEST['cont_level'];
				$_REQUEST['cont_level']			=	strtolower($_REQUEST['cont_level']);
				
				if($lanId==1)
				{
					$_SESSION['item_session']	=	$_REQUEST['cont_session'];
				}
				else
				{
					$_SESSION['item_sport']		=	$_REQUEST['cont_sport'];
					$_REQUEST['cont_sport']		=	strtolower($_REQUEST['cont_sport']);
				}
				if($lanId	==	1){
						if($_REQUEST['cont_goal']!=""	&&	$_REQUEST['cont_level']!=""	&&	$_REQUEST['cont_session']!="")
							{

								$url	=	ROOT_JWPATH.'entrainement/'.$_REQUEST['cont_goal']."+".$_REQUEST['cont_level']."+".$_REQUEST['cont_session'];
								header('location:'.$url,true,301);
								exit;
							}
						else if($_REQUEST['cont_goal']!=""	&&	$_REQUEST['cont_level']!="")
							{
								$url	=	ROOT_JWPATH.'entrainement/'.$_REQUEST['cont_goal']."+".$_REQUEST['cont_level'];
								header('location:'.$url,true,301);
								exit;												
							}
						else if($_REQUEST['cont_goal']!=""	&&	$_REQUEST['cont_session']!="")
							{
								$url	=	ROOT_JWPATH.'entrainement/'.$_REQUEST['cont_goal']."+".$_REQUEST['cont_session'];
								header('location:'.$url,true,301);
								exit;	
							}
						else if($_REQUEST['cont_level']!=""	&&	$_REQUEST['cont_session']!="")
							{
								$url	=	ROOT_JWPATH.'entrainement/'.$_REQUEST['cont_level']."+".$_REQUEST['cont_session'];
								header('location:'.$url,true,301);
								exit;
							}
						else if($_REQUEST['cont_goal']!="")
							{
								$url	=	ROOT_JWPATH.'entrainement/'.$_REQUEST['cont_goal'];
								header('location:'.$url,true,301);
								exit;
							}
						else if($_REQUEST['cont_level']!="")
							{
								$url	=	ROOT_JWPATH.'entrainement/'.$_REQUEST['cont_level'];
								header('location:'.$url,true,301);
								exit;
							}
						else if($_REQUEST['cont_session']!="")
							{
								$url	=	ROOT_JWPATH.'entrainement/'.$_REQUEST['cont_session'];
								header('location:'.$url,true,301);
								exit;
							}
				}
				else{
					
						if($_REQUEST['cont_goal']!=""	&&	$_REQUEST['cont_level']!=""	&&	$_REQUEST['cont_sport']!="")
							{
								
								 $url	=	ROOT_JWPATH.'entrainement/'.$_REQUEST['cont_goal']."+".$_REQUEST['cont_level']."+".$_REQUEST['cont_sport'];
								 $url	=	str_replace(' ',"-",$url);
								header('location:'.$url,true,301);
								exit;
							}
							
						else if($_REQUEST['cont_goal']!=""	&&	$_REQUEST['cont_level']!="")
						{	
							$url	=	ROOT_JWPATH.'entrainement/'.$_REQUEST['cont_goal']."+".$_REQUEST['cont_level'];
							header('location:'.$url,true,301);
							exit;
						}						
						else if($_REQUEST['cont_goal']!=""	&&	$_REQUEST['cont_sport']!="")
						{	
							$url	=	ROOT_JWPATH.'entrainement/'.$_REQUEST['cont_goal']."+".$_REQUEST['cont_sport'];
							$url	=	str_replace(' ',"-",$url);
							header('location:'.$url,true,301);
							exit;
						}
						else if($_REQUEST['cont_level']!=""	&&	$_REQUEST['cont_sport']!="")
						{	
							$url	=	ROOT_JWPATH.'entrainement/'.$_REQUEST['cont_level']."+".$_REQUEST['cont_sport'];
							$url	=	str_replace(' ',"-",$url);
							header('location:'.$url,true,301);
							exit;
						}
						else if($_REQUEST['cont_goal']!="")
						{
							$url	=	ROOT_JWPATH.'entrainement/'.$_REQUEST['cont_goal'];
							header('location:'.$url,true,301);
							exit;
						}
						else if($_REQUEST['cont_level']!="")
						{
							$url	=	ROOT_JWPATH.'entrainement/'.$_REQUEST['cont_level'];
							header('location:'.$url,true,301);
							exit;
						}
						else if($_REQUEST['cont_sport']!="")
						{	
							$url	=	ROOT_JWPATH.'entrainement/'.$_REQUEST['cont_sport'];
							$url	=	str_replace(' ',"-",$url);
							header('location:'.$url,true,301);
							exit;
						}
				}
				
		}
        ?>
