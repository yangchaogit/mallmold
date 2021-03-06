<?php
/*
*	@statistic.php
*	Copyright (c)2013 Mallmold Ecommerce(HK) Limited. 
*	http://www.mallmold.com/
*	
*	This program is free software; you can redistribute it and/or
*	modify it under the terms of the GNU General Public License
*	as published by the Free Software Foundation; either version 2
*	of the License, or (at your option) any later version.
*	More details please see: http://www.gnu.org/licenses/gpl.html
*	
*	If you want to get an unlimited version of the program or want to obtain
*	additional services, please send an email to <service@mallmold.com>.
*/

class statistic extends model
{
	private function actions()
	{
		return array('click', 'cart', 'buy', 'delivery', 'refund');
	}
	
	public function add($goods_id, $action)
	{
		$actions = $this->actions();
		if(!$goods_id || !in_array($action, $actions)){
			return false;
		}
		
		$row = $this->db->table('goods_statistic')->where("goods_id='$goods_id'")->get();
		if($row){
			$number = $row[$action] + 1;
			$this->db->table('goods_statistic')->where("goods_id='$goods_id'")->update(array($action=>$number));
		}else{
			$data = array(
				'goods_id' => $goods_id,
				$action => 1,
			);
			$this->db->table('goods_statistic')->insert($data);
		}
		return true;
	}
	
	public function get_top5($type)
	{
		$actions = $this->actions();
		if(!in_array($type, $actions)){
			return false;
		}
		
		$sql = "select g.goods_id,g.title_key_,g.sku,s.$type 
				from ".$this->db->tbname('goods')." as g
				left join ".$this->db->tbname('goods_statistic')." as s on s.goods_id=g.goods_id 
				order by $type desc 
				limit 5";
		$query = $this->db->query($sql);
		$list = array();
		while($rs = $this->db->fetch($query)){
			$list[] = $this->model('dict')->getdict($rs);
		}
		return $list;
	}
	
	public function get_attr_top5()
	{
		return $this->model('mdata')->table('attribute')
									->where('status=1 and can_filter=1')
									->order('click desc')
									->limit(5)
									->getlist();
	}
}
?>