<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * |--------------------------------------------------------------
 * | ADLINKX_Model Model extended class
 * |--------------------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date 2017-01-07
 * |--------------------------------------------------------------
 */
class ADLINKX_Model extends CI_Model {
	private $aliyun;
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	public static $id_space = array(
		"website" => 0x400000000000000,
	);

	/**
	 * 获取指定数据库
	 * @param  [type] $db [description]
	 * @return [type]     [description]
	 */
	public function get_database($db) {
		return $this->load->database($db, TRUE);
	}

	public function get_seq_id() {
		$this->aliyun = $this->get_database('aliyun');
		$this->aliyun->query("REPLACE INTO sequence.sequence (`stub`) VALUES ('a');");
		return $this->aliyun->insert_id();
	}
}