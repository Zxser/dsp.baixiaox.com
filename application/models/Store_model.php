<?php
/**
 * |---------------------------------------------------
 * | Store_model Model
 * |---------------------------------------------------
 * | Author bluelife
 * | Email thebulelife@outlook.com
 * | Date 2017-08-28
 * |--------------------------------------------------------------
 */
class Store_model extends ADLINKX_Model {
	private $db;
	private $tbale_name;
	public function __construct() {
		parent::__construct();
		$this->db = $this->get_database('aliyun');
		$this->load->model('user_model', 'user');
		$this->load->model('dsp_stats_ad_task_effects_model', 'dsatem');
		$this->tbale_name = 'store';
	}

	public function add($data = array()) {
		$seq_id = $this->get_seq_id();
		$shop_id = parent::$id_space['website'] + $seq_id;
		$param = array(
			"shop_id" => sprintf("%.0f", $shop_id),
			"shop_title" => $data['title'],
			"tanx_category" => $data['tanx_category'],
			"bes_industry_code" => $data['bes_category'],
			"vm_category" => $data['vm_category'],
			"update_time" => date("Y-m-d h:m:s"),
			"own_id" => $data['user_id'],
			"website" => $data['url'],
			"plat" => 'site',
			"other" => "{}",
		);
		// var_dump($param);
		$query = $this->db->insert('store', $param);
		return $query && $this->db->affected_rows() > 0 ? $shop_id : false;
	}

	public function get($where = array(), $fields = '*') {
		$this->db->select($fields);
		$this->db->from('store');
		$this->db->where($where);
		$query = $this->db->get();
		return $query && $query->num_rows() > 0 ? $query->row_array() : array();
	}

	public function lists($where, $num = 20, $offset = 1, $key = 'id', $stor = 'desc', $fields = '*', &$count) {
		unset($where['start_date']);
		unset($where['end_date']);
		$sql = 'select `s`.`shop_title`, `s`.`shop_id`, `s`.`money` AS `store_money`,`s`.`own_id`,`s`.`user_nick`,`s`.`website`,`s`.`update_time`, `u`.`money` AS `account_money`,`u`.`phone`,ifnull(`u`.`money_adv`,0) AS `money_adv`,ifnull(`u`.`money_agent`,0) AS `money_agent`,`u`.`charge_today`,`u`.`charge_yesterday`,`u`.`username`,`u`.`channel_id`,`u`.`company_name`,
    `u`.`contact`,`u`.`add_time`,IFNULL(`dsate`.`ds_click`, 0) AS `click`,IFNULL(ROUND(`dsate`.`ds_charge` / 100, 2),
            0) AS `adv_charge`,IFNULL(`dsate`.`ds_click` * 0.5, 0) AS `agent_charge` from (select * from `huihe_marketing_system`.`store` where ' . $this->build_where($where) . ' and `is_del`=0) as `s` left join `huihe_marketing_system`.`user` as `u` on `u`.`uid`=`s`.`own_id` LEFT JOIN
    `huihe_marketing_system`.`dsp_stats_ad_task_effects` AS `dsate` ON `dsate`.`store_id` = `s`.`shop_id` group by `shop_id` order by ' . $key . ' ' . $stor . ' limit ' . intval(($offset - 1) / $num) . ',' . $num;
		$count_sql = 'SELECT COUNT(*) AS `count` FROM (SELECT COUNT(*) FROM (SELECT * FROM `huihe_marketing_system`.`store` WHERE
        `own_id` = "' . $where['own_id'] . '" AND `is_del` = 0) AS `s` LEFT JOIN `huihe_marketing_system`.`user` AS `u` ON `u`.`uid` = `s`.`own_id` LEFT JOIN `huihe_marketing_system`.`dsp_stats_ad_task_effects` AS `dsate` ON `dsate`.`store_id` = `s`.`shop_id` GROUP BY shop_id) AS a';
		// $sql = "select * from (select `owner`,`shop_name`,uid,username,shop_site as shop_url,company_name,company_addr,contact,phone,email,fax,qq,add_time,ifnull(`channel_id`,'') as channel_id,ifnull(`money_agent`,0) as money_agent,ifnull(`money_adv`,0) as money_adv, charge_today, charge_yesterday from `huihe_marketing_system`.`user` where ".$this->build_where($where).") A left join (SELECT s.own_id,s.shop_id,sum(dsate.ds_click) click,ROUND(sum(dsate.ds_charge)/100,2) adv_charge, sum(dsate.ds_click)*0.5 agent_charge FROM store s left join `dsp_stats_ad_task_effects` dsate on s.shop_id = dsate.store_id and dsate.date between '" . $date_start . "' and '" . $date_end . "' group by s.own_id) as B on A.uid=B.own_id order by $key $stor limit ".intval(($offset-1)*$num).",".$num;
		$count = $this->db->query($count_sql)->result_array()[0]['count'];
		$query = $this->db->query($sql);
		// var_dump($query->result_array());
		return $query && $query->num_rows() > 0 ? $query->result_array() : array();
	}

	public function get_all($where) {
		$start_date = isset($where['start_date']) && !empty($where['start_date']) ? $where['start_date'] : date('Y-m-d', time()) . ' 00:00:00';
		$end_date = isset($where['end_date']) && !empty($where['end_date']) ? $where['end_date'] : date('Y-m-d H:i:s', time());
		$this->db->select('*');
		$this->db->from($this->tbale_name);
		$this->db->where($where);
		$query = $this->db->get();
		$result = $query && $query->num_rows() > 0 ? $query->result_array() : array();
		if (is_array($result) && !empty($result)) {
			for ($i = 0; $i < count($result); $i++) {
				$result[$i]['roi'] = $this->get_roi($result[$i]['shop_id'], $start_date, $end_date);
			}
		}
		return $result;
	}

	public function get_roi($store_id, $start_date, $end_date) {
		$charge = $this->dsatem->query_store_charge($store_id, $start_date, $end_date);
		// var_dump($charge);
		$achieve_payment = $this->dsatem->query_store_achieve_payment($store_id, $start_date, $end_date);
		// var_dump($achieve_payment);
		$roi = $charge != 0 && $achieve_payment != 0 ? round($achieve_payment / $charge, 2) : 0;
		return $roi;
	}

	public function update($data = array(), $where = array()) {
		$this->db->where($where);
		$query = $this->db->update('store', $data);
		return $query && $this->db->affected_rows() > 0 ? true : false;
	}

	public function delete($where) {
		$FB = null;
		for ($i = 0; $i < count($where); $i++) {
			$query = $this->update(array('is_del' => 1), $where[$i]);
			$FB = $query ? true : false;
		}
		return $FB ? true : false;
	}

	public function build_where($where) {
		$filter_list = array('shop_title', 'user_nick');
		$tmp = '';
		foreach ($where AS $key => $value) {
			if (in_array($key, $filter_list)) {
				$tmp .= '`' . $key . '` like "%' . $value . '%" and ';
			} else {
				$tmp .= '`' . $key . '` = ' . $value . ' and ';
			}
		}
		return $tmp != '' ? substr($tmp, 0, strlen($tmp) - 5) : '';
	}

	public function update_money($data, $where) {
		//开启事务
		$this->db->trans_start();
		// 获取用户的帐户余额
		$user = $this->user->get(array('uid' => $where['uid']), array('money'));
		// 获取已分配的金额
		$quota = $this->get(array('shop_id' => $where['shop_id']), 'money');
		var_dump($quota);
		$shop_money = $quota + $data['money'];
		$user_money = $user['money'] - $data['money'];
		$up_user_money_sql = 'UPDATE `huihe_marketing_system`.`user` SET `money`=' . $user_money . ' WHERE `uid`=' . $where['uid'];
		$up_store_money_sql = 'UPDATE `huihe_marketing_system`.`store` SET `money`=' . $shop_money . ' WHERE `shop_id`=' . $where['shop_id'] . ' AND `own_id`=' . $where['uid'];
		$log_sql = 'INSERT INTO `huihe_marketing_system`.`dsp_log_charge` (uid,date,time,money,type,user_money,remark,detail) VALUES (' . $where['uid'] . ',"' . date('Y-m-d', time()) . '","' . date('Y-m-d H:i:s', time()) . '",' . $data['money'] . ',1,' . $new_money . ',"分配配额，网站名称：' . $where['shop_title'] . '","[]")';
		//更新帐户余额
		$this->db->query($up_user_money_sql);
		//更新配额
		$this->db->query($up_store_money_sql);
		//记录操作日志
		$this->db->query($log_sql);
		//提交事务
		$this->db->trans_complete();
		if ($this->db->trans_status()) {
			return true;
		} else {
			return false;
		}
	}
}