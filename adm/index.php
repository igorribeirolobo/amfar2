<?php
//error_reporting(E_ALL|E_STRICT);
//setlocale (LC_ALL, 'pt_BR');
//date_default_timezone_set('Brazil/East');
set_include_path('.' . PATH_SEPARATOR . '../ZendLibrary'
. PATH_SEPARATOR . './application/models/'
. PATH_SEPARATOR . './application/classes/'
. PATH_SEPARATOR . './application/classes/html2pdf/'
. PATH_SEPARATOR . './application/classes/html2fpdf/'
. PATH_SEPARATOR . './application/classes/htmlMimeMail/'
. PATH_SEPARATOR . get_include_path());
include "Zend/Loader.php";
Zend_Loader::loadClass('Zend_Controller_Front');

Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Registry');
Zend_Loader::loadClass('Zend_Db');
Zend_Loader::loadClass('Zend_Db_Table');
Zend_Loader::loadClass('Zend_Form');
Zend_Loader::loadClass('Zend_Form_Element_Text');
Zend_Loader::loadClass('Zend_Form_Element_Password');
Zend_Loader::loadClass('Zend_Form_Element_Submit');
Zend_Loader::loadClass('SessionWrapper');
Zend_Loader::loadClass('FuncoesUteis');
Zend_Loader::loadClass('html2pdf');
Zend_Loader::loadClass('html2fpdf');
Zend_Loader::loadClass('htmlMimeMail');
Zend_Loader::loadClass('Crypt');
Zend_Loader::loadClass('Zend_Filter_StripTags');
Zend_Loader::loadClass('Zend_Auth');
Zend_Loader::loadClass('Zend_Debug');
Zend_Loader::loadClass('Zend_Date');
Zend_Loader::loadClass('Zend_Layout');


// load configuration
$config = new Zend_Config_Ini('./application/dbconfig.ini', 'mssql');
Zend_Registry::set('config', $config);
$db = Zend_Db::factory($config->db->adapter, $config->db->config->toArray());
Zend_Db_Table::setDefaultAdapter($db);
$db->setFetchMode(Zend_Db::FETCH_OBJ);
Zend_Registry::set('db', $db);


$config2 = new Zend_Config_Ini('./application/dbconfig.ini', 'mysql');
Zend_Registry::set('config2', $config2);
$db2 = Zend_Db::factory($config2->db2->adapter, $config2->db2->config->toArray());
$db2->setFetchMode(Zend_Db::FETCH_OBJ);
Zend_Registry::set('db2', $db2);



//Zend_Registry::set('jsLib', "http://localhost/jsLibrary");
define('jsLib', 'http://www.amfar.com.br/jsLibrary');

// setup controller
$frontController = Zend_Controller_Front::getInstance();
$frontController->throwExceptions(true);
//$frontController->setBaseUrl('/zf-tutorial');
$frontController->setControllerDirectory('./application/controllers');
// run!
$frontController->setParam('useDefaultControllerAlways', true);
$frontController->throwExceptions(true);
Zend_Layout::startMvc();

$frontController->dispatch();


