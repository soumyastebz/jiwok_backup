<?
/**************************************************************************** 
   Project Name	::> Jiwok 
   Module 	::> Function for Gift code cretion and reseller id generation
   Programmer	::>Jasmin
   Date		::> 09-12-2009
   DESCRIPTION::::>>>>
   This is a Function code used to Generate Gift code and reseller id generation
*****************************************************************************/

// function to generate the gift code

function get_code($seed_length=8) {
	$seed_length=8;
    $seed = "GIFTJIWOKREUBROPROGRAMMERSP8J12V2K9REUBRO1357JIWOK";
    $str = 'GC';
    srand((double)microtime()*1000000);
    for ($i=0;$i<$seed_length;$i++) {
        $str .= substr ($seed, rand() % 48, 1);
    }
	return $str;
}


// function to generate reseller id

function get_recode($seed_length=8) {
	$seed_length=8;
	$seed = "1234567890".time();
	//$seed = time();
    $str = 'R';
    srand((double)microtime()*1000000);
    for ($i=0;$i<$seed_length;$i++) {
        $str .= substr ($seed, rand() % 20, 1);
    }
	return $str;
}



?>
