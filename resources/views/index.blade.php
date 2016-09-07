<xml>
    <ToUserName><![CDATA[{{ $message->FromUserName }}]]></ToUserName>
    <FromUserName><![CDATA[{{ $message->ToUserName }}]]></FromUserName>
    <CreateTime>{{ time() }}</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[{{ 'From:'.$message->FromUserName.'To:'.$message->ToUserName.'Msg:'.$message->Content }}]]></Content>
</xml>

