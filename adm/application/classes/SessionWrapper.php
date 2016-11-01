<?php
require_once 'Zend/Session.php';

class SessionWrapper extends Zend_Session{
	protected static $_instance;
	public $namespace = null;
	
	protected function __construct(){
		/* Explicitly start the session */
		Zend_Session::start();
		
		/* Create our Session namespace - using 'Default' namespace */
		$this->namespace = new Zend_Session_Namespace('sbrafh');
		
		/* Check that our namespace has been initialized - If not, regenerate the session id
		* Makes  Session fixation more difficult to archive
		*/
		if (!isset($this->namespace->initialized)){
			Zend_Session::regenerateId();
			$this->namespace->initialized = true;
			}		  		  
		}
		
	public static function getInstance(){
		if(null == self::$_instance){
			self::$_instance = new self();
			}
		return self::$_instance;
		}
		
	public function getSessVar($var, $default=null){
		return (isset($this->namespace->$var))?$this->namespace->$var : $default;
		}


	public function setSessVar($var, $value){
		if (!empty($var)&& !empty($value)){
		$this->namespace->$var = $value;
			}
		}
		
	public function emptySess(){
		Zend_Session::NamespaceUnset('sbrafh');
		}	

}
?>
