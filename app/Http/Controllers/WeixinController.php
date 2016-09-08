<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\WxController;

use Log;

class WxMsgInstruction {
	public $_fromUserName;
	public $_toUserName;
	public $_msg;
	function __construct($fromUserName, $toUserName, $msg) {
		$this->_fromUserName = $toUserName;
		$this->_toUserName = $fromUserName;
		$this->_msg = $msg;
		Log::info($this->_msg);
	}
}

class WeixinController extends WxController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	//echo "wxctl index";
	Log::info("WeixinController index");
	echo $_GET["echostr"];
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		Log::info("WeixinController store");
    	$message = file_get_contents('php://input');
    	$message = simplexml_load_string($message, 'SimpleXMLElement', LIBXML_NOCDATA);
    	
    	$respMsg = '';
    	$recvMsg = $message->Content;
    	Log::info("recvMsg--from--{$message->FromUserName}--to--{$message->ToUserName}--content--{$message->Content}--");
    	
    	
    	// 刚进来，如果没有注册，则先记录其wx_user_id，然后让他输入姓名
    	$oldUser = $this->getModel('user')->where('wx_user_id', $message->FromUserName)->first();
		if(empty($oldUser)) {
			$newUser = $this->newModel('user');
			$newUser->wx_user_id = $message->FromUserName;
			$newUser->user_name = '';
			$newUser->phone_num = '';
			$newUser->group_id = 0;
			$newUser->reg_status = 0;
			$newUser->save();
        	$respMsg = '你好，教会事工公众号可以记录读经和代祷。请先注册，输入你的姓名：';
    	}
    	else { // 如果已经记录了wx_user_id，则检查姓名、手机、小组是否已输入，完成注册后才可以使用功能
    		$reg_status = $oldUser->reg_status;
    		if($reg_status == 0) {
    			// 输入的是用户名
    			$oldUser->update(array('user_name'=>$recvMsg, 'reg_status'=>1));
    			$respMsg = "你好，{$oldUser->user_name}。请继续注册，输入你的手机号码：";
    		}
    		else if($reg_status == 1) {
    			// 输入的是手机号码
    			$oldUser->update(array('phone_num'=>$recvMsg, 'reg_status'=>2));
    			$respMsg = "你好，{$oldUser->user_name}。你的手机号码是{$oldUser->phone_num}，请完成注册，输入你所在的小组编号：{$this->getGroupList()}";
    		}
    		else if($reg_status == 2) {
    			// 输入的是小组编号
    			$oldUser->update(array('group_id'=>$recvMsg, 'reg_status'=>3));
    			$respMsg = "注册完成，{$oldUser->user_name}。你的手机号码是{$oldUser->phone_num}，所在的小组是：{$this->getGroupName($oldUser->group_id)}";
    		}
    		else if($reg_status == 3) {
    			// 已完成注册
    			if(in_array($recvMsg, ['1', '2'])) {
    				$respMsg = "你输入的功能编号是【{$message->Content}】，该功能正在开发中。";
    			}
    			else if($recvMsg == '3') {
    				$groupName = $this->getGroupName($oldUser->group_id);
    				$respMsg = "本周{$groupName}已经收集上来的代祷是：\n{$groupPrayer}\n\n回复【1】继续";
    			}
    			else {
    				$respMsg = "请输入功能对应的编号：\n【1】 个人信息\n【2】 读经记录\n【3】 填写代祷";
    			}
    		}
    	}
    	
    	$retMsg = new WxMsgInstruction($message->FromUserName, $message->ToUserName, $respMsg);
    	Log::info('retMsg: '.$retMsg->_fromUserName.' - '.$retMsg->_toUserName.' - '.$retMsg->_msg);
    	$ret = view('index')->with('message', $retMsg);
    	Log::info('store ret: '.$ret);
    	return $ret;
    }

	private function getGroupName($groupId)
	{
		$oldGroup = $this->getModel('group')->where('id', $groupId)->first();
		if(!empty($oldGroup)) {
			return $oldGroup->group_name;
		}
		else {
			return '无';
		}
	}

	private function getGroupList()
	{
		$groupList = $this->getModel('group')->where('id', '>', 0)->get()->toArray();
		$groupString = '';
		foreach($groupList as $group) {
			$id = $group['id'];
			$name = $group['group_name'];
			$groupString .= "\n【{$id}】 {$name}";
		}
		return $groupString;
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
