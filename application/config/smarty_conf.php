<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 *|---------------------------------------------------------------------------------------------------------------
 *| smarty Config
 *|---------------------------------------------------------------------------------------------------------------
 *| smarty 配置文件
 *| Author bluelife
 *| Email thebulelife@outlook.com
 *| Date 2017-01-04
 *|---------------------------------------------------------------------------------------------------------------
 */
$config['smarty']['template_dir'] = APPPATH . 'views' . DIRECTORY_SEPARATOR ;
$config['smarty']['config_dir'] = APPPATH . '..' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR;
$config['smarty']['compile_dir'] = APPPATH . '..' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR . 'templates_c' . DIRECTORY_SEPARATOR;
$config['smarty']['plugins_dir'] = APPPATH . '..' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR . 'plugins_dir' . DIRECTORY_SEPARATOR;
$config['smarty']['cache_dir'] = APPPATH . '..' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
$config['smarty']['left_delimiter'] = '<{';
$config['smarty']['right_delimiter'] = '}>';
$config['smarty']['template_ext'] = '.html';
$config['smarty']['caching'] = false;
$config['smarty']['lefttime'] = 60;
$config['smarty']['debug'] = true;