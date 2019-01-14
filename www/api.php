<?php
require "conn.php";
date_default_timezone_set('PRC');
if(isset($_GET['type'])){
    $c_type = $_GET['type'];
    switch ($c_type){
        case "login":
            include('httprequests/Requests.php');
            Requests::register_autoloader();
            if(isset($_GET['lg_code'])){
                $code = $_GET['lg_code'];
                if($code != ""){
                    $request = Requests::get("https://api.weixin.qq.com/sns/jscode2session?appid=wx661987e4032cda91&secret=9ed2da072eb89751781b14fbf3511839&js_code=$code&grant_type=authorization_code");
                    $json_data = json_decode($request->body, true);
                    if(isset($json_data['errcode'])){
                        echo '{"error":"'.$json_data['errmsg'].'"}';
                        exit;
                    }else{
                        echo '{"openid":"'.$json_data['openid'].'"}';
                        exit;
                    }
                }
            }
            break;
        case "update":
            if(isset($_POST['wx_openid'])){
                
                $openid = $_POST['wx_openid'];
                if($openid="null"){
                    exit;
                }
                $sql = "SELECT Id FROM find_users WHERE user_wx='$openid'";
                $nickname = $_POST['wx_name'];
                $aurl = $_POST['wx_aurl'];
                $timec = time();
                $time = date('Y-m-d H:i:s');
                if($db->query($sql)->rowCount()==0){
                    $sql = "INSERT INTO find_users(`user_wx`,`user_name`,`user_aurl`,`user_uptime`,`user_regtime`) VALUES ('$openid','$nickname','$aurl','$timec','$time')";
                    if($db->query($sql)){
                        echo '{"return":"更新完成-1"}';
                    }else{
                        echo '{"return":"更新失败-1"}';
                    }
                }else{
                    $sql = "UPDATE find_users SET user_name='$nickname', user_aurl='$aurl',user_uptime='$timec' WHERE user_wx='$openid'";
                    if($db->query($sql)){
                        echo '{"return":"更新完成-2"}';
                    }else{
                        echo '{"return":"更新失败-2"}';
                    }
                }
            }
            break;
        case "push":
            if(isset($_POST['wx_openid'])){
                $openid = $_POST['wx_openid'];
                $c_type=$_POST['c_type'];
                $c_text=$_POST['c_text'];
                $c_address = $_POST['c_address'];
                $c_phone = $_POST['c_phone'];
                $c_cardid = $_POST['c_cardid'];
                $time = date('Y-m-d H:i:s');
                $timec = time();
                $sql = "INSERT INTO find_content(`c_userid`,`c_type`,`c_text`,`c_address`,`c_phone`,`c_cardid`, `c_uptime`,`c_pushtime`) VALUES ('$openid','$c_type','$c_text','$c_address','$c_phone','$c_cardid','$timec','$time')";
                if($db->query($sql)){
                    echo '{"return":"10000"}'; //插入成功
                    if($c_type==1){
                        if($c_cardid !=""){
                            $row=$db->query("SELECT c_userid, c_uptime,c_phone FROM find_content WHERE c_cardid='$c_cardid' and c_type='2'");
                            if($row->rowCount()!=0){
                                $firstdata = $row->FetchAll()[0];
                                $towechat = $firstdata['c_userid'];
                                $pushtime =  date('Y-m-d H:i:s', $firstdata['c_uptime']);
                                $fromwx = $firstdata['c_userid'];
                                $phonetext =  $firstdata['c_phone'] == '' ? '未留下联系方式':'联系方式:'.$firstdata['c_phone'];
                                $sql = "SELECT Id FROM find_users WHERE user_wx='$openid'";
                                $wechatid =  $db->query($sql)->FetchAll()[0]['Id'];
                                $emailtext= "我在$pushtime 捡到你校园卡,请长按回复消息与我($phonetext)联系![系统自动发送,已找回则忽略]";
                                $sql = "INSERT INTO find_email(`email_from`,`email_to`,`email_text`, `email_time`, `email_status`) VALUES ('$fromwx','$wechatid','$emailtext','$time','0')";
                                if($wechatid != ""){
                                    $db->query($sql);
                                }
                               
                            }
                        }
                    }else{
                        if($c_cardid !=""){
                            $row=$db->query("SELECT s_wx FROM find_school WHERE s_cid=$c_cardid");
                            if($row->rowCount()!=0){
                                $towechat = $row->FetchAll()[0]['s_wx'];
                                $sql = "SELECT Id FROM find_users WHERE user_wx='$towechat'";
                                $wechatid =  $db->query($sql)->FetchAll()[0]['Id'];
                                $phonetext = $c_phone == '' ? '未留下联系方式':'联系方式:'.$c_phone;
                                $emailtext= "我在$time 捡到你校园卡,请长按回复消息或与我($phonetext)联系![系统自动发送,已找回则忽略]";
                                $sql = "INSERT INTO find_email(`email_from`,`email_to`,`email_text`, `email_time`, `email_status`) VALUES ('$openid','$wechatid','$emailtext','$time','0')";
                                if($wechatid != ""){
                                    $db->query($sql);
                                }
                            }
                        }
                    }
                }else{
                    echo '{"return":"10001"}'; //插入失败
                }
            }
            break;
        case "getinfo":

            if(isset($_GET['page'])){
                $page = $_GET['page']=="" ? 1 : $_GET['page']; 
            }else{
                $page=1;
            }
            $everyget = 10;
            $thisget = ($page-1)*10;
            $sql = "SELECT u.Id ,u.user_name, u.user_aurl,c.c_type,c.c_text,c.c_address, c.c_phone, c.c_uptime FROM find_content c LEFT JOIN find_users u ON c.c_userid=u.user_wx ORDER BY c.c_uptime DESC limit $thisget,$everyget";
            $json_data =[];
            foreach ($db->query($sql) as $row) {
                $nowtime = time();
                $dtime = $nowtime-$row['c_uptime'];
                if($dtime<300){
                    $showtime = "刚刚";
                }elseif($dtime<3600){
                    $showtime = intval($dtime/3600+1)."小时之内";
                }elseif($dtime<86400){
                    $showtime = "1天之内";    
                }else{
                    $showtime = "1天之前";
                }
                $content = array("uid"=>$row['Id'],"username"=>$row['user_name'],"useraurl"=>$row['user_aurl'],"ctype"=>$row['c_type'],"ctext"=>$row['c_text'],"caddress"=>$row['c_address'],"cphone"=>$row['c_phone'],"time"=>$showtime);
                array_push($json_data,$content);
            }
            $data = array("items"=>$json_data);
            echo json_encode($data);
            break;

        case "getcount":
            if(isset($_GET['wx_openid'])){
                if($_GET['wx_openid']){
                    $openid=$_GET['wx_openid'];
                    $sql = "SELECT Id FROM find_content WHERE c_userid='$openid'";
                    $cpush = $db->query($sql)->rowCount();
                    $sql ="SELECT count(Id) as cemail FROM find_email WHERE email_to=(SELECT Id FROM find_users WHERE user_wx='$openid')";
                    $cemail = $db->query($sql)->FetchAll()[0]['cemail'];
                    echo '{"cpush":"'.$cpush.'","cemail":"'.$cemail.'"}'; //返回
                }
            }
            break;
        case "ccard":
            if(isset($_POST['wx_openid'])){
                $openid = $_POST['wx_openid'];
                $cardid=$_POST['card_id'];
                $time = date('Y-m-d H:i:s');
                $sql = "SELECT Id FROM find_school WHERE s_wx='$openid'";
                
                if($db->query($sql)->rowCount()==0){
                    $sql = "INSERT INTO find_school(`s_wx`,`s_cid`,`s_time`) VALUES ('$openid','$cardid','$time')";
                    if($db->query($sql)){
                        echo '{"return":"更新完成-1"}';
                        $row=$db->query("SELECT c_userid, c_uptime,c_phone FROM find_content WHERE c_cardid='$cardid'");
                        if($row->rowCount()!=0){
                            $firstdata = $row->FetchAll()[0];
                            $pushtime =  date('Y-m-d H:i:s', $firstdata['c_uptime']);
                            $fromwx = $firstdata['c_userid'];
                            $phonetext =  $firstdata['c_phone'] == '' ? '未留下联系方式':'联系方式:'.$firstdata['c_phone'];
                            $sql = "SELECT Id FROM find_users WHERE user_wx='$openid'";
                            $wechatid =  $db->query($sql)->FetchAll()[0]['Id'];
                            $emailtext= "我在$pushtime 捡到你校园卡,请长按回复消息并与我($phonetext)联系![系统自动发送,已找回则忽略]";
                            $sql = "INSERT INTO find_email(`email_from`,`email_to`,`email_text`, `email_time`, `email_status`) VALUES ('$fromwx','$wechatid','$emailtext','$time','0')";
                            if($wechatid != ""){
                                $db->query($sql);
                            }
                        }
                    }else{
                        echo '{"return":"更新失败-1"}';
                    }
                }else{
                    $sql = "UPDATE find_school SET s_cid='$cardid', s_time='$time' WHERE s_wx='$openid'";
                    if($db->query($sql)){
                        echo '{"return":"更新完成-2"}';
                        $row=$db->query("SELECT c_userid, c_uptime,c_phone FROM find_content WHERE c_cardid='$cardid'");
                        if($row->rowCount()!=0){
                            $firstdata = $row->FetchAll()[0];
                            $pushtime =  date('Y-m-d H:i:s', $firstdata['c_uptime']);
                            $fromwx = $firstdata['c_userid'];
                            $phonetext =  $firstdata['c_phone'] == '' ? '未留下联系方式':'联系方式:'.$firstdata['c_phone'];
                            $sql = "SELECT Id FROM find_users WHERE user_wx='$openid'";
                            $wechatid =  $db->query($sql)->FetchAll()[0]['Id'];
                            $emailtext= "我在$pushtime 捡到你校园卡,请长按回复消息并与我($phonetext)联系![系统自动发送,已找回则忽略]";
                            $sql = "INSERT INTO find_email(`email_from`,`email_to`,`email_text`, `email_time`, `email_status`) VALUES ('$fromwx','$wechatid','$emailtext','$time','0')";
                            if($wechatid != ""){
                                $db->query($sql);
                            }
                        }
                    }else{
                        echo '{"return":"更新失败-2"}';
                    }
                }
            }
            break;
        case "getcardid":
            if(isset($_GET['wx_openid'])){
                if($_GET['wx_openid']){
                    $openid=$_GET['wx_openid'];
                    $sql = "SELECT s_cid FROM find_school WHERE s_wx='$openid'";
                    $cardid = $db->query($sql)->FetchAll()[0]['s_cid'];
                    echo '{"cardid":"'.$cardid.'"}'; //返回
                }
            }
            break;
        case "getmyemail":
            if(isset($_GET['wx_openid'])){
                if($_GET['wx_openid']){
                    $openid=$_GET['wx_openid'];
                    $sql ="SELECT Id,email_from,email_text,email_time FROM find_email WHERE email_to=(SELECT Id FROM find_users WHERE user_wx='$openid') ORDER BY email_time DESC";
                    $json_data =[];
                    foreach ($db->query($sql) as $row) {
                        $content = array("cid"=>$row['Id'],"email_from"=>$row['email_from'],"email_text"=>$row['email_text'],"email_time"=>$row['email_time']);
                        array_push($json_data,$content);
                    }
                    $data = array("items"=>$json_data);
                    echo json_encode($data);
                }
            }      
            break;
        case "getmyinfo":
            if(isset($_GET['wx_openid'])){
                if($_GET['wx_openid']){
                    $openid=$_GET['wx_openid'];
                    $sql = "SELECT Id, c_type,c_text FROM find_content WHERE c_userid='$openid' ORDER BY c_uptime DESC";
                    $json_data =[];
                    foreach ($db->query($sql) as $row) {
                        $content = array("cid"=>$row['Id'],"ctype"=>$row['c_type'],"ctext"=>$row['c_text']);
                        array_push($json_data,$content);
                    }
                    $data = array("items"=>$json_data);
                    echo json_encode($data);
                }
            }      
            break;
        case "deleteinfo":
            if(isset($_GET['cid'])){
                if($_GET['cid']){
                    $cid=$_GET['cid'];
                    $sql = "DELETE FROM find_content WHERE Id='$cid'";
                    if($db->query($sql)){
                        echo '{"return":"10000"}';
                    }else{
                        echo '{"return":"10001"}';
                    }
                    
                }
            }   
            break;
        case "deleteemail":
            if(isset($_GET['cid'])){
                if($_GET['cid']){
                    $cid=$_GET['cid'];
                    $sql = "DELETE FROM find_email WHERE Id='$cid'";
                    if($db->query($sql)){
                        echo '{"return":"10000"}';
                    }else{
                        echo '{"return":"10001"}';
                    }
                    
                }
            }   
            break;
        case "updateinfo":
            if(isset($_GET['cid'])){
                if($_GET['cid']){
                    $cid=$_GET['cid'];
                    $time = time();
                    $sql = "UPDATE find_content SET c_uptime='$time' WHERE Id='$cid'";
                    if($db->query($sql)){
                        echo '{"return":"10000"}';
                    }else{
                        echo '{"return":"10001"}';
                    }
                    
                }
            }   
            break;
        case "getadb":
            $sql = "SELECT adb_image FROM find_adb";
            $json_data =[];
            foreach ($db->query($sql) as $row) {
                array_push($json_data,$row['adb_image']);
            }
            echo json_encode($json_data);
            break;
        case "send":
            if(isset($_POST['wx_openid'])){
                $openid = $_POST['wx_openid'];
                $s_text=$_POST['s_text'];
                $s_sendto = $_POST['s_sendto'];
                $time = date('Y-m-d H:i:s');
                if($s_sendto != ""){
                    $sql = "INSERT INTO find_email(`email_from`,`email_to`,`email_text`, `email_time`, `email_status`) VALUES ('$openid','$s_sendto','$s_text','$time','0')";
               
                    if($db->query($sql)){
                        echo '{"return":"10000"}'; //插入成功
                    }else{
                        echo '{"return":"10001"}'; //插入失败
                    }
                }
            }
            break;
        case "getuser":
            if(isset($_GET['wx_openid'])){
                $openid = $_GET['wx_openid'];
                $sql = "SELECT Id,user_name FROM find_users WHERE user_wx='$openid'";
                $result = $db->query($sql);
                if($result){
                    $data = $result->FetchAll()[0];
                    echo '{"return":"10000","uid":"'.$data['Id'].'","name":"'.$data['user_name'].'"}'; //插入成功
                }else{
                    echo '{"return":"10001"}'; //插入失败
                }
            }
            break;
    }
}else{
	echo "Hello World!";
}


