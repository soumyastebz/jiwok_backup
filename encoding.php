<?php 
echo mb_detect_encoding("à" , "auto");
mb_list_encodings()
foreach(mb_list_encodings() as $char)
{
echo mb_convert_encoding("à" ,"UTF-8",$char) .":" .$char."<br/>";
}
?>