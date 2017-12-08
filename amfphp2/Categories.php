<?php

class Categories{

	private $lang_data;

	private $dbObj, $magicQuotesGPC;

	public function __construct(){

		$this->lang_data	= array();

		require_once('config.php');

		################

		$dbObj	= new dataBase($db_host, $db_username, $db_password, $db_database);

		$this->dbObj	= $dbObj;

		$this->magicQuotesGPC	= get_magic_quotes_gpc();

		################

		$select_Qry	= "select language_id, language_name from `languages`";

		$lang_data	= $this->dbObj->selectQry($select_Qry);

		foreach($lang_data as $lang_row){

			$language_id	= $lang_row['language_id'];

			$this->lang_data[$language_id]	= $lang_row['language_name'];

		}

	}

	

	public function deleteCategory($flex_id){

		$affected_rows	= 0;

		if($flex_id!='') {

			$this->escapeData($flex_id);

			 $delete_condition	= "`flex_id` = '$flex_id' OR `parent_id` = '$flex_id'";

			$affected_rows		= $this->dbObj->deleteQry('sub_category', $delete_condition);

		}



		return $affected_rows;

	}

	

	public function AddCategories($xml_string){

		$affected_rows	= 0;

		$categories_arr = array();

		$category_arr	= array(

			'flex_id'=>'',

			'language_id'=>'',

			'category_name'=>'',

			'parent_id'=>'',

			'status'=>'',

			'english_status'=>'',

			'description'=>'',

			);
		
		$xml_string = utf8_encode($xml_string);
		$xml_obj	= simplexml_load_string($xml_string);
		$myFile = "workoutlog.doc";
		$fh = fopen($myFile, 'a');
		$stringData = "-----------------------\n";
		fwrite($fh, $stringData);		
		fwrite($fh, $xml_string);
		$stringData = "-----------------------\n";
		fwrite($fh, $stringData);
		fclose($fh);

 

		

		foreach($xml_obj->cat as $category){			

			$category_arr['flex_id']	= (string) $category['id'];

			$category_flex_id	= $category_arr['flex_id'];

			$category_arr['parent_id']	= 0;

			$category_arr['status']	=(string)  $category['status'];

			$category_arr['english_status']	=(string) $category['english_status'];



			foreach($this->lang_data as $language_name){
				
				//New content added by Dileep for SEO title and description start
				$seoNodeCatgry			=	'SEO'.$language_name;
				$seo_cat_language_data	=	$category->$seoNodeCatgry;
				//New content added by Dileep for SEO title and description ends

				$language_data	= $category->$language_name;

				$category_arr['language_id']	= (string) $language_data['id'];

				$category_arr['description']	= (string) $language_data['desc'];

				$category_arr['category_name']	= (string) $category->$language_name;

				//New content added by Dileep for SEO title and description start
				$category_arr['seo_title']		= (string) $category->$seoNodeCatgry;
				$category_arr['seo_description']= (string) $seo_cat_language_data['desc'];
				//New content added by Dileep for SEO title and description ends
				//New content added by Dileep for category update issue starts				
				$category_arr['status']			= (string) $language_data['status'];
				$category_arr['english_status']	= (string) $language_data['engStatus'];
				//New content added by Dileep for category update issue ends
				
				$categories_arr[]		= $category_arr;

			}

		

			foreach($category->subcategories->subcategory as $subcategory){

				$category_arr['flex_id']	= (string) $subcategory['id'];

				$category_arr['parent_id']	= $category_flex_id;

				foreach($this->lang_data as $language_name){

					//New content added by Dileep for SEO title and description start
					$seoNodeSubCatgry			=	'SEO'.$language_name;
					$seo_sub_cat_language_data	=	$subcategory->$seoNodeSubCatgry;
					//New content added by Dileep for SEO title and description ends
					
					$language_data	= $subcategory->$language_name;

					$category_arr['language_id']	= (string) $language_data['id'];

					$category_arr['description']	= (string) $language_data['desc'];

					$category_arr['category_name']	= (string) $subcategory->$language_name;

					//New content added by Dileep for SEO title and description start
					$category_arr['seo_title']		= (string) $subcategory->$seoNodeSubCatgry;
					$category_arr['seo_description']= (string) $seo_sub_cat_language_data['desc'];
					//New content added by Dileep for SEO title and description ends
					//New content added by Dileep for category update issue starts				
					$category_arr['status']			= (string) $language_data['status'];
					$category_arr['english_status']	= (string) $language_data['engStatus'];
					//New content added by Dileep for category update issue ends
					
					
					$categories_arr[]		= $category_arr;

				}

			}

		}

		unset($xml_obj, $category_arr, $language_data);

		//echo "<pre>";

		//print_r($categories_arr);

		//echo "</pre>";

		//exit;

		$affected_rows	= $this->insertData($categories_arr);

		$xml_str		= "<result>$affected_rows</result>";



		return $xml_str;

	}



    public function EditCategories($xml_string){

		$affected_rows	= 0;

		$categories_arr = array();

		$category_arr	= array(

			'flex_id'=>'',

			'language_id'=>'',

			'category_name'=>'',

			'parent_id'=>'',

			'status'=>'',

			'english_status'=>'',

			'description'=>'',
			
			'seo_title'=>'',
			
			'seo_description'=>''
			);
		//$xml_string = utf8_encode($xml_string);
		$xml_obj	= simplexml_load_string($xml_string);

		foreach($xml_obj->cat as $category){

			$category_arr['flex_id']	= (string) $category['id'];

			$category_flex_id	= $category_arr['flex_id'];

			$category_arr['parent_id']	= 0;

			$category_arr['status']	=(string)  $category['status'];

			$category_arr['english_status']	=(string) $category['english_status'];



			foreach($this->lang_data as $language_name){

				//New content added by Dileep for SEO title and description start
				$seoNodeCatgry			=	'SEO'.$language_name;
				$seo_cat_language_data	=	$category->$seoNodeCatgry;
				//New content added by Dileep for SEO title and description ends
				
				$language_data		= 	$category->$language_name;
				

				$category_arr['language_id']	= (string) $language_data['id'];

				$category_arr['description']	= (string) $language_data['desc'];

				$category_arr['category_name']	= (string) $category->$language_name;
				
				//New content added by Dileep for SEO title and description start
				$category_arr['seo_title']		= (string) $category->$seoNodeCatgry;
				$category_arr['seo_description']= (string) $seo_cat_language_data['desc'];
				//New content added by Dileep for SEO title and description ends
				//New content added by Dileep for category update issue starts				
				$category_arr['status']			= (string) $language_data['status'];
				$category_arr['english_status']	= (string) $language_data['engStatus'];
				//New content added by Dileep for category update issue ends
				$categories_arr[]		= $category_arr;

			}

			foreach($category->subcategories->subcategory as $subcategory){

				$category_arr['flex_id']	= (string) $subcategory['id'];

				$category_arr['parent_id']	= $category_flex_id;

				foreach($this->lang_data as $language_name){

					//New content added by Dileep for SEO title and description start
					$seoNodeSubCatgry			=	'SEO'.$language_name;
					$seo_sub_cat_language_data	=	$subcategory->$seoNodeSubCatgry;
					//New content added by Dileep for SEO title and description ends
					$language_data	= $subcategory->$language_name;

					$category_arr['language_id']	= (string) $language_data['id'];

					$category_arr['description']	= (string) $language_data['desc'];

					$category_arr['category_name']	= (string) $subcategory->$language_name;
					
					//New content added by Dileep for SEO title and description start
					$category_arr['seo_title']		= (string) $subcategory->$seoNodeSubCatgry;
					$category_arr['seo_description']= (string) $seo_sub_cat_language_data['desc'];
					//New content added by Dileep for SEO title and description ends
					//New content added by Dileep for category update issue starts				
					$category_arr['status']			= (string) $language_data['status'];
					$category_arr['english_status']	= (string) $language_data['engStatus'];
					//New content added by Dileep for category update issue ends
					$categories_arr[]		= $category_arr;

				}

			}

		}

		unset($xml_obj, $category_arr, $language_data);

		//echo "<pre>";

		//print_r($categories_arr);

		//echo "</pre>";

		

		$affected_rows	= $this->updateData($categories_arr);

		$xml_str		= "<result>$affected_rows</result>";



		return $xml_str;

	}




	public function GetAllCategories(){

		$parent_categories	= array();



		$select_Qry	= "SELECT * FROM `sub_category` WHERE `parent_id` = 0 ORDER BY flex_id,language_id";

		$parent_categories_temp	= $this->dbObj->selectQry($select_Qry);

		//return $parent_categories_temp;

		foreach($parent_categories_temp as $parent_categories_temp_row)

			{

				$flex_id		= $parent_categories_temp_row['flex_id'];

				$language_id1	= $parent_categories_temp_row['language_id'];	

				$parent_categories[$flex_id]['status'] = $parent_categories_temp_row['status'];

				$parent_categories[$flex_id]['english_status'] = $parent_categories_temp_row['english_status'];



				$category_name	= $parent_categories_temp_row['category_name'];

				$select_Qry1	= "select language_id, language_name from `languages`";

				$lang_data		= $this->dbObj->selectQry($select_Qry1);

				

				foreach($lang_data as $lang_row)

					{

						$language_id			=	$lang_row['language_id'];	

						$select_Qry				=	"SELECT * FROM `sub_category` WHERE `flex_id` = '$flex_id' and  language_id='$language_id' ORDER BY flex_id,language_id";

						$parent_categories_temp	=	$this->dbObj->selectQry($select_Qry);

						if($parent_categories_temp)

						{

							$catName				=	$parent_categories_temp[0]["category_name"];

							$catdesc                =   $parent_categories_temp[0]["description"];
							
							//New content added by Dileep for SEO title and description start
							$seo_title				=	$parent_categories_temp[0]["seo_title"];
							$seo_desc				=	$parent_categories_temp[0]["seo_description"];
							$status					=	$parent_categories_temp[0]["status"];							
							$engStatus				=	$parent_categories_temp[0]["english_status"];
							//New content added by Dileep for SEO title and description ends							
						}

						else

						{

							$catName				=	"";

							$catdesc                =  	"";
							
							//New content added by Dileep for SEO title and description start
							$seo_title				=	"";
							$seo_desc				=	"";
							$status					=	'1';							
							$engStatus				=	'1';
							//New content added by Dileep for SEO title and description ends
						}

						

						

						$parent_categories[$flex_id]['lan'][$language_id]['category_name']	= $catName;	

						$parent_categories[$flex_id]['lan'][$language_id]['description']	= $catdesc;
						
						//New content added by Dileep for SEO title and description start
						$parent_categories[$flex_id]['lan'][$language_id]['seo_title']	= $seo_title;
						$parent_categories[$flex_id]['lan'][$language_id]['seo_description']	= $seo_desc;	
						
						$parent_categories[$flex_id]['lan'][$language_id]['status']	= $status;
						$parent_categories[$flex_id]['lan'][$language_id]['engStatus']	= $engStatus;					
						//New content added by Dileep for SEO title and description ends		

					}	

			}

		unset($parent_categories_temp);

		

		

		$select_Qry	= "SELECT * FROM `sub_category` WHERE `parent_id` <> 0 ORDER BY flex_id,language_id";

		$sub_categories_temp	= $this->dbObj->selectQry($select_Qry);

		foreach($sub_categories_temp as $sub_categories_temp_row){

			$parent_id		= $sub_categories_temp_row['parent_id'];

			$flex_id		= $sub_categories_temp_row['flex_id'];

			$language_id	= $sub_categories_temp_row['language_id'];

			$sub_categories[$parent_id][$flex_id]['status'] = $sub_categories_temp_row['status'];

			$sub_categories[$parent_id][$flex_id]['english_status'] = $sub_categories_temp_row['english_status'];



			//$category_name	= $sub_categories_temp_row['category_name'];

			$select_Qry1	= "select language_id, language_name from `languages`";

			$lang_data		= $this->dbObj->selectQry($select_Qry1);	

			//print_r($lang_data);		

			foreach($lang_data as $lang_row)

				{

					$language_id			=	$lang_row['language_id'];

					$select_Qry				=	"SELECT * FROM `sub_category` WHERE `flex_id` = '$flex_id' and parent_id = '$parent_id' and  language_id='$language_id' ORDER BY flex_id,language_id";

					$sub_categories_temp	=	$this->dbObj->selectQry($select_Qry);

					//int_r($sub_categories_temp);

					if($sub_categories_temp)

					{

							$catName				=	$sub_categories_temp[0]["category_name"];

							$catdesc                =   $sub_categories_temp[0]["description"];
							
							//New content added by Dileep for SEO title and description start
							$seo_title				=	$sub_categories_temp[0]["seo_title"];
							$seo_desc				=	$sub_categories_temp[0]["seo_description"];
							$status					=	$sub_categories_temp[0]["status"];							
							$engStatus				=	$sub_categories_temp[0]["english_status"];
							//New content added by Dileep for SEO title and description ends

					}	

					else

					{

							$catName				=	"";

							$catdesc                =   "";
							
							//New content added by Dileep for SEO title and description start
							$seo_title				=	"";
							$seo_desc				=	"";
							$status					=	'1';							
							$engStatus				=	'1';
							//New content added by Dileep for SEO title and description ends

					}

			$sub_categories[$parent_id][$flex_id]['lan'][$language_id]['category_name']	= $catName;

			$sub_categories[$parent_id][$flex_id]['lan'][$language_id]['description']	= $catdesc;
			
			//New content added by Dileep for SEO title and description start
			$sub_categories[$parent_id][$flex_id]['lan'][$language_id]['seo_title']	= $seo_title;
			$sub_categories[$parent_id][$flex_id]['lan'][$language_id]['seo_description']	= $seo_desc;
			$sub_categories[$parent_id][$flex_id]['lan'][$language_id]['status']	= $status;
			$sub_categories[$parent_id][$flex_id]['lan'][$language_id]['engStatus']	= $engStatus;				
			//New content added by Dileep for SEO title and description ends

			}

		}

		unset($sub_categories_temp);

		$xml_string	= $this->createXml($parent_categories, $sub_categories);		

		return $xml_string;

	}



	

	private function createXml($parent_categories, $sub_categories){

	//echo "<pre>";print_r($sub_categories);echo "</pre>";

		$count				= 0;

		$parent_category	= array();

		$root_tag			= 'cats';

		$cat_tag			= 'cat';

		$sub_cat_root_tag	= 'subcategories';

		$sub_cat_tag		= 'subcategory';

		$cat_attribute		= 'id';

		$sub_cat_attribute	= 'id';

		$cat_status		=	'status';

		$cat_eng_status		=	'english_status';

		$sub_cat_status		=	'status';

		$sub_cat_eng_status		=	'english_status';
		
		$seo_desc			=	'desc';



		if(sizeof($parent_categories)>0){

			array_walk_recursive($parent_categories, array($this, 'convertData'));

		}

		if(sizeof($sub_categories)>0){

			array_walk_recursive($sub_categories, array($this, 'convertData'));

		}

		$wrt = xmlwriter_open_memory();

		xmlwriter_set_indent($wrt,true);

		xmlwriter_start_document($wrt, '1.0', 'ISO-8859-1');
		//xmlwriter_start_document($wrt, '1.0', 'UTF-8');

		xmlwriter_start_element($wrt, $root_tag);



		foreach($parent_categories as $cat_flex_id=>$parent_category) {

			xmlwriter_start_element($wrt, $cat_tag);

			xmlwriter_write_attribute($wrt, $cat_attribute, $cat_flex_id);

			xmlwriter_write_attribute($wrt, $cat_status, $parent_category['status']);

			xmlwriter_write_attribute($wrt, $cat_eng_status, $parent_category['english_status']);



			foreach($parent_category['lan'] as $language_id=>$parent_category_row){

				xmlwriter_start_element($wrt, $this->lang_data[$language_id]);

					xmlwriter_write_attribute($wrt, 'id', $language_id);

					xmlwriter_write_attribute($wrt, 'desc', $parent_category_row['description']);
					
					//New content added by Dileep for category update issue starts
					xmlwriter_write_attribute($wrt, 'status', $parent_category_row['status']);
					
					xmlwriter_write_attribute($wrt, 'engStatus', $parent_category_row['engStatus']);
					//New content added by Dileep for category update issue ends
					
					xmlwriter_text($wrt, $parent_category_row['category_name']);

				xmlwriter_end_element($wrt);
				//New content added by Dileep for SEO title and description start
				$seoNodeCatgry			=	'SEO'.$this->lang_data[$language_id];				
				xmlwriter_start_element($wrt, $seoNodeCatgry);
					xmlwriter_write_attribute($wrt, $seo_desc, $parent_category_row['seo_description']);
					xmlwriter_text($wrt, $parent_category_row['seo_title']);
				xmlwriter_end_element($wrt);
				//New content added by Dileep for SEO title and description ends
			}

			//Sub-categories

			xmlwriter_start_element($wrt, $sub_cat_root_tag);

if(isset($sub_categories[$cat_flex_id])){

			foreach($sub_categories[$cat_flex_id] as $sub_cat_flex_id=>$sub_category) {

				xmlwriter_start_element($wrt, $sub_cat_tag);

				xmlwriter_write_attribute($wrt, $sub_cat_attribute, $sub_cat_flex_id);

				xmlwriter_write_attribute($wrt, $sub_cat_status, $sub_category['status']);

				xmlwriter_write_attribute($wrt, $sub_cat_eng_status, $sub_category['english_status']);



				foreach($sub_category['lan'] as $sub_language_id=>$category_row) {

					xmlwriter_start_element($wrt, $this->lang_data[$sub_language_id]);

						xmlwriter_write_attribute($wrt, 'id', $sub_language_id);
	
						xmlwriter_write_attribute($wrt, 'desc', $category_row['description']);
	
						//New content added by Dileep for category update issue starts
						xmlwriter_write_attribute($wrt, 'status', $category_row['status']);
						
						xmlwriter_write_attribute($wrt, 'engStatus', $category_row['engStatus']);
						//New content added by Dileep for category update issue ends
						
						xmlwriter_text($wrt, $category_row['category_name']);

					xmlwriter_end_element($wrt);
					
					//New content added by Dileep for SEO title and description start
					$seoNodeSubCatgry			=	'SEO'.$this->lang_data[$sub_language_id];					
					xmlwriter_start_element($wrt, $seoNodeSubCatgry);
						xmlwriter_write_attribute($wrt, $seo_desc, $category_row['seo_description']);
						xmlwriter_text($wrt, $category_row['seo_title']);
					xmlwriter_end_element($wrt);
					//New content added by Dileep for SEO title and description ends

				}

				xmlwriter_end_element($wrt);

			}

}			xmlwriter_end_element($wrt);

		xmlwriter_end_element($wrt);

		}

		xmlwriter_end_element($wrt);

		xmlwriter_end_document($wrt);

		$xml_string	= xmlwriter_output_memory($wrt);

		//$xml_string = utf8_encode($xml_string);

		return($xml_string);

	}

	

	private function insertData($categories_arr){

		$affected_rows	= 0;

		if(sizeof($categories_arr)>0){

			array_walk_recursive($categories_arr, array($this, 'escapeData'));			
				$insert_query	= "INSERT INTO `sub_category` (`flex_id`, `language_id`, `category_name`, `parent_id`,`status`,`english_status`,`description`,`seo_title`,`seo_description`,`category_image`) VALUES ";

			foreach($categories_arr as $category_arr){

				 $select_Qry		= "select category_image  from `sub_category` where flex_id =". $category_arr['flex_id'] ;
				$cat_img_data 	= $this->dbObj->selectQry($select_Qry);
				if($cat_img_data[0]['category_image'] !="")
				{
					$category_arr['category_image'] = $cat_img_data[0]['category_image'];
				}
				else
				{
						$category_arr['category_image'] = "";
				}
				$myarr[]	=	$cat_img_data 	;
				$insert_query	.= "('" . implode("', '", $category_arr). "'), ";

			}
			$truncate_qry	= "TRUNCATE TABLE `sub_category`";

			$this->dbObj->changeRowsQry($truncate_qry);
			$insert_query	= substr($insert_query, 0, -2);

			$insert_query	.= ';';

			//echo $insert_query;

			//exit;

			$affected_rows	= $this->dbObj->changeRowsQry($insert_query);

		}

		

		return $affected_rows;

	}

	

	private function updateData($categories_arr){

		$affected_rows	= 0;

		if(sizeof($categories_arr)>0){

			array_walk_recursive($categories_arr, array($this, 'escapeData'));

			$insert_query	= "INSERT INTO `sub_category` (`flex_id`, `language_id`, `category_name`, `parent_id`,`status`,`english_status`,`description`,`seo_title`,`seo_description`) VALUES ";

			foreach($categories_arr as $category_arr){

			    $check=$this->deleteCategory($category_arr['flex_id']);

				$insert_query	.= "('" . implode("', '", $category_arr). "'), ";

			}

			
			$insert_query	= substr($insert_query, 0, -2);

			$insert_query	.= ';';

			//echo $insert_query;

			//exit;

			$affected_rows	= $this->dbObj->changeRowsQry($insert_query);

		}

		

		return $affected_rows;

	}

	private function escapeData(&$element){

		if($this->magicQuotesGPC){

			$element	= stripslashes($element);

		}

		//$element	= utf8_decode($element);

		//$element	= utf8_decode($element);

		// Changed on 12-12-2012
		$element	= utf8_decode($element);


		//$element	= mb_convert_encoding($element, 'ISO-8859-1');

		$element	= html_entity_decode($element);

		$element	= mysql_real_escape_string($element,$this->dbObj->con);

	}

	

	private function convertData(&$element){

		/*convert to utf-8 and back into iso-8859-1. 

		* else some characters are not detected correctly by xml_writer.

		*/

		$element	= mb_convert_encoding($element, 'UTF-8');

//		$element	= mb_convert_encoding($element, 'ISO-8859-1');
		
		$element	= utf8_decode($element);
		
		//$element	= html_entity_decode($element,ENT_COMPAT);

		$element	= htmlspecialchars($element, ENT_COMPAT);

	}

	

	

	//Function to get all subcategories

	

	/*public function CategoryXml()

	{

		$xml  = '<?xml version="1.0" encoding="utf-8" ?>';

		$xml .= '<root>';

		$select_Qry		=	"select distinct(flex_id) from sub_category where flex_id not like '%.%' order by flex_id desc";

		$categories				= $this->dbObj->selectQry($select_Qry);

		

		$sql_lan				=	"select language_id from languages";

		$res_lan				= $this->dbObj->selectQry($sql_lan);

		for($k=0;$k<count($res_lan);$k++)

		{

				$xml								.=	"<cats lanid='".$res_lan[$k]['language_id']."'>";

				for($i=0;$i<count($categories);$i++)

				{

					$flex_id							=	$categories[$i]['flex_id'];

					$select_Qry_main_cat		=	"select * from sub_category where language_id=".$res_lan[$k]['language_id']." and flex_id='".$flex_id."'";

					$categories_main				= $this->dbObj->selectQry($select_Qry_main_cat);

					

					$xml								.=	'<cat id="'.$categories_main[0]['flex_id'].'" pid="'.$categories_main[0]['parent_id'].'" t="'.$categories_main[0]['category_name'].'" checked="">';

					

					$select_Qry1					=	"select * from sub_category where language_id=".$res_lan[$k]['language_id']." and flex_id like '".$flex_id.".%'";

					$categories1					= $this->dbObj->selectQry($select_Qry1);

					for($j=0;$j<count($categories1);$j++)

						{	

							$xml					.=	'<cat id="'.$categories1[$j]['flex_id'].'" pid="'.$categories1[$j]['parent_id'].'" t="'.$categories1[$j]['category_name'].'"></cat>';

						}

						$xml .= "</cat>";

				}

				$xml.="</cats>";

		}

		 $xml.="</root>";

		 return $xml;

	}*/

	public function CategoryXml()
	{
		header('Content-type:application/xml;charset=utf-8');  
	//	$headers = headers_list();return $headers;
		$this	->	doc 	= 	new DOMDocument('1.0', 'utf-8');
		$this	->	doc 	->	preserveWhiteSpace = false;
		$this	->	doc 	->	formatOutput = true;
		$this	->	xmlMainNode	=	$this	->	doc ->createElement( "root" );
		$this	->	doc		->	appendChild( $this	->	xmlMainNode );
		
		$select_Qry		=	"select distinct(flex_id) from sub_category where flex_id not like '%.%' order by flex_id desc";

		$categories				= $this->dbObj->selectQry($select_Qry);

		

		$sql_lan				=	"select language_id from languages";

		$res_lan				= $this->dbObj->selectQry($sql_lan);

		for($k=0;$k<count($res_lan);$k++)

		{

				//$xml								.=	"<cats lanid='".$res_lan[$k]['language_id']."'>";
				$xmlLang		=	$this		->	doc	->	createElement( "cats" );
				$xmlLangAttr 	= 	$this		->	doc	->	createAttribute("lanid");
				$xmlLangAttr	->	value	=	$res_lan[$k]['language_id'];
				$xmlLang->appendChild($xmlLangAttr);				
				for($i=0;$i<count($categories);$i++)
				{
					$flex_id							=	$categories[$i]['flex_id'];
					$select_Qry_main_cat		=	"select * from sub_category where language_id=".$res_lan[$k]['language_id']." and flex_id='".$flex_id."'";
					$categories_main				= $this->dbObj->selectQry($select_Qry_main_cat);

					

					//$xml								.=	'<cat id="'.$categories_main[0]['flex_id'].'" pid="'.$categories_main[0]['parent_id'].'" t="'.$categories_main[0]['category_name'].'" checked="">';
					$xmlMainCat			=	$this	->	doc	->	createElement( "cat" );
					$xmlMainCatAttrId 	= 	$this	->	doc	->	createAttribute("id");
					$xmlMainCatAttrId	->	value	=	$categories_main[0]['flex_id'];
					$xmlMainCat->appendChild($xmlMainCatAttrId);
					
					$xmlMainCatAttrPid 	= 	$this	->	doc	->	createAttribute("pid");
					$xmlMainCatAttrPid	->	value	=	$categories_main[0]['parent_id'];
					$xmlMainCat->appendChild($xmlMainCatAttrPid);
					
					$xmlMainCatAttrT 	= 	$this	->	doc	->	createAttribute("t");
					$xmlMainCatAttrT	->	value	=	$categories_main[0]['category_name'];
					$xmlMainCat->appendChild($xmlMainCatAttrT);
					
					$xmlMainCatAttrCkd 	= 	$this	->	doc	->	createAttribute("checked");
					$xmlMainCatAttrCkd	->	value	=	"";
					$xmlMainCat->appendChild($xmlMainCatAttrCkd);
					

					$select_Qry1					=	"select * from sub_category where language_id=".$res_lan[$k]['language_id']." and flex_id like '".$flex_id.".%'";

					$categories1					= $this->dbObj->selectQry($select_Qry1);

					for($j=0;$j<count($categories1);$j++)
					{	

						//$xml					.=	'<cat id="'.$categories1[$j]['flex_id'].'" pid="'.$categories1[$j]['parent_id'].'" t="'.$categories1[$j]['category_name'].'"></cat>';
						$xmlSubCat			=	$this		->	doc	->	createElement( "cat" );
						$xmlSubCatAttrId 	= 	$this		->	doc	->	createAttribute("id");
						$xmlSubCatAttrId	->	value		=	$categories1[$j]['flex_id'];
						$xmlSubCat->appendChild($xmlSubCatAttrId);	
						
						$xmlSubCatAttrPid 	= 	$this		->	doc	->	createAttribute("pid");
						$xmlSubCatAttrPid	->	value		=	$categories1[$j]['parent_id'];
						$xmlSubCat->appendChild($xmlSubCatAttrPid);	
						
						$xmlSubCatAttrT 	= 	$this		->	doc	->	createAttribute("t");
						$xmlSubCatAttrT		->	value		=	$categories1[$j]['category_name'];
						$xmlSubCat->appendChild($xmlSubCatAttrT);
						$xmlMainCat	->	appendChild( $xmlSubCat );	
					}

						//$xml .= "</cat>";
						$xmlLang	->	appendChild( $xmlMainCat );
							
				}

				//$xml.="</cats>";
				$this	->	xmlMainNode	->	appendChild( $xmlLang );

		}

		 //$xml.="</root>";
			
				
			//-------------
			
		 //return $xml;
		return $this	->	doc	->	saveXML();
	}

	public function GetProgramCategory($flex_id)

	{

		$select_Qry		=	"select * from program_master where flex_id='".$flex_id."'";

		$program			= $this->dbObj->selectQry($select_Qry);

		if($program)

		$category			=	$program[0]['program_category_flex_id'];

		else

		$category			=	0;

		return $category;

	}

}

?>