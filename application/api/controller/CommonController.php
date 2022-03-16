<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */


namespace app\api\controller;

use think\Cache;

use app\api\controller\BaseController;



class CommonController extends BaseController{

	//获取平台共用数据
	public function BackData(){
		$param 	=	input('param.');
		$lang	=	isset($param['lang']) && $param['lang'] ? $param['lang'] : 'id';
		//网站公告
		$noticelist		= model('Notice')->where(array(['state','=',1],['lang','=',$lang]))->order('add_time','desc')->select()->toArray();
		$data			=	[];
		$k=$l=$j=$s=0;
		$data['info']['noticelist'] =	[];
		$data['info']['helpList'] =	[];
		$data['info']['videovTutorial'] =	[];
		$data['info']['serviceList'] =	[];
		foreach ($noticelist as $key => $value) {

			switch($value['gropid']){
				case 1:
					$data['info']['noticelist'][$k]['id']   			= $value['id'];
					$data['info']['noticelist'][$k]['title']   			= $value['title'];
					$data['info']['noticelist'][$k]['content']   		= htmlspecialchars_decode($value['content']);
					$data['info']['noticelist'][$k]['add_time'] 		= date('Y-m-d H:i:s',$value['add_time']);
					++$k;
				break;
				case 2:
					$data['info']['helpList'][$l]['id']   			= $value['id'];
					$data['info']['helpList'][$l]['title']   			= $value['title'];
					$data['info']['helpList'][$l]['content']   		= htmlspecialchars_decode($value['content']);
					$data['info']['helpList'][$l]['add_time'] 		= date('Y-m-d H:i:s',$value['add_time']);
					++$l;
				break;
				case 3:
				    $data['info']['videovTutorial'][$j]['id']   			= $value['id'];
					$data['info']['videovTutorial'][$j]['title']   			= $value['title'];
					$data['info']['videovTutorial'][$j]['content']   		= htmlspecialchars_decode($value['content']);
					$data['info']['videovTutorial'][$j]['add_time'] 		= date('Y-m-d H:i:s',$value['add_time']);
					++$j;
				break;
				case 4:
				    $data['info']['serviceList'][$s]['id']   			= $value['id'];
					$data['info']['serviceList'][$s]['title']   			= $value['title'];
					$data['info']['serviceList'][$s]['content']   		= htmlspecialchars_decode($value['content']);
					$data['info']['serviceList'][$s]['add_time'] 		= date('Y-m-d H:i:s',$value['add_time']);
					++$s;
				break;
			}
		}
		

		$headData	= ['head_1.png', 'head_2.png', 'head_3.png', 'head_4.png', 'head_5.png', 'head_6.png', 'head_7.png', 'head_8.png', 'head_9.png', 'head_10.png'];

		$usernameData	= ['130','131','132','133','134','135','136','137','138','139','145','146','147','150','151','152','153','155','156','157','158','159','162','165','166'];

		$UserVipcrData	=	['普通会员','主播','网红','明星','奥斯卡'];

		$UserViperData	=	['General members','Anchor','Internet celebrity','Star','Oscar'];

		// 最新播报
	//	$UserVipData	= model('UserVip')->where(array(['etime','>=',strtotime(date("Y-m-d",time()))],['state','=',1]))->order('etime','DESC')->limit(10)->select()->toArray();
	    $UserVipData	= model('UserIndex')->where(array(['trade_type','=',8]))->order('id','DESC')->limit(10)->select()->toArray();
		$userviplist	=[];
		if($UserVipData){
			foreach ($UserVipData as $key2 => $value2) {

				$userviplist[$key2]['username']			= substr(trim($value2['username']),0,0).'****'.substr(trim($value2['username']),-4);
				
				$child_vip_name = '';
				
				$child = model('users')->where('id', '=', $value2['sid'])->find();
				if ($child) {
				    $child_vip = model('user_grade')->where('grade', '=', $child['vip_level'])->find();
				    
				    if ($child_vip) {
				        
				        	if($lang=='en'){
				                 $child_vip_name = $child_vip['en_name'];
				        	}elseif($lang=='cn') {
				        	      $child_vip_name = $child_vip['name'];
				        	}elseif($lang=='ft') {
				        	      $child_vip_name = $child_vip['ft_name'];
				        	}elseif($lang=='id') {
				        	      $child_vip_name = $child_vip['ydn_name'];
				        	}elseif($lang=='vi') {
				        	      $child_vip_name = $child_vip['yn_name'];
				        	}elseif($lang=='es') {
				        	      $child_vip_name = $child_vip['xby_name'];
				        	}elseif($lang=='jp') {
				        	      $child_vip_name = $child_vip['ry_name'];
				        	}elseif($lang=='th') {
				        	      $child_vip_name = $child_vip['ty_name'];
				        	}elseif($lang=='yd') {
				        	      $child_vip_name = $child_vip['yd_name'];
				        	}
				        	
				    }
				    
				}
				
				$userviplist[$key2]['child_vip_name'] = $child_vip_name;

				if($lang=='en'){
				    $userviplist[$key2]['name']			= $value2['trade_amount'];
				}else{
					$userviplist[$key2]['name']			= $value2['trade_amount'];
				}
			}
		}
		$userviplist2	=	[];
		// 最新播报
	//	for ($j=0;$j < 10;$j++) {

	//		$nameKey		= array_rand($usernameData);
	//		$username		= $usernameData[$nameKey];

	//		$userviplist2[$j]['username']		= $username.'****'.mt_rand(1000,9999);
	//		if($lang=='en'){
	//			$nameeKey		= array_rand($UserViperData);
	//			$name			= $UserViperData[$nameeKey];
	//		}else{
	//			$namecKey		= array_rand($UserVipcrData);
	//			$name			= $UserVipcrData[$namecKey];
	//		}
	//		$userviplist2[$j]['name']			= $name;
	//	}
		$data['info']['userviplist']	=	array_merge($userviplist,$userviplist2);
		// 会员榜单
		for ($i=0;$i < 20;$i++) {

			$headKey		= array_rand($headData);
			$headerImage	= $headData[$headKey];

			$nameKey		= array_rand($usernameData);
			$username		= $usernameData[$nameKey];

			$data['info']['memberList'][$i]['username']		= '****'.mt_rand(1000,9999);
			$data['info']['memberList'][$i]['header'] 		= $headerImage;
			$data['info']['memberList'][$i]['number'] 		= mt_rand(10,100);
			$data['info']['memberList'][$i]['profit'] 		= round($data['info']['memberList'][$i]['number'] * 1.8,3);

		}
		
		
		// 商家榜单
		for ($j=0;$j < 20;$j++) {

			$headKey		= array_rand($headData);
			$headerImage	= $headData[$headKey];

			$nameKey		= array_rand($usernameData);
			$username		= $usernameData[$nameKey];

			$data['info']['businessList'][$j]['username']		= '****'.mt_rand(1000,9999);
			$data['info']['businessList'][$j]['header'] 		= $headerImage;
			$data['info']['businessList'][$j]['number'] 		= mt_rand(1000,9999);
			$data['info']['businessList'][$j]['profit'] 		= round($data['info']['businessList'][$j]['number'] * 2,3);

		}
		//任务类型
		$taskclasslist = model('TaskClass')->where(array(['state','=',1]))->order('num','ASC')->select()->toArray();
		$TaskClassdata = [];
		foreach ($taskclasslist as $key => $value) {
			$TaskClassdata[$key]['group_id']   			= $value['id'];
			$TaskClassdata[$key]['icon']   				= 'http://'.$_SERVER['HTTP_HOST'].$value['h_icon'];
			
			if($lang=='en'){
			$TaskClassdata[$key]['group_name']			= $value['group_name_en'];
			$TaskClassdata[$key]['group_info']			= $value['group_info_en'];
			$TaskClassdata[$key]['h_group_name']		= $value['group_name_en'];
			$TaskClassdata[$key]['h_group_info']		= $value['group_info_en'];
			}elseif($lang=='cn'){
			$TaskClassdata[$key]['group_name']			= $value['group_name'];
			$TaskClassdata[$key]['group_info']			= $value['group_info'];
			$TaskClassdata[$key]['h_group_name']		= $value['group_name']; 
			$TaskClassdata[$key]['h_group_info']		= $value['group_info'];
			}elseif($lang=='id'){
			$TaskClassdata[$key]['group_name']			= $value['group_name_ydn'];
			$TaskClassdata[$key]['group_info']			= $value['group_info_ydn'];
			$TaskClassdata[$key]['h_group_name']		= $value['group_name_ydn'];
			$TaskClassdata[$key]['h_group_info']		= $value['group_info_ydn'];
			}elseif($lang=='ft'){
			$TaskClassdata[$key]['group_name']			= $value['group_name_ft'];
			$TaskClassdata[$key]['group_info']			= $value['group_info_ft'];
			$TaskClassdata[$key]['h_group_name']		= $value['group_name_ft'];
			$TaskClassdata[$key]['h_group_info']		= $value['group_info_ft'];
			}elseif($lang=='vi'){
			$TaskClassdata[$key]['group_name']			= $value['group_name_yn'];
			$TaskClassdata[$key]['group_info']			= $value['group_info_yn'];
			$TaskClassdata[$key]['h_group_name']		= $value['group_name_yn'];
			$TaskClassdata[$key]['h_group_info']		= $value['group_info_yn'];
			}elseif($lang=='ja'){
			$TaskClassdata[$key]['group_name']			= $value['group_name_ry'];
			$TaskClassdata[$key]['group_info']			= $value['group_info_ry'];
			$TaskClassdata[$key]['h_group_name']		= $value['group_name_ry'];
			$TaskClassdata[$key]['h_group_info']		= $value['group_info_ry'];
			}elseif($lang=='es'){
			$TaskClassdata[$key]['group_name']			= $value['group_name_xby'];
			$TaskClassdata[$key]['group_info']			= $value['group_info_xby'];
			$TaskClassdata[$key]['h_group_name']		= $value['group_name_xby'];
			$TaskClassdata[$key]['h_group_info']		= $value['group_info_xby'];
			}elseif($lang=='th'){
			$TaskClassdata[$key]['group_name']			= $value['group_name_ty'];
			$TaskClassdata[$key]['group_info']			= $value['group_info_ty'];
			$TaskClassdata[$key]['h_group_name']		= $value['group_name_ty'];
			$TaskClassdata[$key]['h_group_info']		= $value['group_info_ty'];
			}elseif($lang=='yd'){
			$TaskClassdata[$key]['group_name']			= $value['group_name_yd'];
			$TaskClassdata[$key]['group_info']			= $value['group_info_yd'];
			$TaskClassdata[$key]['h_group_name']		= $value['group_name_yd'];
			$TaskClassdata[$key]['h_group_info']		= $value['group_info_yd'];
			}
			
			
			
			
			
			$TaskClassdata[$key]['state']				= $value['state'];
			$TaskClassdata[$key]['h_icon']   			= 'http://'.$_SERVER['HTTP_HOST'].$value['h_icon'];
			$TaskClassdata[$key]['is_f']				= $value['is_f'];
			$TaskClassdata[$key]['is_fx']				= $value['is_fx'];
		}
		$data['info']['taskclasslist'] = $TaskClassdata;


		$data['info']['setting'] = model('Setting')->field('q_server_name,service_hotline,official_QQ,WeChat_official,Mobile_client,aboutus,company,contact,problem,guides,hezuomeiti,zhifufangshi,record_number,Company_name,Customer_QQ,Accumulated_investment_amount,Conduct_investment_amount,Cumulative_expected_earnings,registered_smart_investors,service_url,seal_img,info_w,min_w,max_w,reg_url,is_sms,ft,cn,en,yny,vi,jp,es,ty,currency,yd')->find();
         $data['info']['currency']=$data['info']['setting']['currency'];
		//会员等级
		$UserViplist = model('UserGrade')->where(array('state'=>1))->order('id','ASC')->select()->toArray();
		$UserViplistdata = [];
		foreach ($UserViplist as $key => $value) {
			$UserViplistdata[$key]['grade']				= $value['grade'];
			$UserViplistdata[$key]['amount']			= $value['amount'];
			if($lang=='en'){
				$UserViplistdata[$key]['name']			= $value['en_name'];
			}elseif($lang=='cn'){
				$UserViplistdata[$key]['name']			= $value['name'];
			}elseif($lang=='ft'){
				$UserViplistdata[$key]['name']			= $value['ft_name'];
			}elseif($lang=='ja'){
				$UserViplistdata[$key]['name']			= $value['ry_name'];
			}elseif($lang=='id'){
				$UserViplistdata[$key]['name']			= $value['ydn_name'];
			}elseif($lang=='vi'){
				$UserViplistdata[$key]['name']			= $value['yn_name'];
			}elseif($lang=='es'){
				$UserViplistdata[$key]['name']			= $value['xby_name'];
			}elseif($lang=='th'){
				$UserViplistdata[$key]['name']			= $value['ty_name'];
			}elseif($lang=='yd'){
				$UserViplistdata[$key]['name']			= $value['yd_name'];
			}
			$UserViplistdata[$key]['number']			= $value['number'];
			$UserViplistdata[$key]['commission']		= $value['commission'];
			
			$UserViplistdata[$key]['income']			= $value['number'] * $value['commission'];
			$UserViplistdata[$key]['income1']			= $value['number'] * $value['commission'] * 30 ;
		}
		$data['info']['UserGradeList'] = $UserViplistdata;
		
		$authenticationdata	=	[];
		switch($lang){
			case 'en':
				$authenticationdata = ['Mobile phone authentication','Wechat authentication','Real name authentication','Identity authentication'];
			break;
			case 'cn':
				$authenticationdata	=['手机认证','微信认证','实名认证','身份认证'];
			break;
			case 'ft':
				$authenticationdata	=['手機認證','微信認證','實名認證','身份認證'];
			break;
			case 'vi':
				$authenticationdata	=['Xác thực điện thoại','Xác thực chat','Xác thực tên thật','Xác thực danh tính'];
			break;
			case 'id':
				$authenticationdata	=['Otentikasi ponsel','Otentikasi Wechat','Autentikasi nama asli','autentikasi identitas'];
			break;
			case 'es':
				$authenticationdata	=['Autenticación de teléfonos','Autenticación de micro carta','Homologación real','Identificación:'];
			break;
			case 'ja':
				$authenticationdata	=['携帯電話の認証','WeChat認証','実名認証','認証'];
			break;
			case 'yd':
				$authenticationdata	=['मोबाइल फोन प्रमाणीकरण', 'wechat प्रमाणीकरण', 'वास्तविक नाम प्रमाणीकरण', 'पहिचान प्रमाणीकरण'];
			break;
		}

		$data['info']['authenticationList']	=	$authenticationdata;

		//获取可提现银行列表
		$payBanks = model('Bank')->where(array(['q_state','=',1],['pay_type','=',4]))->group('bank_name')->select();

		foreach($payBanks as $key =>$value){
			$BanksList[$key]['bank_id'] = $value['id'];
			$BanksList[$key]['bank'] = $value['bank_name'];
			$BanksList[$key]['types'] =  $value['pay_type'];
		}
		$data['info']['BanksList'] = $BanksList;

		/**
		 * 获取幻灯片
		 */
		$slideLikst = model('Slide')->where(array(['status','=',1],['lang','=',$lang]))->select()->toArray();
		$data['info']['bannerList'] = [];
		foreach ($slideLikst as $key => $value) {
			$data['info']['bannerList'][$key] = $data['info']['setting']['q_server_name'].$value['img_path'];
		}
		

		
		
		$data['info']['link']		=	['http://39.96.23.242','http://39.96.23.242','http://39.96.23.242'];

		return json($data);
	}

}
