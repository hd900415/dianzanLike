<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\api\model;

use think\Model;

use think\Cache;

class TaskModel extends Model
{
	protected $table = 'ly_task';

	/**
		发布新任务
	**/
	public function publishTask(){

		$param		= input('post.');
		$userArr	= explode(',',auth_code($param['token'],'DECODE'));
		$uid		= $userArr[0];
		$username	= $userArr[1];

		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型

		$task_class	= (input('post.task_class')) ? input('post.task_class') : '';	// 任务分类
			
		
		$ftime = cache('C_ftime_'.$uid) ? cache('C_ftime_'.$uid) : time()-2;
		//10秒
		if(time()-$ftime < 2){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败'];
			}elseif($lang=='en'){
				return ['code' => 0, 'code_dec' => 'Fail'];
			}elseif($lang=='id'){
				return ['code' => 0, 'code_dec' => 'gagal'];
			}elseif($lang=='ft'){
				return ['code' => 0, 'code_dec' => '失敗'];
			}elseif($lang=='yd'){
				return ['code' => 0, 'code_dec' => 'असफल'];
			}elseif($lang=='vi'){
				return ['code' => 0, 'code_dec' => 'hỏng'];
			}elseif($lang=='es'){
				return ['code' => 0, 'code_dec' => 'Fracaso'];
			}elseif($lang=='ja'){
				return ['code' => 0, 'code_dec' => '失敗'];
			}elseif($lang=='th'){
				return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
			}
		}
		cache('C_ftime_'.$uid,time()+2);


		//判断任务分类是否可以用
		$is_task_class =	model('TaskClass')->where(array(['id','=',$task_class],['state','=',1],['is_f','=',1]))->count();
		if(!$is_task_class){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '任务分类错误';
			elseif($lang=='en') $data['code_dec']				= 'Task classification error !';
			elseif($lang=='id') $data['code_dec']				= 'Galat klasifikasi tugas !';
			elseif($lang=='ft') $data['code_dec']				= '任務分類錯誤 !';
			elseif($lang=='yd') $data['code_dec']				= 'कार्य वर्गीकरण त्रुटि';
			elseif($lang=='vi') $data['code_dec']				= 'Lỗi phân loại nhiệm !';
			elseif($lang=='es') $data['code_dec']				= 'Error de clasificación !';
			elseif($lang=='ja') $data['code_dec']				= 'タスク分類エラー !';
			elseif($lang=='th') $data['code_dec']				= 'ข้อผิดพลาดการจัดหมวดหมู่งาน';
			return $data;
		}

		$title	=	(input('post.title')) ? input('post.title') : '';	// 任务标题
		if(!$title){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '任务标题不能为空';
			elseif($lang=='en') $data['code_dec']				= 'Task Title cannot be empty !';
			elseif($lang=='id') $data['code_dec']				= 'Tajuk Tugas tidak dapat kosong';
			elseif($lang=='ft') $data['code_dec']				= '任務標題不能為空 !';
			elseif($lang=='yd') $data['code_dec']				= 'कार्य शीर्षक खाली नहीं हो सकता';
			elseif($lang=='vi') $data['code_dec']				= 'Bí danh nhiệm không thể rỗng';
			elseif($lang=='es') $data['code_dec']				= 'El título de la tarea no está vacío';
			elseif($lang=='ja') $data['code_dec']				= 'タスクのタイトルは空にできません。';
			elseif($lang=='th') $data['code_dec']				= 'ชื่องานไม่สามารถว่างเปล่า';
			return $data;
		}

		$content	=	(input('post.content')) ? input('post.content') : '';	// 任务简介

		$reward_price	=	(input('post.reward_price')) ? input('post.reward_price') : 0;	// 任务单价

		if($reward_price < 0){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '任务单价错误';
			elseif($lang=='en') $data['code_dec']				= 'Task unit price error !';
			elseif($lang=='id') $data['code_dec']				= 'Galat harga unit tugas';
			elseif($lang=='ft') $data['code_dec']				= '任務單價錯誤';
			elseif($lang=='yd') $data['code_dec']				= 'कार्य यूनिट मूल्य त्रुटि';
			elseif($lang=='vi') $data['code_dec']				= 'Lỗi trúng giá đơn nhiệm';
			elseif($lang=='es') $data['code_dec']				= 'Error de cálculo';
			elseif($lang=='ja') $data['code_dec']				= 'タスク単価が間違っています';
			elseif($lang=='th') $data['code_dec']				= 'ข้อผิดพลาดราคาต่อหน่วยงาน';
			return $data;
		}

		$total_number	=	(input('post.total_number')) ? input('post.total_number') : 0;	// 领取数量

		if($total_number < 0){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '领取数量错误';
			elseif($lang=='en') $data['code_dec']				= 'Received quantity error !';
			elseif($lang=='id') $data['code_dec']				= 'Galat kuantitas klaim';
			elseif($lang=='ft') $data['code_dec']				= '領取數量錯誤';
			elseif($lang=='yd') $data['code_dec']				= 'क्लाइम मात्रा त्रुटि';
			elseif($lang=='vi') $data['code_dec']				= 'Lỗi về đòi hỏi';
			elseif($lang=='es') $data['code_dec']				= 'Error de cálculo';
			elseif($lang=='ja') $data['code_dec']				= '受取数量が間違っています';
			elseif($lang=='th') $data['code_dec']				= 'รับหมายเลขผิด';
			return $data;
		}

		$person_time	=	(input('post.person_time')) ? input('post.person_time') : 0;	// 领取次数 次/人

		if($person_time < 0){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '领取次数 次/人 ';
			elseif($lang=='en') $data['code_dec']				= 'Times of collection Times / person error !';
			elseif($lang=='id') $data['code_dec']				= 'Waktu klaim / orang';
			elseif($lang=='ft') $data['code_dec']				= '領取次數次/人';
			elseif($lang=='yd') $data['code_dec']				= 'श्रेणी समय / व्यक्ति';
			elseif($lang=='vi') $data['code_dec']				= 'Vòng phát kiện';
			elseif($lang=='es') $data['code_dec']				= 'Número de veces / persona';
			elseif($lang=='ja') $data['code_dec']				= '受取回数/人';
			elseif($lang=='th') $data['code_dec']				= 'จำนวนครั้งที่ได้รับ';
			return $data;
		}

		$total_price	=	(input('post.total_price')) ? input('post.total_price') : 0;	// 任务总价

		if($total_price < 0){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '任务总价错误';
			elseif($lang=='en') $data['code_dec']				= 'Total task price error !';
			elseif($lang=='id') $data['code_dec']				= 'Total kesalahan harga tugas';
			elseif($lang=='ft') $data['code_dec']				= '任務總價錯誤';
			elseif($lang=='yd') $data['code_dec']				= 'कुल मूल्य त्रुटि';
			elseif($lang=='vi') $data['code_dec']				= 'Lỗi hoàn toán';
			elseif($lang=='es') $data['code_dec']				= 'Error de cálculo';
			elseif($lang=='ja') $data['code_dec']				= 'タスクの合計値が間違っています';
			elseif($lang=='th') $data['code_dec']				= 'ข้อผิดพลาดรวมงาน';
			return $data;
		}

		$task_level	=	(input('post.task_level')) ? input('post.task_level') : 0;	// 任务级别

		if($task_level < 0){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '任务级别错误';
			elseif($lang=='en') $data['code_dec']				= 'Task level error !';
			elseif($lang=='id') $data['code_dec']				= 'Galat tingkat tugas';
			elseif($lang=='ft') $data['code_dec']				= '任務級別錯誤';
			elseif($lang=='yd') $data['code_dec']				= 'कार्य स्तर त्रुटि';
			elseif($lang=='vi') $data['code_dec']				= 'Lỗi cấp nhiệm vụ';
			elseif($lang=='es') $data['code_dec']				= 'Error de nivel';
			elseif($lang=='ja') $data['code_dec']				= 'タスクレベルエラー';
			elseif($lang=='th') $data['code_dec']				= 'ข้อผิดพลาดระดับงาน';
			return $data;
		}

		$user_level	=	model('Users')->join('ly_user_grade','ly_users.vip_level=ly_user_grade.grade')->where(array('ly_users.id'=>$uid))->value('ly_user_grade.grade');

		if(!$user_level){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '级别错误';
			elseif($lang=='en') $data['code_dec']				= 'VIP level error !';
			elseif($lang=='id') $data['code_dec']				= 'Galat tahap';
			elseif($lang=='ft') $data['code_dec']				= '級別錯誤';
			elseif($lang=='yd') $data['code_dec']				= 'स्तर त्रुटि';
			elseif($lang=='vi') $data['code_dec']				= 'Lỗi cấp';
			elseif($lang=='es') $data['code_dec']				= 'Error de nivel';
			elseif($lang=='ja') $data['code_dec']				= 'レベルエラー';
			elseif($lang=='th') $data['code_dec']				= 'ระดับข้อผิดพลาด';
			return $data;
		}

		//抽水
		$pump	=	model('UserGrade')->where(array(['grade','=',$user_level],['state','=',1]))->value('pump');

		if(!$pump){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '任务错误';
			elseif($lang=='en') $data['code_dec']				= 'Task error !';
			elseif($lang=='id') $data['code_dec']				= 'Galat tugas';
			elseif($lang=='ft') $data['code_dec']				= '任務錯誤';
			elseif($lang=='yd') $data['code_dec']				= 'कार्य त्रुटि';
			elseif($lang=='vi') $data['code_dec']				= 'Lỗi nhiệm vụ';
			elseif($lang=='es') $data['code_dec']				= 'Error de misión';
			elseif($lang=='ja') $data['code_dec']				= 'タスクエラー';
			elseif($lang=='th') $data['code_dec']				= 'ข้อผิดพลาดในงาน';
			return $data;
		}

		//任务总额
		$task_total			=	$total_number*$reward_price;

		//抽水
		$task_pump			=	$task_total*($pump/100);

		//总金额
		$task_total_price	=	$task_total + $task_pump;

		//判断提交的总价跟平台总价

		if($total_price	!= $task_total_price){

			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '任务总价错误'.$task_total_price.'='.$total_price;
			elseif($lang=='en') $data['code_dec']				= 'Total task price error !';
			elseif($lang=='id') $data['code_dec']				= 'Total kesalahan harga tugas';
			elseif($lang=='ft') $data['code_dec']				= '任務總價錯誤';
			elseif($lang=='yd') $data['code_dec']				= 'कुल मूल्य त्रुटि';
			elseif($lang=='vi') $data['code_dec']				= 'Lỗi hoàn toán';
			elseif($lang=='es') $data['code_dec']				= 'Error de cálculo';
			elseif($lang=='ja') $data['code_dec']				= 'タスクの合計値が間違っています';
			elseif($lang=='th') $data['code_dec']				= 'ข้อผิดพลาดรวมงาน';
			return $data;

		}

		$link_info	=	(input('post.link_info')) ? input('post.link_info') : '';	// 链接信息
		if(!$link_info){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '链接信息错误';
			elseif($lang=='en') $data['code_dec']				= 'Link information error !';
			elseif($lang=='id') $data['code_dec']				= 'Galat informasi hubungan';
			elseif($lang=='ft') $data['code_dec']				= '連結資訊錯誤';
			elseif($lang=='yd') $data['code_dec']				= 'लिंक जानकारी त्रुटि';
			elseif($lang=='vi') $data['code_dec']				= 'Lỗi thông tin liên kết';
			elseif($lang=='es') $data['code_dec']				= 'Error de enlace';
			elseif($lang=='ja') $data['code_dec']				= 'リンク情報エラー';
			elseif($lang=='th') $data['code_dec']				= 'ข้อผิดพลาดในการเชื่อมโยงข้อมูล';
			return $data;
		}

		$end_time	= (input('post.end_time')) ? input('post.end_time') : 0;	// 截止日期

		$start_time = strtotime(date("Y-m-d",time()));
		$end_time	= strtotime($end_time);
		if($end_time < $start_time){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '截止日错误';
			elseif($lang=='en') $data['code_dec']				= 'Closing date error !';
			elseif($lang=='id') $data['code_dec']				= 'Galat tanggal';
			elseif($lang=='ft') $data['code_dec']				= '截止日錯誤';
			elseif($lang=='yd') $data['code_dec']				= 'देर तिथि त्रुटि';
			elseif($lang=='vi') $data['code_dec']				= 'Lỗi hẹn giờ';
			elseif($lang=='es') $data['code_dec']				= 'Fecha límite';
			elseif($lang=='ja') $data['code_dec']				= '締め切りエラー';
			elseif($lang=='th') $data['code_dec']				= 'ข้อผิดพลาดวันหมดอายุ';
			return $data;
		}

		$finish_condition	= (input('post.finish_condition')) ? input('post.finish_condition') : '';	// 完成条件
		if($finish_condition){
			$finish_condition	=	json_encode(array_keys($finish_condition));
		}

		$examine_demo		=	(input('post.examine_demo')) ? input('post.examine_demo') : '';	// 审核样例

		if(is_array($examine_demo)){
			foreach($examine_demo as $key2 => $value2){
				if(!file_exists('/www/wwwroot/1.wezhenxin.xyz/public'.$value2)){//判断服务器是有该文件
					if($lang=='cn'){
						return ['code' => 0, 'code_dec' => '失败'];
					}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
				}
			}
			$examine_demo		=	json_encode($examine_demo);
		}

		$task_step		   		=	(input('post.task_step')) ? input('post.task_step') : '';	// 审核样例

		if(is_array($task_step)){
			foreach($task_step as $key3 => $value3){
				if(!file_exists('/www/wwwroot/1.wezhenxin.xyz/public'.$value2)){//判断服务器是有该文件
					if($lang=='cn'){
						return ['code' => 0, 'code_dec' => '失败'];
					}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
				}
			}
			$task_step			=	json_encode($task_step);
		}

		$task_type		=	(input('post.task_type')) ? input('post.task_type') : 1;	// 任务类型 1 供应信息 2需求信息

		// 检测用户的余额
		$userBalance	= model('UserTotal')->where('uid', $uid)->value('balance');	// 获取用户的余额
		if($task_total_price > $userBalance){
			if($lang=='cn'){
				return ['code' => 2, 'code_dec' => '用户余额不足'];
			}elseif($lang=='en'){
				return ['code' => 2, 'code_dec' => 'Insufficient user balance!'];
			}elseif($lang=='id'){
				return ['code' => 2, 'code_dec' => 'Tidak cukup keseimbangan pengguna'];
			}elseif($lang=='ft'){
				return ['code' => 2, 'code_dec' => '用戶餘額不足'];
			}elseif($lang=='yd'){
				return ['code' => 2, 'code_dec' => 'अपर्याप्त प्रयोक्ता बैलेंस'];
			}elseif($lang=='vi'){
				return ['code' => 2, 'code_dec' => 'Lượng người dùng kém'];
			}elseif($lang=='es'){
				return ['code' => 2, 'code_dec' => 'Saldo de usuario insuficiente'];
			}elseif($lang=='ja'){
				return ['code' => 2, 'code_dec' => 'ユーザー残高が足りない'];
			}elseif($lang=='th'){
				return ['code' => 2, 'code_dec' => 'ยอดผู้ใช้ไม่เพียงพอ'];
			}
		}
		$id		=	(input('post.id')) ? input('post.id') : 0;	// 任务ID
		
		$requirement	=	(input('post.requirement')) ? input('post.requirement') : '';	// 任务总价

		if($id){
			//审核中的任务才能编辑
			$count = $this->where(array(['id','=',$id],['status','=',1]))->count();

			if(!$count){
				if($lang=='cn'){
					return ['code' => 0, 'code_dec' => '失败'];
				}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
			}

			$updateArray = array(
				'task_class'		=>	$task_class,
				'title'				=>	$title,
				'content'			=>	$content,
				'reward_price'		=>	$reward_price,
				'total_number'		=>	$total_number,
				'person_time'		=>	$person_time,
				'total_price'		=>	$task_total,
				'lang'				=>	$lang,
				'task_type'			=>	$task_type,
				'link_info'			=>	$link_info,
				'task_level'		=>	$task_level,
				'end_time'			=>	$end_time,
				'finish_condition'	=>	$finish_condition,
				'examine_demo'		=>	$examine_demo,
				'task_step'			=>	$task_step,
				'task_pump'			=>	$task_pump,
				'pump'				=>	$pump,
				'surplus_number'	=>	$total_number,
				'requirement'		=>	$requirement,
			);

			$res = $this->where('id', $id)->update($updateArray);
			if(!$res){
				if($lang=='cn'){
					return ['code' => 0, 'code_dec' => '失败'];

				}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
			}

			if($lang=='cn'){
				return ['code' => 1, 'code_dec' => '成功'];
			}elseif($lang=='en'){
						return ['code' => 1, 'code_dec' => 'Success'];
					}elseif($lang=='id'){
						return ['code' => 1, 'code_dec' => 'sukses'];
					}elseif($lang=='ft'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='yd'){
						return ['code' => 1, 'code_dec' => 'सफलता'];
					}elseif($lang=='vi'){
						return ['code' => 1, 'code_dec' => 'thành công'];
					}elseif($lang=='es'){
						return ['code' => 1, 'code_dec' => 'éxito'];
					}elseif($lang=='ja'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='th'){
						return ['code' => 1, 'code_dec' => 'ประสบความสำเร็จ'];
					}

		}
		// 流水 任务金额
		$order_number = 'B'.trading_number();
		$trade_number = 'L'.trading_number();

		$task_data = array(
			'uid'				=>	$uid,
			'username'			=>	$username,
			'task_class'		=>	$task_class,
			'title'				=>	$title,
			'content'			=>	$content,
			'reward_price'		=>	$reward_price,
			'total_number'		=>	$total_number,
			'person_time'		=>	$person_time,
			'total_price'		=>	$task_total,
			'lang'				=>	$lang,
			'task_type'			=>	$task_type,
			'link_info'			=>	$link_info,
			'task_level'		=>	$task_level,
			'end_time'			=>	$end_time,
			'finish_condition'	=>	$finish_condition,
			'examine_demo'		=>	$examine_demo,
			'task_step'			=>	$task_step,
			'add_time'			=>	time(),
			'task_pump'			=>	$task_pump,
			'pump'				=>	$pump,
			'status'			=>	1,
			'surplus_number'	=>	$total_number,
			'order_number'		=>	$order_number,
			'trade_number'		=>	$trade_number,
			'requirement'		=>	$requirement,
		);

		$new_id	= $this->insertGetId($task_data);

		if(!$new_id){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败'];
			}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
		}
				// 扣减用户汇总表的用户余额
		$isDecBalance	= model('UserTotal')->where('uid', $uid)->setDec('balance', $task_total);
		if(!$isDecBalance){

			$this->where('id', $new_id)->delete();

			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败'];
			}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
		}


		$financial_data_f['uid'] 					= $uid;
		$financial_data_f['username'] 				= $username;
		$financial_data_f['order_number'] 			= $order_number;
		$financial_data_f['trade_number'] 			= $trade_number;
		$financial_data_f['trade_type'] 			= 3;
		$financial_data_f['trade_before_balance']	= $userBalance;
		$financial_data_f['trade_amount'] 			= $task_total;
		$financial_data_f['account_balance'] 		= $userBalance - $task_total;
		$financial_data_f['remarks'] 				= '发布任务';
		$financial_data_f['types'] 					= 1;	// 用户1，商户2

		model('common/TradeDetails')->tradeDetails($financial_data_f);

		if($task_pump >0 ){

			$userBalance_p	= model('UserTotal')->where('uid', $uid)->value('balance');	// 获取用户的余额


			model('UserTotal')->where('uid', $uid)->setDec('balance', $task_pump);
			// 流水

			$financial_data_p['uid'] 					= $uid;
			$financial_data_p['username'] 				= $username;
			$financial_data_p['order_number'] 			= $order_number;
			$financial_data_p['trade_number'] 			= 'L'.trading_number();
			$financial_data_p['trade_type'] 			= 4;
			$financial_data_p['trade_before_balance']	= $userBalance_p;
			$financial_data_p['trade_amount'] 			= $task_pump;
			$financial_data_p['account_balance'] 		= $userBalance_p - $task_pump;
			$financial_data_p['remarks'] 				= '平台抽水';
			$financial_data_p['types'] 					= 1;	// 用户1，商户2

			model('common/TradeDetails')->tradeDetails($financial_data_p);

			if($lang=='cn'){
				return ['code' => 1, 'code_dec' => '成功'];
			}elseif($lang=='en'){
						return ['code' => 1, 'code_dec' => 'Success'];
					}elseif($lang=='id'){
						return ['code' => 1, 'code_dec' => 'sukses'];
					}elseif($lang=='ft'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='yd'){
						return ['code' => 1, 'code_dec' => 'सफलता'];
					}elseif($lang=='vi'){
						return ['code' => 1, 'code_dec' => 'thành công'];
					}elseif($lang=='es'){
						return ['code' => 1, 'code_dec' => 'éxito'];
					}elseif($lang=='ja'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='th'){
						return ['code' => 1, 'code_dec' => 'ประสบความสำเร็จ'];
					}
		}
	}


	/**
		获取任务列表
	**/
	public function getTaskList(){

		$param		= input('post.');
		
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型

		// 分页
		$is_u			= (input('post.is_u')) ? input('post.is_u') : 0;	// 是否自己发布的

		$task_class		= (input('post.group_id')) ? input('post.group_id') : 0;	// 分类
			
		$task_level		=	(input('post.task_level')) ? input('post.task_level') : 0;	// 分类

		$where = array();
		
		$is_l		=	0;
		$uid		=   0;
		$is_login	= 	0;
		$t = time();
		$start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
		$end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));

		
		if($is_u and $param['token']){
			//自己发的

			$userArr	= explode(',',auth_code($param['token'],'DECODE'));

			$uid		= $userArr[0];

			$where[] = array(['uid','=',$uid]);


		}else{

			if($param['token']){

				$userArr	= explode(',',auth_code($param['token'],'DECODE'));

				$uid		= $userArr[0];

				$where[] 	= array(['uid','<>',$uid]);//不是自己发的

				$vip_level	=	model('Users')->where('id', $uid)->value('vip_level');

				$where[]	= array(['task_level','<=',$vip_level]);//剩余任务数

				//我能接的任务次数
				$userinfo	=	model('Users')->join('ly_user_grade','ly_users.vip_level=ly_user_grade.grade')->where(array('ly_users.id'=>$uid))->find();
				$my_day_number		=	$userinfo['number'];
				
				if($userinfo['credit']<=30){
					$my_day_number	=	$userinfo['number']/2;
				}
		
				$is_login	= 	1;
				//我今天领取任务次数
				//$day_number			=	model('UserTask')->where(array(['uid','=',$uid],['add_time','>=',$start],['add_time','<=',$end]))->count();//ken注释
				$my_task_id			=	model('UserTask')->field('task_id')->where(array(['uid','=',$uid]))->select()->toArray();
				if ($my_task_id) {
					foreach ($my_task_id as $kid => $vid) {
						$my_task_id_array[] = $vid['task_id'];
					}
				}else{
					$my_task_id_array = array();
				}
				//var_dump($my_task_id_array);
				/*
				if($my_day_number==$day_number){
					//$is_l	=	1;
				}
				*/
				
				if($userinfo['credit']==0){
					$is_l	=	0;	
				}

			}

			$where[]	= array(['surplus_number','>',0]);//剩余任务数

			$where[] 	= array(['status','=',3]);//未完成的

			$where[] 	= array(['end_time','>=',strtotime(date("Y-m-d",time()))]);//未完成的

		}
		if($task_class){
			$where[] 	= array(['task_class','=',$task_class]);//类型
		}
		
		if($task_level){
			$where[] 	= array(['task_level','=',$task_level]);//等级
		}
		if($is_login == 1){
			$count	= $this->where($where)->where(array(['ly_task.id','not in',$my_task_id_array]))->count();	// 记录数量
		}else{
			$count	= $this->where($where)->count();	// 记录数量
		}
		if(!$count){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
			return $data;
		}

		//每页记录数
		$pageSize	= (isset($param['page_size']) and $param['page_size']) ? $param['page_size'] : 10;
		//当前页
		$pageNo		= (isset($param['page_no']) and $param['page_no']) ? $param['page_no'] : 1;
		//总页数
		$pageTotal	= ceil($count / $pageSize); //当前页数大于最后页数，取最后
		//偏移量
		$limitOffset	= ($pageNo - 1) * $pageSize;

		$dataAll	= [];

		if($is_login == 1){
			$dataAll	= $this->where($where)->where(array(['ly_task.id','not in',$my_task_id_array]))->order('add_time desc')->limit($limitOffset, $pageSize)->select()->toArray();	//
		}else{
			$dataAll	= $this->where($where)->order('add_time desc')->limit($limitOffset, $pageSize)->select()->toArray();	//
		}
		//获取成功
		$data	= [];
		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $count;		// 记录数量
		$data['data_total_page'] 	= $pageTotal;	// 总页数
		$data['data_current_page'] 	= $pageNo;		// 当前页

		foreach ($dataAll as $key => $value) {

			$data['info'][$key]['task_id']				= 	$value['id'];
			$data['info'][$key]['title']				= 	$value['title'];
			if($value['username']){
				$username								= 	$value['username'];
			}else{
				$username								=	'1'.mt_rand(50,99).'1234'.mt_rand(1000,9999);//model('Setting')->where(array(['id','=',1]))->value('task_phone');
			}
			$data['info'][$key]['username']				=	substr(trim($username),0,3).'****'.substr(trim($username),-4);

			$TaskClass									= 	model('TaskClass')->where(array(['id','=',$value['task_class']]))->find();
			
			$data['info'][$key]['group_name']			=	$TaskClass['group_name'];
			$data['info'][$key]['is_fx']				=	$TaskClass['is_fx'];
			$data['info'][$key]['icon']					=	'http://'.$_SERVER['HTTP_HOST'].$TaskClass['icon'];

			$UserGrade									=	model('UserGrade')->where(array(['grade','=',$value['task_level']]))->find();
			/*   ken 注释
			if($uid){
				$uall										=	model('UserTask')->where(array(['task_id','=',$value['id']],['uid','=',$uid]))->count();
				//$uall										=	model('UserTask')->where(array(['task_id','=',$value['id']],['uid','=',$uid],['add_time','>=',$start],['add_time','<=',$end]))->count();
				if($is_l){//今天是否领完
					$data['info'][$key]['is_l']					=	1;//今天已经领完任务
				}else{
					if($uall){
						if($uall==$value['person_time']){
							$data['info'][$key]['is_l']				=	3;//已领玩
						}else{
							$ucount 								=	model('UserTask')->where(array(['task_id','=',$value['id']],['status','<=',2],['uid','=',$uid]))->count();
		
							if($ucount){
								$data['info'][$key]['is_l']			=	2;//已经领取 还未完成 还有进行中的
							}else{
								$data['info'][$key]['is_l']			=	0;// 完成的 失败的 恶意的 算未领取 还可以领取
							}
						}
					}else{
						$data['info'][$key]['is_l']					=	0;//未领取 还可以领取 
					}
	
				}
			}else
			*/
			{
				$data['info'][$key]['is_l']					=	0;//未领取 还可以领取 
			}
			
			

			if($lang=='en'){
				$data['info'][$key]['vip_dec']			=	$UserGrade['en_name'];
			}else{
				$data['info'][$key]['vip_dec']			=	$UserGrade['name'];
			}
			$data['info'][$key]['surplus_number']		= 	$value['surplus_number'];

			$data['info'][$key]['group_info']			= 	$TaskClass['group_info'];

			$data['info'][$key]['reward_price']			= 	$value['reward_price'];

			$data['info'][$key]['link_info']			= 	$value['link_info'];

			$data['info'][$key]['status']				=	$value['status'];

			$data['info'][$key]['total_number']			=	$value['total_number'];

			$data['info'][$key]['end_time']				= 	($value['end_time']) ? date('Y-m-d', $value['end_time']) : '';

			if($is_u){
				if($lang=='en'){
					$data['info'][$key]['status_dec']		=	config('custom.entaskStatus')[$value['status']];
				}else{
					$data['info'][$key]['status_dec']		=	config('custom.taskStatus')[$value['status']];
				}

			}else{
				if($lang=='en'){
					$data['info'][$key]['status_dec']		=	'PAY';
				}elseif($lang=='cn'){
					$data['info'][$key]['status_dec']		=	'已付款';
				}elseif($lang=='id'){
					$data['info'][$key]['status_dec']		=	'Dibayar';
				}elseif($lang=='ft'){
					$data['info'][$key]['status_dec']		=	'已付款';
				}elseif($lang=='yd'){
					$data['info'][$key]['status_dec']		=	'पैदा';
				}elseif($lang=='vi'){
					$data['info'][$key]['status_dec']		=	'Trả';
				}elseif($lang=='es'){
					$data['info'][$key]['status_dec']		=	'Pagos efectuados';
				}elseif($lang=='ja'){
					$data['info'][$key]['status_dec']		=	'支払い済み';
				}elseif($lang=='th'){
					$data['info'][$key]['status_dec']		=	'จ่ายเงิน';
				}
			}
			
			/*if(in_array($data['info'][$key]['is_l'],array(1,2,3))){
				unset($data['info'][$key]);
			}*/

		}

		$data['code']	= 1;
		return $data;
	}


	//获取任务信息
	public function getTaskinfo(){

		$param		= input('post.');
		$uid		= 0;
		
		if(isset($param['token']) and $param['token']){
			$userArr	= explode(',',auth_code($param['token'],'DECODE'));
			$uid		= $userArr[0];
		}
		
		$id			= (input('post.id')) ? input('post.id') : 0;	// 任务ID;
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		if(!$id){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
			return $data;
		}

		$info = $this->where(array(['id','=',$id]))->find();

		if(!$info){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
			return $data;
		}

		if($info['username']){
			$username								= 	$info['username'];
		}else{
			$username								=	 '1'.mt_rand(50,99).'1234'.mt_rand(1000,9999);//model('Setting')->where(array(['id','=',1]))->value('task_phone');
		}
		$username			=	substr(trim($username),0,3).'****'.substr(trim($username),-4);
		
		$y_surplus_number	=	model('UserTask')->where(array(['task_id','=',$id],['status','=',3]))->count();
		$is_l		=	0;
		
		if($uid){//已经登录
					
			//我能接的任务次数
			$userinfo	=	model('Users')->join('ly_user_grade','ly_users.vip_level=ly_user_grade.grade')->where(array('ly_users.id'=>$uid))->find();
			
			
			$t 					= time();
			$start 				= mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
			$end 				= mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
			$my_day_number		=	$userinfo['number'];
			
			if($userinfo['credit']<=30){
				$my_day_number		=	$userinfo['number']/2;
			}
	
			//我今天领取任务次数
			$day_number			=	model('UserDaily')->where(array(['uid','=',$uid],['date','=',$start]))->value('l_t_o_n');
			
			if($my_day_number  == $day_number){
				//$is_l	=	1;
			}
			
			if($userinfo['credit']==0){
				$is_l	=	0;	
			}
			
			if($is_l){
				$is_l						=	1;
			}else{
				$uall						=	model('UserTask')->where(array(['task_id','=',$id],['uid','=',$uid]))->count();
				//$uall						=	model('UserTask')->where(array(['task_id','=',$id],['uid','=',$uid],['add_time','>=',$start],['add_time','<=',$end]))->count();
				if($uall){
					
					if($uall==$info['person_time']){
						$is_l				=	3;//已领玩
					}else{
						$ucount 			=	model('UserTask')->where(array(['task_id','=',$id],['status','<=',2],['uid','=',$uid]))->count();
		
						if($ucount){
							$is_l			=	2;//已经领取 还未完成 还有进行中的
						}else{
							$is_l			=	0;//完成的 失败的 恶意的 算未领取 还可以领取
						}
					}
				}else{
					$is_l					=	0;//未领取
				}
			}
		}
		
		if($info['uid']){
			$f_header 	= 	model('Users')->where(array(['id','=',$info['uid']]))->value('header');//发布人头像
		}else{
			$f_header	=	'head_'.mt_rand(1,10).'.png';	
		}


		$data['info']	=	array(
			'id'				=>	$info['id'],
			'f_uid'				=>	$info['uid'],//发布人id
			'f_username'		=>	$username,//发布人name
			'title'				=>	$info['title'],
			'f_header'			=>	$f_header,
			'content'			=>	htmlspecialchars_decode($info['content']),
			'surplus_number'	=>	$info['surplus_number'],//剩余
			'y_surplus_number'	=>	2138 + $y_surplus_number,//已经完成的
			'finish_condition' 	=>	json_decode($info['finish_condition'], true),
			'link_info'			=>	htmlspecialchars_decode($info['link_info']),
			'reward_price'		=>	$info['reward_price'],
			'examine_demo' 		=>	json_decode($info['examine_demo'], true),
			'task_step' 		=>	json_decode($info['task_step'], true),
			'task_type' 		=>	$info['task_type'],
			'task_level' 		=>	$info['task_level'],
			'task_class' 		=>	$info['task_class'],
			'reward_price' 		=>	$info['reward_price'],
			'total_price' 		=>	$info['total_price'],
			'total_number' 		=>	$info['total_number'],
			'receive_number' 	=>	$info['receive_number'],
			'end_time' 			=>	($info['end_time']) ? date('Y-m-d', $info['end_time']) : '',
			'receive_number' 	=>	$info['receive_number'],
			'lang' 				=>	$info['lang'],
			'person_time' 		=>	$info['person_time'],
			'is_l'				=>	$is_l,
			'is_fx'				=>	model('TaskClass')->where('id',$info['task_class'])->value('is_fx'),
		);

		$data['code']	=	1;

		return $data;
	}

	//撤销任务
	public function revokeTask(){

		$param			= input('post.');
		$userArr		= explode(',',auth_code($param['token'],'DECODE'));
		$uid			= $userArr[0];
		$username		= $userArr[1];
		$id				= (input('post.id')) ? input('post.id') : 0;	// 任务ID;

		$lang			= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型

		if(!$id){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败'];
			}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
		}

		$info = $this->where(array(['id','=',$id],['uid','=',$uid],['status','=',1]))->find();

		if(!$info){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败'];
			}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
		}

		$is_up	=	$this->where(array(['id','=',$id],['uid','=',$uid],['status','=',1]))->update(array('status'=>5));//撤销

		if(!$is_up){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败'];
			}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
		}

		$userBalance_p		= 	model('UserTotal')->where('uid', $uid)->value('balance');	// 获取用户的余额

		$total_price		=	$info['total_price']	+	$info['task_pump'];

		$is_up_to = model('UserTotal')->where('uid', $uid)->Inc('balance', $total_price);
		if(!$is_up_to){

			$this->where(array(['id','=',$id],['uid','=',$uid],['status','=',5]))->update(array('status'=>1));//撤销

			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败'];
			}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
		}

		// 流水

		$financial_data_p['uid'] 					= $uid;
		$financial_data_p['username'] 				= $username;
		$financial_data_p['order_number'] 			= $info['order_number'];
		$financial_data_p['trade_number'] 			= 'L'.trading_number();;
		$financial_data_p['trade_type'] 			= 10;
		$financial_data_p['trade_before_balance']	= $userBalance_p;
		$financial_data_p['trade_amount'] 			= $total_price;
		$financial_data_p['account_balance'] 		= $userBalance_p + $total_price;
		$financial_data_p['remarks'] 				= '撤销任务';
		$financial_data_p['types'] 					= 1;	// 用户1，商户2

		model('common/TradeDetails')->tradeDetails($financial_data_p);

		if($lang=='cn'){
			return ['code' => 1, 'code_dec' => '成功'];
		}elseif($lang=='en'){
						return ['code' => 1, 'code_dec' => 'Success'];
					}elseif($lang=='id'){
						return ['code' => 1, 'code_dec' => 'sukses'];
					}elseif($lang=='ft'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='yd'){
						return ['code' => 1, 'code_dec' => 'सफलता'];
					}elseif($lang=='vi'){
						return ['code' => 1, 'code_dec' => 'thành công'];
					}elseif($lang=='es'){
						return ['code' => 1, 'code_dec' => 'éxito'];
					}elseif($lang=='ja'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='th'){
						return ['code' => 1, 'code_dec' => 'ประสบความสำเร็จ'];
					}


	}


	//领取任务
	public function receiveTask(){

		$param			= input('post.');
		$userArr		= explode(',',auth_code($param['token'],'DECODE'));
		$uid			= $userArr[0];
		$username		= $userArr[1];
		$id				= (input('post.id')) ? input('post.id') : 0;	// 任务ID;
		$lang			= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型

		if(!$id){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
			return $data;
		}
		
		
		$ltime = cache('C_ltime_'.$uid.'_'.$id) ? cache('C_ltime_'.$uid.'_'.$id) : time()-2;
		//10秒
		if(time()-$ltime < 2){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败'];
			}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
		}
		cache('C_ltime_'.$uid.'_'.$id,time()+2);


		$info = $this->where(array(['id','=',$id],['status','=',3]))->find();//进行中的订单

		if(!$info){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
			return $data;
		}
		
		//自己发的不能自己接
		if($info['uid']==$uid){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
			return $data;
		}

		//剩余任务
		if($info['surplus_number']==0){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '任务次数为 0';
			elseif($lang=='en') $data['code_dec']	= 'The number of tasks is 0!';
			elseif($lang=='id') $data['code_dec']	= 'Jumlah tugas adalah 0';
			elseif($lang=='ft') $data['code_dec']	= '任務次數為0';
			elseif($lang=='yd') $data['code_dec']	= 'कार्यों की संख्या 0 है';
			elseif($lang=='vi') $data['code_dec']	= 'Số công việc là 0';
			elseif($lang=='es') $data['code_dec']	= 'Número de misiones 0';
			elseif($lang=='ja') $data['code_dec']	= 'タスクの回数は0です';
			elseif($lang=='th') $data['code_dec']	= 'จำนวนงาน';
			return $data;
		}

		//我能接的任务次数
		$userinfo	=	model('Users')->join('ly_user_grade','ly_users.vip_level=ly_user_grade.grade')->where(array('ly_users.id'=>$uid))->find();

		$t = time();
		$start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
		$end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
		
		if($userinfo['credit']==0){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '信用为 0';
			elseif($lang=='en') $data['code_dec']	= 'Credit is 0!';
			elseif($lang=='id') $data['code_dec']	= 'Kredit adalah 0';
			elseif($lang=='ft') $data['code_dec']	= '信用為0';
			elseif($lang=='yd') $data['code_dec']	= 'क्रेडिट 0 है';
			elseif($lang=='vi') $data['code_dec']	= 'Thương lượng';
			elseif($lang=='es') $data['code_dec']	= 'Crédito a cero';
			elseif($lang=='ja') $data['code_dec']	= '信用は0です';
			elseif($lang=='th') $data['code_dec']	= 'เครดิตสำหรับ';
			return $data;
		}
		$my_day_number			=	$userinfo['number'];
		//信用<30任务减半
		if($userinfo['credit']<=30){
			$my_day_number		=	$userinfo['number']/2;
		}
		
		//我今天领取任务次数
		$day_number			=	model('UserDaily')->where(array(['uid','=',$uid],['date','=',$start]))->value('l_t_o_n');
		
		if($day_number >= $my_day_number){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '今日次数已用完';
			elseif($lang=='en') $data['code_dec']	= "Today's times have run out '";
			elseif($lang=='id') $data['code_dec']	= 'Hari ini sudah habis';
			elseif($lang=='ft') $data['code_dec']	= '今日次數已用完';
			elseif($lang=='yd') $data['code_dec']	= 'आज के समय बाहर हो गया है';
			elseif($lang=='vi') $data['code_dec']	= 'Thời buổi hôm nay hết rồi.';
			elseif($lang=='es') $data['code_dec']	= 'Hoy no hay más.';
			elseif($lang=='ja') $data['code_dec']	= '本日は回数が切れました。';
			elseif($lang=='th') $data['code_dec']	= 'วันนี้เวลาหมด';
			return $data;
		}
		
		//今天我这条任务 我领了几次
		$f_surplus_number	=	model('UserTask')->where(array(['task_id','=',$id],['uid','=',$uid],['add_time','between',[$start,$end]]))->count();
		
		if($f_surplus_number >= $info['person_time']){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '任务次数为 0';
			elseif($lang=='en') $data['code_dec']	= 'The number of tasks is 0!';
			elseif($lang=='id') $data['code_dec']	= 'Jumlah tugas adalah 0';
			elseif($lang=='ft') $data['code_dec']	= '任務次數為0';
			elseif($lang=='yd') $data['code_dec']	= 'कार्यों की संख्या 0 है';
			elseif($lang=='vi') $data['code_dec']	= 'Số công việc là 0';
			elseif($lang=='es') $data['code_dec']	= 'Número de misiones 0';
			elseif($lang=='ja') $data['code_dec']	= 'タスクの回数は0です';
			elseif($lang=='th') $data['code_dec']	= 'จำนวนงาน';
			return $data;
		}
		
		//还有在进行中的不能领取
		$ucount 								=	model('UserTask')->where(array(['task_id','=',$id],['status','<=',2],['uid','=',$uid]))->count();
		
		if($ucount){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '进行中的任务，不能在领取';
			elseif($lang=='en') $data['code_dec']	= 'The task in progress cannot be claimed !';
			elseif($lang=='id') $data['code_dec']	= 'Tugas yang sedang dilakukan tidak dapat dikumpulkan di';
			elseif($lang=='ft') $data['code_dec']	= '進行中的任務，不能在領取';
			elseif($lang=='yd') $data['code_dec']	= 'प्रगति में कार्य इकट्ठा नहीं कर सकता';
			elseif($lang=='vi') $data['code_dec']	= 'Nhiệm vụ đang diễn ra không thể thu thập được';
			elseif($lang=='es') $data['code_dec']	= 'Tarea en curso, no disponible';
			elseif($lang=='ja') $data['code_dec']	= '進行中のジョブは受領できません。';
			elseif($lang=='th') $data['code_dec']	= 'งานต่อเนื่องที่ไม่สามารถได้รับ';
			return $data;
		}


		//添加领取任务列表
		$Task_data = array(
			'task_id'	=>	$id,
			'uid'		=>	$uid,
			'username'	=>	$username,
			'status'	=>	1,
			'fuid'		=>	$info['uid'],
			'add_time'	=>	time()
		);

		$new_id	= model('UserTask')->insertGetId($Task_data);

		if(!$new_id){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败'];
			}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
		}

		//更新次数
		$this->where('id', $id)->Inc('receive_number',1)->Dec('surplus_number',1)->update();
		
		//更新每日领取任务次数
		$UserDailydata = array(
			'uid'				=>	$uid,
			'username'			=>	$username,
			'field'				=>	'l_t_o_n',//领取
			'value' 			=>	1,
		);
		model('UserDaily')->updateReportfield($UserDailydata);

		if($lang=='cn'){
			return ['code' => 1, 'code_dec' => '成功'];
		}elseif($lang=='en'){
						return ['code' => 1, 'code_dec' => 'Success'];
					}elseif($lang=='id'){
						return ['code' => 1, 'code_dec' => 'sukses'];
					}elseif($lang=='ft'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='yd'){
						return ['code' => 1, 'code_dec' => 'सफलता'];
					}elseif($lang=='vi'){
						return ['code' => 1, 'code_dec' => 'thành công'];
					}elseif($lang=='es'){
						return ['code' => 1, 'code_dec' => 'éxito'];
					}elseif($lang=='ja'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='th'){
						return ['code' => 1, 'code_dec' => 'ประสบความสำเร็จ'];
					}
	}

	//领取的任务列表
	public function taskOrderlist(){

		$param			= input('post.');
		$userArr		= explode(',',auth_code($param['token'],'DECODE'));
		$uid			= $userArr[0];
		$username		= $userArr[1];

		$task_id		= (input('post.task_id')) ? input('post.task_id') : 0;	// 任务ID下的任务领取列表 如没有就全部的任务列表;

		$lang			= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型

		$is_u			= (input('post.is_u')) ? input('post.is_u') : 2;	// 1是自己发布的;2自己领取的

		$status			= (isset($param['status']) and $param['status']) ? $param['status'] : 1;//状态。1：进行中；2：审核中；3：已完成；4：已失败;5:恶意

		$where = array();

		if($task_id){
			$where[]	=	array(['ly_user_task.task_id','=',$task_id]);
		}

		$where[]		=	array(['ly_user_task.status','=',$status]);

		switch($is_u){
			case 1://我发布的 会员领取的任务
				$where[]		=	array(['ly_user_task.fuid','=',$uid]);
			break;
			case 2://我领取的任务
				$where[]		=	array(['ly_user_task.uid','=',$uid]);
			break;
		}

		$count	= model('UserTask')->where($where)->count();	// 记录数量

		if(!$count){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
			return $data;
		}

		//每页记录数
		$pageSize	= (isset($param['page_size']) and $param['page_size']) ? $param['page_size'] : 10;
		//当前页
		$pageNo		= (isset($param['page_no']) and $param['page_no']) ? $param['page_no'] : 1;
		//总页数
		$pageTotal	= ceil($count / $pageSize); //当前页数大于最后页数，取最后
		//偏移量
		$limitOffset	= ($pageNo - 1) * $pageSize;

		$dataAll	= [];

		$dataAll = model('UserTask')->field('ly_task.*,ly_user_task.status as o_status,ly_user_task.add_time as o_add_time,ly_user_task.username as o_username,ly_user_task.examine_demo as o_examine_demo,ly_user_task.trial_time,ly_user_task.handle_time,ly_user_task.id as order_id')->join('ly_task','ly_task.id=ly_user_task.task_id')->where($where)->order('add_time desc')->limit($limitOffset, $pageSize)->select()->toArray();
		//获取成功
		$data	= [];
		$data['code'] 				= 1;
		$data['data_total_nums'] 	= $count;		// 记录数量
		$data['data_total_page'] 	= $pageTotal;	// 总页数
		$data['data_current_page'] 	= $pageNo;		// 当前页

		foreach ($dataAll as $key => $value) {

			$data['info'][$key]['task_id']				= 	$value['id'];
			$data['info'][$key]['order_id']				= 	$value['order_id'];
			$data['info'][$key]['title']				= 	$value['title'];
			//$data['info'][$key]['o_examine_demo']		= 	$value['o_examine_demo'];

			if($value['username']){
				$username								= 	$value['username'];
			}else{
				$username								=	model('Setting')->where(array(['id','=',1]))->value('task_phone');
			}

			$data['info'][$key]['username']				=	substr(trim($username),0,3).'****'.substr(trim($username),-4);

			$data['info'][$key]['o_username']			= 	substr(trim($value['o_username']),0,3).'****'.substr(trim($value['o_username']),-4);

			$TaskClass									= 	model('TaskClass')->where(array(['id','=',$value['task_class']]))->find();

			$data['info'][$key]['group_name']			=	$TaskClass['group_name'];
			
			$data['info'][$key]['is_fx']				=	$TaskClass['is_fx'];

			$data['info'][$key]['icon']					=	'http://'.$_SERVER['HTTP_HOST'].$TaskClass['icon'];

			$UserGrade									=	model('UserGrade')->where(array(['grade','=',$value['task_level']]))->find();

			if($lang=='en'){
				$data['info'][$key]['vip_dec']			=	$UserGrade['en_name'];
			}else{
				$data['info'][$key]['vip_dec']			=	$UserGrade['name'];
			}

			$data['info'][$key]['surplus_number']		= 	$value['surplus_number'];

			$data['info'][$key]['group_info']			= 	$TaskClass['group_info'];

			$data['info'][$key]['reward_price']			= 	$value['reward_price'];

			$data['info'][$key]['link_info']			= 	$value['link_info'];

			$data['info'][$key]['add_time']				= 	($value['o_add_time']) ? date('Y.m.d-H:i:s', $value['o_add_time']) : '';

			$data['info'][$key]['trial_time']			= 	($value['trial_time']) ? date('Y.m.d-H:i:s', $value['trial_time']) : '';
			
			$data['info'][$key]['handle_time']			= 	($value['handle_time']) ? date('Y.m.d-H:i:s', $value['handle_time']) : '';

			$data['info'][$key]['status']				= 	$value['o_status'];
			
			$data['info'][$key]['requirement']			= 	$value['requirement'];

			if($lang=='en'){
				$data['info'][$key]['status_dec']		=	config('custom.entaskOrderStatus')[$value['o_status']];
			}else{
				$data['info'][$key]['status_dec']		=	config('custom.cntaskOrderStatus')[$value['o_status']];
			}
		}

		$data['code']	= 1;
		return $data;
	}

	//领取的任务信息
	public function taskOrderInfo(){

		$param		= input('post.');
		$userArr	= explode(',',auth_code($param['token'],'DECODE'));
		$uid		= $userArr[0];
		$order_id	= (input('post.order_id')) ? input('post.order_id') : 0;	// 任务ID;
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		if(!$order_id){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
			return $data;
		}

		$info = model('UserTask')->field('ly_task.*,ly_user_task.status as o_status,ly_user_task.add_time as o_add_time,ly_user_task.username as o_username,ly_user_task.examine_demo as o_examine_demo,ly_user_task.trial_time,ly_user_task.id as order_id,ly_user_task.uid as o_uid,ly_user_task.username as o_username,ly_user_task.trial_remarks,ly_user_task.handle_remarks,ly_user_task.complete_time as o_complete_time,ly_user_task.handle_time')->join('ly_task','ly_task.id=ly_user_task.task_id')->where(array(['ly_user_task.id','=',$order_id]))->find();

		if(!$info){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
			return $data;
		}

		if($info['username']){
			$username		= 	$info['username'];
		}else{
			$username		=	model('Setting')->where(array(['id','=',1]))->value('task_phone');
		}

		$username			=	substr(trim($username),0,3).'****'.substr(trim($username),-4);

		$y_surplus_number	=	model('UserTask')->where(array(['task_id','=',$info['id']],['status','=',3]))->count();

		if($lang=='en'){
			$status_dec		=	config('custom.entaskOrderStatus')[$info['o_status']];
		}else{
			$status_dec		=	config('custom.cntaskOrderStatus')[$info['o_status']];
		}

		$add_time			= 	($info['o_add_time']) ? date('Y.m.d-H:i:s', $info['o_add_time']) : '';

		$trial_time			=	($info['trial_time']) ? date('Y.m.d-H:i:s', $info['trial_time']) : '';

		$handle_time		=	($info['handle_time']) ? date('Y.m.d-H:i:s', $info['handle_time']) : '';

		$o_complete_time	=	($info['o_complete_time']) ? date('Y.m.d-H:i:s', $info['o_complete_time']) : '';


		$is_j = $is_f = 0;

		if($uid==$info['id']){
			$is_f	=	1;
		}

		if($uid==$info['o_uid']){
			$is_j	=	1;
		}
		if($info['uid']){
			$f_header	=	model('Users')->where(array(['id','=',$info['uid']]))->value('header');//发布人头像
		}else{
			$f_header	= 'head_'.mt_rand(1,10).'.png';
		}
		
		if($info['o_examine_demo']){
			if(strstr($info['o_examine_demo'],'[')){ 
				$o_examine_demo   = json_decode($info['o_examine_demo'], true);
			}else{
				$o_examine_demo   = array($info['o_examine_demo']);
			}
		}else{
			$o_examine_demo		  =	array();
		}

		$data['info']	=	array(
			'id'				=>	$info['id'],//任务id
			'order_id'			=>	$info['order_id'],//领取id
			'f_uid'				=>	$info['uid'],//发布人id
			'f_username'		=>	$username,//发布人name
			'title'				=>	$info['title'],
			'f_header'			=>	$f_header,
			'content'			=>	htmlspecialchars_decode($info['content']),
			'surplus_number'	=>	$info['surplus_number'],//剩余
			'y_surplus_number'	=>	$y_surplus_number,//已经完成的
			'finish_condition' 	=>	json_decode($info['finish_condition'], true),
			'link_info'			=>	htmlspecialchars_decode($info['link_info']),
			'reward_price'		=>	$info['reward_price'],
			'examine_demo' 		=>	json_decode($info['examine_demo'], true),
			'task_step' 		=>	json_decode($info['task_step'], true),
			'o_examine_demo'	=>	$o_examine_demo,
			'o_complete_time'	=>	$info['o_complete_time'],
			'add_time'			=>	$add_time,
			'trial_time'		=>	$trial_time,
			'handle_time'		=>	$handle_time,
			'handle_remarks'	=>	$info['handle_remarks'],
			'trial_remarks'		=>	$info['trial_remarks'],
			'status'			=>	$info['status'],
			'requirement'		=>	$info['requirement'],
			'o_status'			=>	$info['o_status'],
			'o_status_dec'		=>	$status_dec,
			'j_uid'				=>	$info['o_uid'],//接单人id
			'j_username'		=>	substr(trim($info['o_username']),0,3).'****'.substr(trim($info['o_username']),-4),//接单人name
			'j_header'			=>	model('Users')->where(array(['id','=',$info['o_uid']]))->value('header'),//接单人头像
			'is_f'				=>	$is_f,
			'is_j'				=>	$is_j,
			'is_fx'				=>	model('TaskClass')->where('id',$info['task_class'])->value('is_fx'),
		);

		$data['code']	=	1;
		return $data;
	}

	//提交
	public function taskOrderSubmit(){

		$param		= input('post.');
		$userArr	= explode(',',auth_code($param['token'],'DECODE'));
		$uid		= $userArr[0];
		$order_id	= (input('post.order_id')) ? input('post.order_id') : 0;	// 任务ID;
		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型
		$status		= (input('post.status')) ? input('post.status') : 2;	// 状态

		if(!$order_id){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
			return $data;
		}
		$info = model('UserTask')->field('ly_task.status,ly_user_task.status as o_status,ly_user_task.task_id')->join('ly_task','ly_task.id=ly_user_task.task_id')->where(array(['ly_task.status','=',3],['ly_user_task.id','=',$order_id],['ly_user_task.uid','=',$uid]))->find();

		if(!$info){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
			return $data;
		}

		$trial_state_arr =  array(1,2);

		if(!in_array($info['o_status'],$trial_state_arr)){

			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败1'];
			}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}

		}

		switch($status){
			case 6:
				$up_trial_data	=	array(
					'status'	=>	6,
					'trial_time'		=>	time(),//提交时间
				);

				$is_up = model('UserTask')->where(array(['ly_user_task.id','=',$order_id],['ly_user_task.uid','=',$uid]))->update($up_trial_data);
				if(!$is_up){
					if($lang=='cn'){
						return ['code' => 0, 'code_dec' => '失败2'];
					}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
				}

				//更新次数
				$this->where('id', $info['task_id'])->setDec('receive_number',1);
				$this->where('id', $info['task_id'])->setInc('surplus_number',1);

				if($lang=='cn'){
					return ['code' => 1, 'code_dec' => '成功'];
				}elseif($lang=='en'){
						return ['code' => 1, 'code_dec' => 'Success'];
					}elseif($lang=='id'){
						return ['code' => 1, 'code_dec' => 'sukses'];
					}elseif($lang=='ft'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='yd'){
						return ['code' => 1, 'code_dec' => 'सफलता'];
					}elseif($lang=='vi'){
						return ['code' => 1, 'code_dec' => 'thành công'];
					}elseif($lang=='es'){
						return ['code' => 1, 'code_dec' => 'éxito'];
					}elseif($lang=='ja'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='th'){
						return ['code' => 1, 'code_dec' => 'ประสบความสำเร็จ'];
					}
			break;
		}

		$examine_demo		=	(input('post.examine_demo')) ? input('post.examine_demo') : '';	// 提交样例

		if(!$examine_demo){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败3'];
			}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
		}
		
		if(!is_array($examine_demo)){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败4'];
			}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
		}

		foreach($examine_demo as $key2 => $value2){
			if(!file_exists('/www/wwwroot/1.wezhenxin.xyz/public'.$value2)){//判断服务器是有该文件
				if($lang=='cn'){
					return ['code' => 0, 'code_dec' => '失败5'];
				}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
			}
		}
		
		//多图
		$examine_demo		=	json_encode($examine_demo);


		$trial_remarks		=	(input('post.trial_remarks')) ? input('post.trial_remarks') : '';	// 提交说明

		$up_trial_data = array(
			'status'			=>	2,//审核中
			'trial_time'		=>	time(),//提交时间
			'examine_demo'		=>	$examine_demo,//提交样例
			'trial_remarks'		=>	$trial_remarks,//提交说明
		);

		$is_up = model('UserTask')->where(array(['ly_user_task.id','=',$order_id],['ly_user_task.uid','=',$uid]))->update($up_trial_data);

		if(!$is_up){
			if($lang=='cn'){
				return ['code' => 0, 'code_dec' => '失败'];
			}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
		}

		if($lang=='cn'){
			return ['code' => 1, 'code_dec' => '成功'];
		}elseif($lang=='en'){
						return ['code' => 1, 'code_dec' => 'Success'];
					}elseif($lang=='id'){
						return ['code' => 1, 'code_dec' => 'sukses'];
					}elseif($lang=='ft'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='yd'){
						return ['code' => 1, 'code_dec' => 'सफलता'];
					}elseif($lang=='vi'){
						return ['code' => 1, 'code_dec' => 'thành công'];
					}elseif($lang=='es'){
						return ['code' => 1, 'code_dec' => 'éxito'];
					}elseif($lang=='ja'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='th'){
						return ['code' => 1, 'code_dec' => 'ประสบความสำเร็จ'];
					}
	}

	//审核
	public function taskOrderTrial(){

		$param		= input('post.');
		$userArr	= explode(',',auth_code($param['token'],'DECODE'));
		$uid		= $userArr[0];

		$order_id	= (input('post.order_id')) ? input('post.order_id') : 1;	// 任务ID;

		$lang		= (input('post.lang')) ? input('post.lang') : 'id';	// 语言类型

		$status		= (input('post.status')) ? input('post.status') : 2;	// 状态

		if(!$order_id){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
			return $data;
		}

		$info = model('UserTask')->field('ly_task.reward_price,ly_task.total_number,ly_task.order_number,ly_user_task.status as o_status,ly_user_task.uid,ly_user_task.handle_remarks,ly_user_task.task_id,ly_user_task.fuid')->join('ly_task','ly_task.id=ly_user_task.task_id')->where(array(['ly_task.status','=',3],['ly_user_task.id','=',$order_id],['ly_user_task.fuid','=',$uid],['ly_user_task.status','=',2]))->find();

		if(!$info){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
			return $data;
		}
		
		$userinfo		= model('Users')->field('ly_users.id,ly_users.username,ly_users.sid,user_total.balance')->join('user_total','ly_users.id=user_total.uid')->where('ly_users.id', $info['uid'])->find();

		if(!$userinfo){
			$data['code'] = 0;
			if($lang=='cn') $data['code_dec']	= '没有数据';
			elseif($lang=='en') $data['code_dec']	= 'No data!';
			elseif($lang=='id') $data['code_dec']	= 'tidak ada data';
			elseif($lang=='ft') $data['code_dec']	= '沒有數據';
			elseif($lang=='yd') $data['code_dec']	= 'कोई डाटा नहीं';
			elseif($lang=='vi') $data['code_dec']	= 'không có dữ liệu';
			elseif($lang=='es') $data['code_dec']	= 'Sin datos';
			elseif($lang=='ja') $data['code_dec']	= 'データがありません';
			elseif($lang=='th') $data['code_dec']	= 'ไม่มีข้อมูล';
			return $data;
		}

		$handle_remarks		=	(input('post.handle_remarks')) ? input('post.handle_remarks') : '';	// 审核说明

		switch($status){
			case 2://重新提交

				$up_trial_data = array(
					'status'			=>	2,//审核中
					'handle_remarks'	=>	$handle_remarks,//审核说明
					'handle_time'		=>	time(),
				);

				$is_up = model('UserTask')->where(array(['ly_user_task.id','=',$order_id],['ly_user_task.fuid','=',$uid],['ly_user_task.status','=',4]))->update($up_trial_data);

				if(!$is_up){
					if($lang=='cn'){
						return ['code' => 0, 'code_dec' => '失败'];
					}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
				}

				if($lang=='cn'){
					return ['code' => 1, 'code_dec' => '成功'];
				}elseif($lang=='en'){
						return ['code' => 1, 'code_dec' => 'Success'];
					}elseif($lang=='id'){
						return ['code' => 1, 'code_dec' => 'sukses'];
					}elseif($lang=='ft'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='yd'){
						return ['code' => 1, 'code_dec' => 'सफलता'];
					}elseif($lang=='vi'){
						return ['code' => 1, 'code_dec' => 'thành công'];
					}elseif($lang=='es'){
						return ['code' => 1, 'code_dec' => 'éxito'];
					}elseif($lang=='ja'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='th'){
						return ['code' => 1, 'code_dec' => 'ประสบความสำเร็จ'];
					}
			break;
			case 3://完成
				$up_trial_data = array(
					'status'			=>	3,//完成
					'handle_remarks'	=>	$handle_remarks,//审核说明
					'handle_time'		=>	time(),//审核时间
					'complete_time'		=>	time(),//完成时间
				);

				$is_up = model('UserTask')->where(array(['ly_user_task.id','=',$order_id],['ly_user_task.fuid','=',$uid],['ly_user_task.status','=',2]))->update($up_trial_data);
				if(!$is_up){
					if($lang=='cn'){
						return ['code' => 0, 'code_dec' => '失败'];
					}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
				}

				//任务提成
				$commission		=	$info['reward_price'];//任务单价

				if($commission > 0){

					//加余额钱
					$is_up_to = model('UserTotal')->where('uid', $userinfo['id'])->setInc('balance', $commission);

					if(!$is_up_to){
						$up_trial_data_r	=	array(
							'status'			=>	2,//审核
							'handle_remarks'	=>	$info['handle_remarks'],//审核说明
							'handle_time'		=>	time(),
						);
						 model('UserTask')->where(array(['ly_user_task.id','=',$order_id],['ly_user_task.fuid','=',$uid],['ly_user_task.status','=',3]))->update($up_trial_data_r);
						if($lang=='cn'){
							return ['code' => 0, 'code_dec' => '失败'];
						}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
					}
					//加总金额
					model('UserTotal')->where('uid', $userinfo['id'])->setInc('total_balance', $commission);
					// 流水
					$financial_data_p['uid'] 					= $userinfo['id'];
					$financial_data_p['username'] 				= $userinfo['username'];
					$financial_data_p['order_number'] 			= $info['order_number'];
					$financial_data_p['trade_number'] 			= 'L'.trading_number();
					$financial_data_p['trade_type'] 			= 6;
					$financial_data_p['trade_before_balance']	= $userinfo['balance'];
					$financial_data_p['trade_amount'] 			= $commission;
					$financial_data_p['account_balance'] 		= $userinfo['balance'] + $commission;
					$financial_data_p['remarks'] 				= '完成任务';
					$financial_data_p['types'] 					= 1;	// 用户1，商户2

					model('common/TradeDetails')->tradeDetails($financial_data_p);

					//已经完成的 和 总的任务数 一样 更新任务 完成

					$y_surplus_number	=	model('UserTask')->where(array(['task_id','=',$info['task_id']],['status','=',3]))->count();

					if($y_surplus_number==$info['total_number']){
						$arr = array(
							'status'			=>4,//完成
							'complete_time'		=>	time(),//完成时间
						);
						$this->where(array(['id','=',$info['task_id']],['uid','=',$info['fuid']],['status','=',3]))->update($arr);
					}

					//上级返点
					if($userinfo['sid']){
						$rebatearr = array(
							'num'			=>	1,
							'uid'			=>	$userinfo['id'],
							'sid'			=>	$userinfo['sid'],
							'order_number'	=>	$info['order_number'],
							'commission'	=>	$commission,
						);

						$this->setrebate($rebatearr);
					}
				}
				
				//更新每日完成任务次数
				$UserDailydata = array(
					'uid'				=>	$userinfo['id'],
					'username'			=>	$userinfo['username'],
					'field'				=>	'w_t_o_n',//完成
					'value' 			=>	1,
				);
				model('UserDaily')->updateReportfield($UserDailydata);


				if($lang=='cn'){
					return ['code' => 1, 'code_dec' => '成功'];
				}elseif($lang=='en'){
						return ['code' => 1, 'code_dec' => 'Success'];
					}elseif($lang=='id'){
						return ['code' => 1, 'code_dec' => 'sukses'];
					}elseif($lang=='ft'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='yd'){
						return ['code' => 1, 'code_dec' => 'सफलता'];
					}elseif($lang=='vi'){
						return ['code' => 1, 'code_dec' => 'thành công'];
					}elseif($lang=='es'){
						return ['code' => 1, 'code_dec' => 'éxito'];
					}elseif($lang=='ja'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='th'){
						return ['code' => 1, 'code_dec' => 'ประสบความสำเร็จ'];
					}

			break;
			case 4://已失败

				$up_trial_data = array(
					'status'			=>	4,//已失败
					'handle_remarks'	=>	$handle_remarks,//审核说明
					'handle_time'		=>	time(),
				);
				$is_up = model('UserTask')->where(array(['ly_user_task.id','=',$order_id],['ly_user_task.fuid','=',$uid],['ly_user_task.status','=',2]))->update($up_trial_data);

				if(!$is_up){
					if($lang=='cn'){
						return ['code' => 0, 'code_dec' => '失败'];
					}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
				}

				//失败加任务
				//更新次数
				$this->where('id', $info['task_id'])->Dec('receive_number',1)->Inc('surplus_number',1)->update();
				
				//更新每日失败任务次数
				$UserDailydata = array(
					'uid'				=>	$userinfo['id'],
					'username'			=>	$userinfo['username'],
					'field'				=>	's_t_o_n',//失败
					'value' 			=>	1,
				);
				model('UserDaily')->updateReportfield($UserDailydata);

				if($lang=='cn'){
					return ['code' => 1, 'code_dec' => '成功'];
				}elseif($lang=='en'){
						return ['code' => 1, 'code_dec' => 'Success'];
					}elseif($lang=='id'){
						return ['code' => 1, 'code_dec' => 'sukses'];
					}elseif($lang=='ft'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='yd'){
						return ['code' => 1, 'code_dec' => 'सफलता'];
					}elseif($lang=='vi'){
						return ['code' => 1, 'code_dec' => 'thành công'];
					}elseif($lang=='es'){
						return ['code' => 1, 'code_dec' => 'éxito'];
					}elseif($lang=='ja'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='th'){
						return ['code' => 1, 'code_dec' => 'ประสบความสำเร็จ'];
					}
			break;
			case 5://恶意
				$up_trial_data = array(
					'status'			=>	5,//恶意
					'handle_remarks'	=>	$handle_remarks,//审核说明
					'handle_time'		=>	time(),
				);

				$is_up = model('UserTask')->where(array(['ly_user_task.id','=',$order_id],['ly_user_task.fuid','=',$uid],['ly_user_task.status','=',2]))->update($up_trial_data);

				if(!$is_up){
					if($lang=='cn'){
						return ['code' => 0, 'code_dec' => '失败'];
					}elseif($lang=='en'){
						return ['code' => 0, 'code_dec' => 'Fail'];
					}elseif($lang=='id'){
						return ['code' => 0, 'code_dec' => 'gagal'];
					}elseif($lang=='ft'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='yd'){
						return ['code' => 0, 'code_dec' => 'असफल'];
					}elseif($lang=='vi'){
						return ['code' => 0, 'code_dec' => 'hỏng'];
					}elseif($lang=='es'){
						return ['code' => 0, 'code_dec' => 'Fracaso'];
					}elseif($lang=='ja'){
						return ['code' => 0, 'code_dec' => '失敗'];
					}elseif($lang=='th'){
						return ['code' => 0, 'code_dec' => 'เสียเหลี่ยม'];
					}
				}

				//失败加任务
				//更新次数
				$this->where('id', $info['task_id'])->Dec('receive_number',1)->Inc('surplus_number',1);

				//更新每日恶意任务次数
				$UserDailydata = array(
					'uid'				=>	$userinfo['id'],
					'username'			=>	$userinfo['username'],
					'field'				=>	'e_t_o_n',//恶意
					'value' 			=>	1,
				);
				model('UserDaily')->updateReportfield($UserDailydata);
				if($lang=='cn'){
					return ['code' => 1, 'code_dec' => '成功'];
				}elseif($lang=='en'){
						return ['code' => 1, 'code_dec' => 'Success'];
					}elseif($lang=='id'){
						return ['code' => 1, 'code_dec' => 'sukses'];
					}elseif($lang=='ft'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='yd'){
						return ['code' => 1, 'code_dec' => 'सफलता'];
					}elseif($lang=='vi'){
						return ['code' => 1, 'code_dec' => 'thành công'];
					}elseif($lang=='es'){
						return ['code' => 1, 'code_dec' => 'éxito'];
					}elseif($lang=='ja'){
						return ['code' => 1, 'code_dec' => '成功'];
					}elseif($lang=='th'){
						return ['code' => 1, 'code_dec' => 'ประสบความสำเร็จ'];
					}
			break;
		}
	}

	public function setrebate($param){
		if($param['num']<4){//上三级

			$rebate			=	model('Setting')->where('id',1)->value('rebate'.$param['num']);//返点值
			if($rebate){

				$rebate_amount	=	round($param['commission']*($rebate/100),2);

				if($rebate_amount>0){
					$userinfo = model('Users')->field('ly_users.id,ly_users.username,ly_users.sid,user_total.balance')->join('user_total','ly_users.id=user_total.uid')->where('ly_users.id', $param['sid'])->find();

					if($userinfo){

						$is_up_to = model('UserTotal')->where('uid', $userinfo['id'])->setInc('balance', $rebate_amount);

						if($is_up_to){
							model('UserTotal')->where('uid', $userinfo['id'])->setInc('total_balance', $rebate_amount);
							// 流水
							$financial_data_p['uid'] 					= $userinfo['id'];
							$financial_data_p['sid']					= $param['uid'];
							$financial_data_p['username'] 				= $userinfo['username'];
							$financial_data_p['order_number'] 			= $param['order_number'];
							$financial_data_p['trade_number'] 			= 'L'.trading_number();
							$financial_data_p['trade_type'] 			= 5;
							$financial_data_p['trade_before_balance']	= $userinfo['balance'];
							$financial_data_p['trade_amount'] 			= $rebate_amount;
							$financial_data_p['account_balance'] 		= $userinfo['balance'] + $rebate_amount;
							$financial_data_p['remarks'] 				= '下级返点';
							$financial_data_p['types'] 					= 1;	// 用户1，商户2

							model('common/TradeDetails')->tradeDetails($financial_data_p);
						}
					}
					if($userinfo['sid']){
						$rebatearr = array(
							'num'			=>	$param['num']+1,
							'uid'			=>	$userinfo['id'],
							'sid'			=>	$userinfo['sid'],
							'order_number'	=>	$param['order_number'],
							'commission'	=>	$param['commission'],
						);
						$this->setrebate($rebatearr);
					}
				}
			}
		}
	}
}
