<?php
/**************************************************************************************
Done On :06-09-2011
@Author	:Sreeraj VR
Purpose	:Initial variables
**************************************************************************************/
//Doyou want to define something here?
$prefix		=	CONST_DB_TABLE_PREFIX;
define('CONST_DB_TABLE_ADMIN_ACTIONS',$prefix.'admin_actions');
define('CONST_DB_TABLE_ADMIN_MENUS',$prefix.'admin_menus');
define('CONST_DB_TABLE_ADMIN_PAGES',$prefix.'admin_pages');
define('CONST_DB_TABLE_ADMIN_PAGE_ACTIONS',$prefix.'admin_page_actions');
define('CONST_DB_TABLE_ADMIN_PERMISSION',$prefix.'admin_permission');
define('CONST_DB_TABLE_ADMIN_USERS',$prefix.'admin_users');
define('CONST_DB_TABLE_ADMIN_USERTYPE',$prefix.'admin_usertype');

define('CONST_MODULE_SEO_TABLE_SEO',$prefix	.'module_seo');
define('CONST_MODULE_SEO_TABLE_SECTION',$prefix.'module_seo_group');

define('CONST_MODULE_EMAIL_CATEGORY',$prefix.'module_email_category');
define('CONST_MODULE_EMAIL_TPL_CATEGORY',$prefix.'module_email_tpl_category');
define('CONST_MODULE_BULK_EMAIL',$prefix.'module_bulk_email');
define('CONST_MODULE_BULK_EMAIL_TPL',$prefix.'module_bulk_email_tpl');
define('CONST_MODULE_BULK_EMAIL_CRON_USER',$prefix.'module_email_cron_users');
define('CONST_MODULE_BULK_EMAIL_CRON',$prefix.'module_email_cron');

//DEFAULTS
define('CONST_DB_TABLE_MODULE_DEFAULTS',$prefix.'module_defaults');
define('CONST_DB_TABLE_MODULE_DEFAULTS_GROUP',$prefix.'module_defaults_group');



define('GLB_PAGE_CNT','10');
?>
