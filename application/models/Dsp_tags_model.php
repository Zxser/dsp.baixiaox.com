<?php
/**
 * |---------------------------------------------------
 * | Dsp_tags_model Model
 * |---------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date 2017-01-07
 * |--------------------------------------------------------------
 */
class Dsp_tags_model extends ADLINKX_Model {
	private $db;
	// 一个标签类型内标签的大小
	public $TAG_SIZE;

	public function __construct() {
		parent::__construct();
		$this->db = $this->get_database('aliyun');
		$this->TAG_SIZE = pow(2, 24);
	}

	/**
	 * 将标签ID转换为标签类和标签值
	 *
	 * @param int	$id	标签ID
	 * @param int	&$type	标签类
	 * @param int	&$value	标签值
	 */
	public function idtotype($id, &$type, &$value)
	{
		$type = intval($id / $this->TAG_SIZE);
		$value = $id % $this->TAG_SIZE;
	}

	/**
	 * 将标签类和标签值转换为标签ID
	 *
	 * @param int	$type	标签类
	 * @param int	$value	标签值
	 * @param int	&$id	标签ID
	 */
	public function typetoid($type, $value, &$id)
	{
		$id = $type * $this->TAG_SIZE + $value;
	}

}