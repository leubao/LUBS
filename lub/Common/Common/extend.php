<?php
// +----------------------------------------------------------------------
// | LubTMP  系统扩展函数
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
/** =============================================================票务系统设置===========================================================================*/
    /**
     * 根据产品ID获取产品名称
     */
    function itemName($param){
    	echo M('Item')->where(array('id'=>$param))->getField('name');
    }
    /**
     * 根据分组ID获取票型分组名称
     */
    function groupName($param){
    	echo M('TicketGroup')->where(array('id'=>$param))->getField('name');
    }
    /**
     * 获取季节类型
     */
    function season($param){
    	switch ($param){
    		case 1 :
    			echo "淡季";
    			break;
    		case 2 :
    			echo "旺季";
    			break;
    		case 3 :
    			echo "淡旺季";
    			break;
    	}
    }
    /**
     * 获取票型类型
     * @param $param int 类型参数
     */
    function ticket_type($param){
    	switch ($param){
    		case 1 :
    			echo "散客票";
    			break;
    		case 2 :
    			echo "团队票";
    			break;
    		case 3 :
    			echo "散客、团队票";
    			break;
    		case 4 :
    			echo "政企渠道票";
    			break;
    			
    	}
    }
    /**
     * 获取产品名称
     * @param $param int 产品ID
     */
    function product_name($param,$type=NULL){
        if(!empty($param)){
             $name = M('Product')->where(array('id'=>$param))->getField('name');
             if($type){
                return $name;
             }else{
                echo $name;
             }
        }else{
            echo "未知";
        }   
    }
    
    /**
     * 获取操作员名称  窗口售票
     * @param $param int 操作员ID
     */
    function userName($param,$type=NULL){
        if(!empty($param)){
             $name = M('User')->where(array('id'=>$param))->getField('nickname');
             if($type){
                return $name;
             }else{
                echo $name;
             }
        }else{
            echo "操作员未知";
        }
    }
    /**
     * 获取角色名称
     *  @param $param int 角色ID
     */
    function roleName($param){
    	if(!empty($param)){
    		echo M('Role')->where(array('id'=>$param))->getField('name');
    	}else{
    		echo "角色未知";
    	}

    }
    /**
     * 区域名称
     *  @param $param int 区域ID
     *  @param $type int 数据返回方式 
     */
    function areaName($param,$type=NULL){
    	if(!empty($param)){
            $area = F('Area');
            if(!empty($area)){
                $name = $area[$param]['name'];
            }else{
                $name = M('Area')->where(array('id'=>$param))->getField('name');
            }
    		if($type){
    		  return $name;
    		}else{
    		  echo $name;
    		}
    	}else{
    		echo "区域未知";
    	}
    }
    /**
     * 票型名称
     * Enter description here ...
     * @param $param
     */
    function ticketName($param,$type=NULL){
    	if(!empty($param)){
    		$name = M('TicketType')->where(array('id'=>$param))->getField('name');
    		if($type){
    		 	return $name;
    		 }else{
    		 	echo $name;
    		 }
    	}else{
    		echo "票型未知";
    	}
    }
    /*
    *获取所有票型
    *@param $param 产品id
    */
    function getPrice($param){
        if(!empty($param)){
            $list = F('TicketType'.$param);
            if($list){
                $list = M('TicketType')->where(array('status'=>'1'))->select();
            }
            return $list;
        }else{
            return false;
        }
    }
    /**
     * 座椅显示处理
     * Enter description here ...
     * @param $param 座椅iD
     */
    function seatShow($param,$type=NULL){
    	if(!empty($param)){
    		$seta = explode('-', $param);
    		$name = $seta['0']."排".$seta['1']."号";
    		if($type){
    		 	return $name;
    		 }else{
    		 	echo $name;
    		 }
    	}else{
    		echo "未知";
    	}
    }
    /*查询座椅所属的订单
    * @param $param 座椅iD
    * @param $plan_id 计划id
    */
    function seatOrder($param,$plan_id,$type=NULL){
        if(!empty($param) && !empty($plan_id)){
            $plan = F('Plan_'.$plan_id);
            if(empty($plan)){
                $name = "订单已过期";
            }else{
                $name = M(ucwords($plan['seat_table']))->where(array('seat'=>$param))->getField('order_sn');
            }
            if($type){
                return $name;
             }else{
                echo $name;
             }
        }else{
            echo "未知";
        }
    }
    /**
     * 根据ID显示销售计划信息
     * @param $param 计划ID
     */
    function planShow($param,$type=NULL){
    	if(!empty($param)){
            $plan = F('Plan_'.$param);
            if(!empty($plan)){
                $info = $plan;
            }else{
               $info = M('Plan')->where(array('id'=>$param))->field('plantime,games,starttime,endtime')->find(); 
            }
    		$name = date('Y-m-d',$info['plantime'])."(".get_chinese_weekday($info['plantime']).")". "&nbsp;&nbsp;第".$info['games']."场&nbsp;&nbsp;".date('H:i',$info['starttime'])."-".date('H:i',$info['endtime']);
    		if($type){
    		 	return $name;
    		}else{
    		 	echo $name;
    		}
    	}else{
    		echo "场次未知";
    	}
    }
    /**
     * 短信发送，演出计划显示
     */
    function planShows($param){
    	if(!empty($param)){
            $plan = F('Plan_'.$param);
            if(!empty($plan)){
                $info = $plan;
            }else{
               $info = M('Plan')->where(array('id'=>$param))->field('plantime,starttime,games')->find();
            }
            $Proconf = cache('ProConfig');
            if($Proconf['plan_start_time'] == '1'){
                $name = date('m月d日',$info['plantime'])."第".$info['games']."场,开演时间".date('H:i',$info['starttime']);
            }else{
                $name = date('m月d日',$info['plantime'])."第".$info['games']."场";
            }
    		return $name;
    	}else{
    		return "场次未知";
    	}
    }
    /**
     * 汉化星期
     */
    function get_chinese_weekday($datetime){
    	$weekday  = date('w', $datetime);
   		$weeklist = array('日', '一', '二', '三', '四', '五', '六');
   		return '星期' . $weeklist[$weekday];
	}
    /*
     * 获取取票人名称
     * @param $param int 操作员ID
     */
    function crmName($param,$type=NULL){
        if(!empty($param)){
            $name = M('Crm')->where(array('id'=>$param))->getField('name');
            if($type){
                return $name;
            }else{
                echo $name;
            }
        }else{
            echo "渠道商";
        }   
    }
    /*
     * 客户分组属性
     * @param $param int 属性id
     */
    function crm_group_type($param){
        switch ($param) {
            case 0:
                echo "未知";
                break;
            case 1:
                echo "企业";
                break;
            case 2:
                echo "个人";
                break;
            case 3:
                echo "政府";
                break;
        }
    }
    /**
     * 产品状态（0,1）、计划状态(1，2)、订单状态(0,2,3,4,5,6)
     * 0 禁用    作废
     * 1 可用  未授权
     * 2 售票中 未出票 
     * 3 已出票
     * 4 已过期 
     */
    function status($param){
    	switch ($param) {
    		case 0:
    			echo "已作废";
    			break;
    		case 1:
    			echo "未授权";
    			break;
    		case 2:
    			echo "售票中";
    			break;
    		case 3:
    			echo "暂停中";
    			break;
    		case 4:
    			echo "已过期 ";
    			break;
    	}
    }
    /*
    *@param $cid int 渠道商ID
    *echo  路径信息 
    */
    function itemnav($cid){
        if(!empty($cid)){
            $crm = M('Crm')->where(array('id'=>$cid))->field('id,name,level,f_agents,product_id')->find();
            $Config = cache("Config");
            switch ($crm['level']){
                case $Config['level_1'] :
                    //一级渠道商
                    $return = $crm['name'];
                    break;
                case $Config['level_2'] :
                    //二级级渠道商
                    $return = $crm['name']."/".crmName($crm['f_agents'],1);
                    break;
                case $Config['level_3'] :
                    //三级渠道商  获取二级的上一级ID  
                    $ccid = Libs\Service\Operate::do_read('Crm',0,array('id'=>$crm['f_agents']),'',array('f_agents'));
                    $return = $crm['name']."/".crmName($crm['f_agents'],1)."/".crmName($ccid,1);
                    break;
            }
            echo $return;
        }else{
            echo "未知";
        }
    }
    /**
     * 1正常2为渠道版订单未支付情况3已取消5已支付但未排座6政府订单
     * Enter description here ...
     * @param $param
     */
    function order_status($param){
    	switch ($param) {
    		case 0:
    			echo "<span class='label label-danger'>已作废</span>";
    			break;
    		case 1:
    			echo "<span class='label label-success'>预定成功</span>";
    			break;
    		case 2:
    			echo "<span class='label label-warning'>待支付</span>";
    			break;
    		case 3:
    			echo "<span class='label label-danger'>已撤销</span>";
    			break;
    		case 4:
    			echo "<span class='label label-default'>已过期 </span>";
    			break;
    		case 5:
    			echo "<span class='label label-warning'>待审核</span>";
    			break;
    		case 6:
    			echo "<span class='label label-info'>待排座</span>";
    			break;
    		case 7:
    			echo "<span class='label label-primary'>取消中 </span>";
    			break;
    		case 9:
    			echo "<span class='label label-default'>完结</span>";
    			break;
            case 11:
                echo "<span class='label label-default'>窗口待完成</span>";
                break;
    		
    	}
    }
    /**
     * 操作类型（1：充值；2：花费3:返佣4：退票 5:退款）
     */
    function operation($param){
    	switch ($param) {
    		case 1:
    			echo "<span class='label label-success'>充值</span>";
    			break;
    		case 2:
    			echo "<span class='label label-info'>花费</span>";
    			break;
    		case 3:
    			echo "<span class='label label-warning'>补贴</span>";
    			break;
    		case 4:
    			echo "<span class='label label-danger'>退票 </span>";
    			break;
    		case 5:
    			echo "<span class='label label-primary'>退款</span>";
    			break;
    		
    	}
    }
    /**
     * 返利状态（1写入2出票成功3财务审核成功4返佣发放成功）
     */
    function rebate($param){
    	switch ($param) {
    		case 4:
    			echo "<span class='label label-success'>补贴成功</span>";
    			break;
    		case 2:
    			echo "<span class='label label-info'>出票成功</span>";
    			break;
    		case 3:
    			echo "<span class='label label-warning'>审核成功</span>";
    			break;
    		case 1:
    			echo "<span class='label label-default'>下单成功</span>";
    			break;
    		
    	}
    }
    /*剧院产品根据场次获取各区域可售座位数
    *@param $table string 表名称
    *@param $area int 区域id
    *@param $type 
    */
    function area_count_seat($table,$area,$type = null){  
        if(!empty($table) && !empty($area)){
            $map = array('status'=>0,'area'=>$area);
            $num = M(ucwords($table))->where($map)->count();
            if($type){
                return $num;
            }else{
                echo $num;
            }
        }else{
            return false;
        }
    }
   /* 返回场次*/
    function games($param,$type=NULL){
        if(!empty($param)){
            $games = M('Plan')->where(array('id'=>$param))->getField('games');
            if($type){
                return $games;
            }else{
                echo "第".$games."场";
            }
        }else{
            echo "未知";
        }
    }
    /**
     * 根据计划统计已售数
     */
    
    /**==========================================================用于系统内部回调==========================================================================****/
    /**
	 * 得到新订单号
	 * @param $paroductId int 产品ID
	 * @param $games int 场次
	 * @param $checkType int 检票类型 1 一人一票 2一团一票
	 * @return  string
	 */
	function get_order_sn($paroductId,$games,$checkType = 1,$ticket_type = 1){
		  if($ticket_type == '1'){
		    return substr(date('Ymd'),3).$checkType. $paroductId . $games . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
		  }else{
		  	//景区票务
		    return substr(date('Ymd'),3).$checkType. $paroductId . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
		  }
	}
	/**
	 * 根据订单号判断订单有效性
	 * @param $sn char 订单号
	 */
	function check_sn($sn){
		if(empty($sn)){
			return false;
		}
		$today =  substr(date('Ymd',time(),3));
		$sns = substr($sn,1,5);
		if($sns < $today){
			return false;
		}else{
			return true;
		}
	}
    /**
     * 获取产品名称
     * @param $param int 产品ID
     */
    function productName($param){
    	if(!empty($param)){
    		return M('Product')->where(array('id'=>$param))->getField('name');
    	}else{
    		return "未知";
    	}	
    }
    /**
     * 获取订单创建场景
     * @param $param int 
     */
    function addsid($param){
    	switch($param){
    		case 1:
    			echo "窗口";
    			break;
    		case 2:
    			echo "渠道版";
    			break;
    		case 3:
    			echo "网站";
    			break;
    		case 4:
    			echo "微信";
    			break;
            case 5:
                echo "API";
                break;
    		default:
    			echo "未知场景";
    			break;
    	}		
    }
    /**
     * 支付方式
     * @param $param int 支付方式
     */
    function pay($param,$type = NULL){
        if($type){
            switch($param){
                case 1:
                    return "现金";
                    break;
                case 2:
                    return "授信额";
                    break;
                case 3:
                    return "签单";
                    break;
                default:
                    return "未知";
                    break;
            }
        }else{
            switch($param){
                case 1:
                    echo "现金";
                    break;
                case 2:
                    echo "授信额";
                    break;
                case 3:
                    echo "签单";
                    break;
                default:
                    echo "未知";
                    break;
            }
        }
    	
    }
     /**
     * 销售渠道
     * @param $param int 销售渠道
     */
    function channel_type($param,$type = null){
        if($type){
            switch($param){
                case 1:
                    return "散客";
                    break;
                case 2:
                    return "团队";
                    break;
                case 4:
                    return "渠道商";
                    break;
                case 6:
                    return "政企";
                    break;
                case 7:
                    return "渠道底价";
                    break;
                default:
                    return "未知";
                    break;
            }

        }else{
            switch($param){
                case 1:
                    echo "散客";
                    break;
                case 2:
                    echo "团队";
                    break;
                case 4:
                    echo "代理商";
                    break;
                case 6:
                    echo "政企";
                    break;
                case 7:
                    return "渠道底价";
                    break;
                default:
                    echo "未知";
                    break;
            }
        }
    }
    /**
     * 金额扣除条件
     * @param $param 渠道商ID
     * @param $channel_id int 渠道商
     */
    function money_map($param){
    	if(!empty($param)){
            $param = M('Crm')->where(array('id'=>$param))->find();
	    	/*
			 * 读取上级渠道商的ID
			 */
    		$Config = cache("Config");
			switch ($param['level']){
				case $Config['level_1'] :
					//一级渠道商
					return $param['id'];
					break;
				case $Config['level_2'] :
					//二级级渠道商
					return $param['f_agents'];
					break;
				case $Config['level_3'] :
					//三级渠道商  获取二级的上一级ID  
					$cid = Libs\Service\Operate::do_read('Crm',0,array('id'=>$param['f_agents']),'',array('f_agents'));
					return $cid['f_agents'];
					break;
			}
    	}else{
    		return false;
    	} 		 
   }
   /*获取渠道商余额
    *@param $param int 渠道商ID
   */
   function balance($param){
        if(empty($param)){return '0.00';}
        $return = M('Crm')->where(array('id'=>$param))->getField('cash');
        return $return;
   }
   /**
    * 获取渠道商集合
    * @param $param int id
    * @param $type int 1 员工ID  2 渠道商id
    */
   function channel_set($param,$type = 1){
        if($type == '1'){
            $crm_id = M('User')->where(array('id'=>$param))->getField('cid');
        }else{
            $crm_id = $param;
        }
   		$crm = M('Crm')->where(array('id'=>$param))->find();
   		$Config = cache("Config");
   		switch ($crm['level']){
			case $Config['level_1'] :
				//一级渠道商
				$channel = M('Crm')->where(array('f_agents'=>$crm['id'],'status'=>'1'))->field('id')->select();
                $channel_id = implode(',', array_column($channel, 'id'));
				return $channel_id;
				break;
			case $Config['level_2'] :
				//二级级渠道商
				$channel = M('Crm')->where(array('f_agents'=>$crm['id'],'status'=>'1'))->field('id')->select();
				$channel_id = implode(',', array_column($channel, 'id'));
				return $channel_id;
				break;
			case $Config['level_3'] :
				//三级渠道商  获取二级的上一级ID
				return $crm['id'];
				break;
		}
   }
   /*在开启代理商制度时，根据一级渠道商查询所有渠道商id集合
   * @param $param int id
    * @param $type int 1 员工ID  2 渠道商id
    *
   */
   function agent_channel($param,$type = 1){
        if($type == '1'){
            $crm_id = M('User')->where(array('id'=>$param))->getField('cid');
        }else{
            $crm_id = $param;
        }
        $crm = M('Crm')->where(array('id'=>$param))->find();
        $u_1 = $crm['id'];
        $u_2 = M('Crm')->where(array('f_agents'=>$crm['id'],'status'=>1))->field('id')->select();
        if($u_2){
            $arr_map_2 = implode(',',array_column($u_2,'id'));//转换为一维数组
            $u_3 = M('Crm')->where(array('f_agents'=>array('in',$arr_map_2),'status'=>1))->field('id')->select();
            $arr_map_3 = implode(',',array_column($u_3,'id'));//转换为一维数组
        }
        $arr_map = $u_1.','.$arr_map_2.','.$arr_map_3;
        return $arr_map;
   }
   /**
     * 获取渠道商
     * @param $channel_id int 渠道商ID
     * @param $level int 代理商级别
     */
    function channel($channel_id,$level){
        $Config = cache("Config");
        switch ($level){
            case $Config['level_1'] :
                //一级渠道商
                //获取一级渠道商所有人员ID
                $u_1 = $channel_id;
                $u_2 = M('Crm')->where(array('f_agents'=>$channel_id,'status'=>1))->field('id')->select();
                if($u_2){
                    $arr_map_2 = implode(',',array_column($u_2,'id'));//转换为一维数组
                    $u_3 = M('Crm')->where(array('f_agents'=>array('in',$arr_map_2),'status'=>1))->field('id')->select();
                    $arr_map_3 = implode(',',array_column($u_3,'id'));//转换为一维数组
                }
                if(empty($arr_map_2)){
                    $arr_map = $u_1;
                }elseif (empty($arr_map_3)) {
                    $arr_map = $u_1.','.$arr_map_2;
                }else{
                    $arr_map = $u_1.','.$arr_map_2.','.$arr_map_3;
                }
                return $arr_map;
                break;
            case $Config['level_2'] :
                //二级级渠道商
                $u_2 = $channel_id;
                $u_3 = M('Crm')->where(array('f_agents'=>$channel_id,'status'=>1))->field('id')->select();
                $arr_map_3 = implode(',',array_column($u_1,'id'));//转换为一维数组
                if(empty($arr_map_3)){
                    $arr_map = $u_2;
                }else{
                    $arr_map = $u_2.','.$arr_map_3;
                }
                return $arr_map;
                break;
            case $Config['level_3'] :
                //三级渠道商  获取二级的上一级ID  
                $arr_map = $channel_id;
                return $arr_map;
                break;
        }
    }
    /*获取渠道商所有员工
    @param $channel_id array 渠道商id集合
    return $user_id array 用户id*/
    function channel_user($channel_id,$level){
        $Config = cache("Config");
        switch ($level){
            case $Config['level_1'] :
                //一级渠道商
                //获取一级渠道商所有人员ID
                $u_1 = $channel_id;
                $u_2 = M('Crm')->where(array('f_agents'=>$channel_id,'status'=>1))->field('id')->select();
                if($u_2){
                    $arr_map_2 = implode(',',array_column($u_2,'id'));//转换为一维数组
                    $u_3 = M('Crm')->where(array('f_agents'=>array('in',$arr_map_2),'status'=>1))->field('id')->select();
                    $arr_map_3 = implode(',',array_column($u_3,'id'));//转换为一维数组
                }
                $arr_map = $u_1.','.$arr_map_2.','.$arr_map_3;
                $user_l = M('User')->where(array('cid'=>array('in',$arr_map),'status'=>1))->field('id')->select();
                $user = implode(',',array_column($user_l,'id'));
                return $user;
                break;
            case $Config['level_2'] :
                //二级级渠道商
                $u_2 = $channel_id;
                $u_3 = M('Crm')->where(array('f_agents'=>$channel_id,'status'=>1))->field('id')->select();
                $arr_map_3 = implode(',',array_column($u_1,'id'));//转换为一维数组
                $arr_map = $u_2.','.$arr_map_3;
                $user_l = M('User')->where(array('cid'=>array('in',$arr_map),'status'=>1))->field('id')->select();
                $user = implode(',',array_column($user_l,'id'));
                return $user;
                break;
            case $Config['level_3'] :
                //三级渠道商  获取二级的上一级ID  
                $arr_map = $channel_id;
                $user_l = M('User')->where(array('cid'=>array('in',$arr_map),'status'=>1))->field('id')->select();
                $user = implode(',',array_column($user_l,'id'));
                return $user;
                break;
        }
    } 
   /*获取二次打印授权人
   * @param 
    */
    function pwd_name($id){
        $name = M('Pwd')->where(array('id'=>$id))->getField('name');
        echo $name;
    }
    //价格政策
    function price_group($param,$type = null)
    {
        if(!empty($param)){
            $name = M('TicketGroup')->where(array('id'=>$param))->getField('name');
            if($type){
                return $name;
            }else{
                echo $name;
            }
        }else{
            echo "未知";
        }
    }
    /*
    * 库存查询
    * @param $plan_id int 计划id
    * @param $area int 区域id
    */
    function sku($plan_id = null, $area = null){
        if(empty($plan_id) || empty($area)){return false;}
        $plan = F('Plan_'.$plan_id);
        if(empty($plan)){return false;}
        $count = M(ucwords($plan['seat_table']))->where(array('area'=>$area,'status'=>'0'))->count();
        return $count;
    }
    /*根据订单号获取座位信息
    * @param $plan_id int 计划id
    * @param $area int 区域id*/
    function sn_seat($plan_id,$sn){
      if(empty($plan_id) || empty($sn)){return false;}
      $plan = F('Plan_'.$plan_id);
      if(empty($plan)){return false;}
      $list = M(ucwords($plan['seat_table']))->where(array('order_sn'=>$sn))->field('area,seat')->select();
      foreach ($list as $k => $v) {
        $info[] = areaName($v['area'],1).seatShow($v['seat'],1);
      }
      return $info;
    }
    /*根据计划id获取场次详情
    *@param $param int  计划ID
    *@param $type 返回类型 0 echo 1 return
    */
    function plan_info($param,$type = null){
        if(empty($param)){return false;}
        $plan = F('Plan_'.$param);
        $area = unserialize($plan['param']);
        foreach ($area['seat'] as $k => $v) {
            $return = areaName($v,1)."剩余";
        }
        if($type){
            return $name;
        }else{
            echo $name;
        }
        
    }
    /*
    * 从订单详情中获取座位区域信息
    * @param $info string 订单详情
    */
    function order_area($info){
        if(empty($info)){return false;}
        $data = unserialize($info);
        return $data['data'];
    }
    //根据
   /*####################################报表*/


   /*****************************************************销售计划相关*************************************/
   //获取当天的销售计划
    function today_plan(){
        $today = strtotime(date('Ymd'));
        $plan = M('Plan')->where(array('plantime'=>array('egt',$today)))->field('id')->select();
        return $plan;
    }