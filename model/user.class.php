<?php
header('Content-type: text/html;charset=utf-8'); 
require_once('DB.class.php');
require_once('/../lib/php.php');
require_once('/../lib/logging.php');
class User
{
    private $key = "ollin2012";
    public function __construct($username,$uid,$pw){
        $this->username = $username;
        $this->pw = $pw;
        $this->DB = new DB();
        // $this->prize = "";
        $this->id = $uid;
        // $this->nickname = "";
    }
    public  static function signIn($phone,$pw){
        $db = new DB();
        $db->open();
        if($db->query_count(__CLASS__,array("phone='$phone'","pw='$pw'"))>0){
            $ret = mysql_fetch_array($db->query(__CLASS__,array("phone='$phone'")));
            // var_dump($ret);die();
            return new User($ret['user_name'],$ret['uid'],$pw);
        }else{
            return null;
        }
        $db->close();
    }
    public  static function signUp($username,$pw,$phone,$realname,$province="-",$city1="-",$city2="-",$buy_car_idea="-"){
        $db = new DB();
        $db->open();

        if($db->query_count(__CLASS__,array("phone='$phone'"))> 0 || $db->query_count(__CLASS__,array("user_name='$username'"))> 0){
            return null;
        }else{
            $result = $db->insert(__CLASS__,
                array("user_name", "pw","phone","real_name","province","city1","city2","buy_car_idea"),
                array("'{$username}'" ,"'{$pw}'","'{$phone}'","'{$realname}'","'{$province}'","'{$city1}'","'{$city2}'","'{$buy_car_idea}'")
            );

        }
        // var_dump($result);die();
        if(! $result) {
            logging(ERROR,'注册失败!'.mysql_error(),LOG_FILE);
            // echo 'Error:'.mysql_error().'\n';die();
        }else{

            logging(INFO,'注册成功新用户:'.$username,LOG_FILE);
            $uid = mysql_insert_id();
            return new User($username,$uid,$pw);
        }
        $db->close();
    }
    // public  function generateSign(){
    //     return md5($this->id.url_encode($this->username).$this->key);
    // } 
    public  static function generateSign($id,$username,$key = "ollin2012"){
        return md5($id.url_encode($username).$key);
    }
    public static function recordScore($uid,$username,$score,$gametype){
        $db = new DB();
        $db->open();
        if($db->query_count(__CLASS__,array("uid='$uid'"))> 0){
            $ret = $db->insert('score', array("uid","user_name","score","game_type") ,array("'{$uid}'","'{$username}'","'{$score}'","'{$gametype}'"));
            // echo 123;
        }
        if(!$ret){
            // echo mysql_error();
            logging(ERROR,$username.':新成绩入库失败!'.mysql_error(),LOG_FILE);
        }else{
            logging(INFO,$username.':新成绩入库!',LOG_FILE);
             $ret = mysql_fetch_array($db->query(__CLASS__,array("uid='$uid'")));
            // var_dump($ret);die();
            $drawCount = (int)$ret['has_draw_count'];
            $drawCount++;
            $ret = $db->update(__CLASS__,array("user_name in ('$username')"),array("has_draw_count='$drawCount'"));
            return $ret;
        }
        $db->close();
    }
    public function recordPrize($prize,$prizetype,$month){
        $this->DB->open();
        $ret = $this->DB->insert('prize',array("uid","user_name","prize","prize_type","month") ,array("'{$this->id}'","'{$this->username}'","'{$prize}'","'{$prizetype}'","'{$month}'"));
        if(!$ret){
            logging(INFO,$this->username.':新奖品入库失败!'.mysql_error(),LOG_FILE);
            // echo mysql_error();die();
        }else{
            logging(INFO,$this->username.':新奖品入库!',LOG_FILE);
            return true;
        } 
        $this->DB->close();
    }
    public static function  updatePrize($uids = array()){
        $db = new DB();
        $db -> open();
        $conditions =array();
        $conditions[] = "uid in ('".join($uids,"','")."')";
        $db->update('prize',$conditions,array("prize_given=1"));
        $db->close();
    }
    public function updateDrawCount ($drawCount){
        if($drawCount>-1){
            $this->DB->open();
            $ret = $this->DB->update(__CLASS__,array("user_name in ('$this->username')"),array("has_draw_count='$drawCount'"));
            return $ret;
            // var_dump($ret);die();
           
            $this->DB->close();
        }
    }
    public function getDrawCount(){
        $this->DB->open();
        $ret = mysql_fetch_array($this->DB->query(__CLASS__,array("user_name='$this->username'")));
            // print_r($ret);die();
        if($ret) {
            return $ret['has_draw_count'];
        }else{
            echo 'Error:'+mysql_error().'\n';
            return 0;
        }
        $this->DB->close();
    }
    public function queryPrize($conditions = array()){
        $this->DB->open();
        $prizes =  array();
        $result = $this->DB->query('prize', $conditions, "id DESC");
        while ($row = mysql_fetch_assoc($result)) {
            $prizes[] = $row['prize'];
            // $this->id = $row['uid'];
        }
        $this->DB->close();
        return $prizes;
    } 
    public static function winnerListAll(){
        $db = new DB();
        $db -> open();
        $list = array(array('name'=>'naili','list'=>array()),array('name'=>'jieyou','list'=>array()),array('name'=>'total','list'=>array()),array('name'=>'draw','list'=>array()),array('name'=>'allstar','list'=>array()));
        $ret = $db->query('prize',array());
        while ($row = mysql_fetch_assoc($ret)) {
            $list[$row['prize_type']-1]['list'][] =$row['user_name'] ;//array("username"=>$row['user_name'],"id"=>$row['id']);
        }
        // $list[] = json_encode($list);
        return json_encode($list);
        $db->close();
    }  
    public static function getUser($page){
        $pageCount = 15;
        $db = new DB();
        $db -> open();
        $users = array();
        $ret = $db->query(__CLASS__,array(),null,($page-1)*$pageCount+1,$pageCount);
        // print_r($ret);die();
        while($row = mysql_fetch_assoc($ret)){
            $users[] = array('username'=>$row['user_name'],'realname'=>$row['real_name'],'phone'=>$row['phone'],'has_draw_count'=>$row['has_draw_count'],'province'=>$row['province'],'city1'=>$row['city1'],'city2'=>$row['city2'],'buy_car_idea'=>$row['buy_car_idea'],'signup_time'=>$row['signup_time']);
        }
        return json_encode($users);
        $db->close();
    }
    public static function saveUser($username,$realname,$phone,$has_draw_count){
        $db = new DB();
        $db -> open();
        $ret = $db->update(__CLASS__,array("user_name in ('$username')"),array("real_name='$realname'","phone='$phone'","has_draw_count='$has_draw_count'"));
        return $ret?'ok':'error';
        $db -> close();
    }
    public static function delUser($username){
        $db = new DB();
        $db -> open();
        $ret = $db->delete(__CLASS__,array("user_name='$username'"));
        return $ret?'ok':'error';
        $db -> close();
    }
    public static function getPrize($page,$prizetype){
        $pageCount = 15;
        $db = new DB();
        $db -> open();
        $prize = array();
        // $ret = $db->query('prize',array("prize_type='$prizetype'"),null,($page-1)*$pageCount,$pageCount);
        // $sql = "select t1.user_name,t1.prize,t1.id,t1.prize_given,t1.time,t2.real_name,t2.phone from prize t1, user t2 limit ".($page-1)*$pageCount.",".$pageCount;
        // echo $sql;die();
        $sql = "select * from prize  left join user on prize.uid=user.uid where prize_type=".$prizetype." limit ".($page-1)*$pageCount.",".$pageCount;
        $ret = $db->execute($sql);

        // if(!$ret) echo mysql_error();die();
        while ($row = mysql_fetch_assoc($ret)) {
            
            $prize[] = array("username"=>$row['user_name'],"phone"=>$row['phone'],"realname"=>$row['real_name'],"id"=>$row['id'],"prize"=>$row['prize'],"prize_given"=>$row["prize_given"],"month"=>$row['month'],"time"=>$row['time']);
            // $this->id = $row['uid'];
        }
        // $list[] = json_encode($item);
        return json_encode($prize);
        $db->close();
    } 
    public static function savePrize($id,$prize,$prize_given){
        $db = new DB();
        $db -> open();
        $ret = $db->update('prize',array("id in ('$id')"),array("prize='$prize'","prize_given='$prize_given'"));
        return $ret?'ok':'error';
        $db -> close();
    }
    public static function delPrize($id){
        $db = new DB();
        $db -> open();
        $ret = $db->delete('prize',array("id='$id'"));
        return $ret?'ok':'error';
        $db -> close();
    }
    public static function getScore($page,$gametype){
        $pageCount = 15;
        $db = new DB();
        $db -> open();
        $score = array();
        $ret = $db->query('score',array("game_type='$gametype'"),null,($page-1)*$pageCount,$pageCount);
        while ($row = mysql_fetch_assoc($ret)) {
            
            $score[] = array("username"=>$row['user_name'],"id"=>$row['id'],"score"=>$row['score'],"time"=>$row['time']);
            // $this->id = $row['uid'];
        }
        // $list[] = json_encode($item);
        return json_encode($score);
        $db->close();
    }  
    public static function saveScore($id,$score){
        $db = new DB();
        $db -> open();
        $ret = $db->update('score',array("id in ('$id')"),array("score='$score'"));
        return $ret?'ok':'error';
        $db -> close();
    }
    public static function delScore($id){
        $db = new DB();
        $db -> open();
        $ret = $db->delete('score',array("id='$id'"));
        return $ret?'ok':'error';
        $db -> close();
    }
    public static function generatePrize($month){
        $db = new DB();
        $db -> open();
        $nailiPrize = array('200元充值卡','100元充值卡','50元充值卡');
        $jieyouPrize = array('200元充值卡','100元充值卡','50元充值卡');
        $totalPrize = array('1000元油卡','500元油卡','200元油卡');
        $allstarPrize = array('长虹彩电LEDA9000，5528元','长虹3DTV46780I，3988元','长虹3DTV42780I，3088元');
        $nailiIndex = 0;
        $jieyouIndex = 0;
        $totalIndex = 0;
        $allstarIndex = 0;
        $time = mktime(0,0,0,$month,1,2012);
        $firstDay =  date('Y-m-1', $time);
        $lastDay =  date('Y-m-t', $time);
        
        // $naili =false;
        // $jieyou =false;
        // $total =false;
        // $allstar =false;
        $db -> execute("delete from prize where prize_type in (1,2,3,5) and month=$month");
        $retNaili = $db -> execute("select * from (select * from `score` where game_type = 1 and time between '$firstDay' and '$lastDay' order by `time` desc) `temp`  group by user_name order by `time` desc limit 3");
        $retJieyou = $db -> execute("select * from (select * from `score` where game_type = 2 and time between '$firstDay' and '$lastDay' order by `time` desc) `temp`  group by user_name order by `time` desc limit 3");
        $retTotal = $db -> execute("SELECT uid,user_name, sum(score) as score from(select uid, user_name, max(score) as score from score where game_type in(1,2) and time between '$firstDay' and '$lastDay'  group by user_name, game_type ) as temp group by user_name order by score desc limit 3");
        $retAllstar = $db -> execute("select * from (select * from `score` where game_type = 3 and time between '$firstDay' and '$lastDay' order by `time` desc) `temp`  group by user_name order by `time` desc limit 3");
        // print_r ($retNaili);die();
        while ($row = mysql_fetch_assoc($retNaili)) {
            // var_dump($row);die();
            $naili = $db->insert('prize',array('uid','user_name','prize','prize_type','month'),array("'{$row['uid']}'","'{$row['user_name']}'","'$nailiPrize[$nailiIndex]'",1,"$month"));
            // print_r($naili);die();
            $nailiIndex++;
        }
        while ($row = mysql_fetch_assoc($retJieyou)) {
            $jieyou = $db->insert('prize',array('uid','user_name','prize','prize_type','month'),array("'{$row['uid']}'","'{$row['user_name']}'","'$jieyouPrize[$jieyouIndex]'",2,"$month"));
            $jieyouIndex++;
        }
        while ($row = mysql_fetch_assoc($retTotal)) {
            $total = $db->insert('prize',array('uid','user_name','prize','prize_type','month'),array("'{$row['uid']}'","'{$row['user_name']}'","'$totalPrize[$totalIndex]'",3,"$month"));
            $totalIndex++;
        }
        while ($row = mysql_fetch_assoc($retAllstar)) {
            $allstar = $db->insert('prize',array('uid','user_name','prize','prize_type','month'),array("'{$row['uid']}'","'{$row['user_name']}'","'$allstarIndex[$allstarIndex]'",5,"$month"));
            $allstarIndex++;
        }
        if($retNaili&&$retJieyou&&$retTotal&&$retAllstar){
            return 'ok';
        }
        $db -> close();
    }
}

?>
