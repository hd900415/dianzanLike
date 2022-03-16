<?php
/* 橘子科技旗下 A4源码所有  https://an4.net 搭建二开联系 QQ 3479863005  纸飞机联系 @orangetech */

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace think\console\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\App;

class Version extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('version')
            ->setDescription('show thinkphp framework version');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('v' . App::version());
    }
}
