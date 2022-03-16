<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

namespace app\common\controller;

use think\Controller;
// 加载二维码类
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;
// 加载二维码解析类
// use Zxing\QrReader;
// 加载二维码解析类2
use PHPZxing\PHPZxingDecoder;

class CommonController extends Controller{
	//初始化方法
	protected function initialize(){
	 	parent::initialize();
		header('Access-Control-Allow-Origin:*');
    }
	
	/**
	 * 发送短信验证码
	 * @param  [type] $phone [description]
	 * @return [type]        [description]
	 */
	public function sendSMSCode($phone){
		return model('Sms')->sendSMSCode($phone);
	}

	/**
	 * 文件上传
	 * @param  string  $data.file 			input的name，必传
	 * @param  string  $data.uploadPath 	上传路径，必传
	 * @param  array   $data.validate 		上传验证，如：['size'=>1000*1024*5,'ext'=>'jpg,png,gif,jpeg']
	 * @param  string  $data.rule 			命名规则，不设置默认date
	 * @param  string  $data.fileName 		文件名
	 * @return array
	 */
	public function uploadFile($data=array()){
		//数据验证
		$validate = validate('app\common\validate\Common');
		if (!$validate->scene('upload')->check($data)) return array('info' => false, 'error' => $validate->getError());
		// 获取文件
		$file = request()->file($data['file']);
		// 创建上传路径
		if(!is_dir($data['uploadPath'])) mkdir($data['uploadPath'], 0777, true);
		// 是否设置上传验证
		if (isset($data['validate'])) $file->validate($data['validate']);
		// 是否设置上传命名规则
		if (isset($data['rule'])) {
			$file->rule($data['rule']);
		} else {
			$file->rule('date');
		}

		// 上传（强烈不建议使用原文件名）
		if (isset($data['fileName']) && $data['fileName'] === true) {			
			// 设置保留原文件名且不覆盖
			$info = $file->move($data['uploadPath'], true, false);

		} else if (isset($data['fileName']) && $data['fileName']) {
			// 设置文件名，传空则保留原文件名
			$info = $file->move($data['uploadPath'], $fileName);

		} else {
			// 默认
			$info = $file->move($data['uploadPath']);
		}	
		
		if($info){
			// 成功上传后 获取上传信息
			return array('info' => $info, 'success' => ltrim($data['uploadPath'], '.').'/'.$info->getSaveName());
		}else{
			// 上传失败获取错误信息
			return array('info' => $info, 'error' => $file->getError());
		}
	}

	/**
	 * 生成二维码
	 * @param  string  qrcode 需要生成二维码的链接
	 * @param  string  imgName 图片保存名字
	 * @return string        二维码保存的路径
	 */
	public function produceQrcode($array=array(), $write=false){
		$validate = validate('app\common\validate\Common');
		if(!$validate->scene('produceQrcode')->check($array)) return $validate->getError();

		// 创建二维码
		$qrCode = new QrCode($array['qrcode']);
		$qrCode->setSize(300); //尺寸

		// 设置高级选项
		$qrCode->setWriterByName('png');	// 图片后缀
		$qrCode->setMargin(10);	// 边距
		$qrCode->setEncoding('UTF-8');	// 编码
		$qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
		$qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);	// 前景颜色
		$qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);	// 背景颜色
		// $qrCode->setLabel('扫一扫上面的二维码图案', 16,'C:/Windows/Fonts/simsun.ttc', LabelAlignment::CENTER());	// 底部字体
		if (isset($array['logoPath'])) $qrCode->setLogoPath($array['logoPath']);	// logo路径
		if (isset($array['logoPath'])) $qrCode->setLogoSize(60, 60);	// logo尺寸
		$qrCode->setRoundBlockSize(true);
		$qrCode->setValidateResult(false);
		$qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

		if ($write) {
			// 直接输出二维码
			header('Content-Type: '.$qrCode->getContentType());
			echo $qrCode->writeString();
		} else {
			// 保存到文件
			$basePath = './qrcode/';
			if (!is_dir($basePath)) mkdir($basePath, 0777, true);
			$savePath = $basePath.$array['imgName'].'.png';
			$qrCode->writeFile($savePath);
			if (!is_file($savePath)) return '获取二维码失败';
			// 返回完整二维码保存路径
			$isHttps = ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')) ? 'https' : 'http';	// 获取传输协议
			return $isHttps.'://'.$_SERVER['HTTP_HOST'].ltrim($savePath, '.');
		}
		// 创建响应对象
		// $response = new QrCodeResponse($qrCode);		
	}
	
	/**
	 * 解析二维码
	 * @return [type] [description]
	 */
	public function qrReader($path){
		// $qrcode = new QrReader($path);
		// $text = $qrcode->text(); // 从二维码返回解码文本
		// return $text;

		$decoder = new PHPZxingDecoder(['try_harder'=>true]);
		$decoder->setJavaPath('D:/Java/jdk1.8.0_212/jre/bin/java.exe'); // 设置java路径
		$data    = $decoder->decode($path);
		// return $data->getImageValue();
		return ($data->isFound()) ? $data->getImageValue() : false;
	}

}