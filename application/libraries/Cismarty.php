<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/smarty/Smarty.class.php';
/**
 * |--------------------------------------------------------------
 * | Cismarty Controller
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date 2017-01-04
 * |--------------------------------------------------------------
 */
class Cismarty extends smarty {
	protected $CI;
	protected $smarty_conf;
	public function __construct() {
		parent::__construct();
		$this->CI = &get_instance(); //将CI的环境变量过继给当前类中的CI,以实现smarty和ci的整合
		$this->smarty_conf = $this->CI->config->item('smarty'); //加载smarty的配置文件
		$this->setTemplateDir($this->smarty_conf['template_dir']); //配置模板保存目录
		$this->setConfigDir($this->smarty_conf['config_dir']); //配置config目录
		// $this->setPluginsDir();//配置插件目录
		$this->setCompileDir($this->smarty_conf['compile_dir']); //配置模板编译目录
		$this->setCacheDir($this->smarty_conf['cache_dir']); //配置缓存目录
		$this->setLeftDelimiter($this->smarty_conf['left_delimiter']); //配置左边界标识符
		$this->setRightDelimiter($this->smarty_conf['right_delimiter']); //配置右边界标识符
		// $this->setDebugging($this->smarty_conf['debug']); //设置是否开户DEBUG
	}
}