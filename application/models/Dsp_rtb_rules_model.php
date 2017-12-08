<?php
/**
 * |---------------------------------------------------
 * | Dsp_rtb_rules_model Model
 * |---------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date 2017-01-07
 * |--------------------------------------------------------------
 */
class Dsp_rtb_rules_model extends ADLINKX_Model{
	private $db;
	private $table_name;
	public function __construct() {
		parent::__construct();
		$this->db = $this->get_database('aliyun');
		$this->table_name = 'dsp_rtb_rules';
	}

	/**
	 * 设置竟价规则
	 *
	 * @param int	$plan_id	计划ID
	 * @param int	$uid		用户ID
	 * @param int	$unit_id	推广组ID
	 * @param array	$tag_ids	设置的标签
	 * @return bool		是否设置成功
	 */
	public function set($plan_id, $uid, $unit_id, $tag_ids){
		$FB = null;
		//删除已有的竟价规则
		$this->del($uid, $unit_id);
		//将每一个时间规则单独写入数据库
		if(is_array($tag_ids)){
			foreach($tag_ids as $tag_id){
				$FB = $this->add($plan_id, $uid, $unit_id, $tag_id);
			}
		}
		return $FB;
	}

	/**
	 * 增加一条竟价规则
	 *
	 * @param int	$uid		用户ID
	 * @param int	$unit_id	推广组ID
	 * @param int	$tag_id		标签ID
	 * @return bool		是否增加成功
	 */
	public function add($plan_id, $uid, $unit_id, $tag_id){
		$tag_type = 0;
		$tag_value = 0;
		$this->load->model('dsp_tags_model', 'dtm');
		// $this->load->model('dsp/dsp_rtb_rules_model', 'drrm');
		// $this->load->model('dsp/diy_unit_model', 'dum');
		// $unit = $this->dum->get($uid, $unit_id);
		$this->dtm->idtotype($tag_id, $tag_type, $tag_value);
		$rule_id = $this->get_seq_id();
		$rule_data = array(
			'id'		=> $rule_id,
			'uid'		=> $uid,
			'plan_id'	=> $plan_id,
			'unit_id'	=> $unit_id,
			'tag_id'	=> $tag_id,
			'tag_type'	=> $tag_type,
			'tag_value'	=> $tag_value,
			'price'		=> 0
		);
		// var_dump($rule_data);
		$query = $this->db->insert($this->table_name,$rule_data);
		return $query && $this->db->affected_rows() > 0 ? true : false;
	}

	/**
	 * 删除一条竟价规则
	 *
	 * @param int	$uid		用户ID
	 * @param int	$unit_id	推广组ID
	 * @return bool		是否删除成功
	 */
	public function del($uid, $unit_id){
		$query = $this->db->delete($this->table_name,array('uid' => $uid, 'unit_id' => $unit_id));

		return $query && $this->db->affected_rows() > 0 ? true : false ;
	}

	public function get($uid,$unit_id){
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->where(array('uid' => $uid, 'unit_id' => $unit_id));
		$query = $this->db->get();
		return $query && $query->num_rows() > 0 ? $query->result_array() : array();
	}
}