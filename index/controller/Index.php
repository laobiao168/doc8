<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\index\logic\IndexLogic;
use think\Cache;
use think\Db;

class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index()
    {
        $this->assign('page', IndexLogic::getBanner());
        $this->assign('dh', IndexLogic::daohang());
        $this->assign('data', IndexLogic::getcontent());


        $this->assign('jyt', IndexLogic::getjingyingtie());
        $this->assign('gsb', IndexLogic::getgongshibang());
        $this->assign('zhzl', IndexLogic::getndzhzl());
        $this->assign('zbtk', IndexLogic::getzbtuku());

        $view = 'am';
        $vstr = '澳门';
        $this->assign('tag', $view);
        $this->assign('vstr', $vstr);
        
        
        
        return $this->view->fetch($view);
    }

    public function tw(){
        //开奖号码处理
        $sheng = ["猴", "羊", "马", "蛇", "龙", "兔", "虎", "牛", "鼠", "猪", "狗", "鸡"];
    	$ball_r = ["01", "02", "07", "08", "12", "13", "18", "19", "23", "24", "29", "30", "34", "35", "40", "45", "46"];
    	$ball_b = ["03", "04", "09", "10", "14", "15", "20", "25", "26", "31", "36", "37", "41", "42", "47", "48"];
    	$ball_g = ["05", "06", "11", "16", "17", "21", "22", "27", "28", "32", "33", "38", "39", "43", "44", "49"];
    	$week = ['星期日', '星期一','星期二','星期三','星期四','星期五','星期六'];
    	//本期
    	$benqi = Db::name('twkj')->order('qihao desc')->where(['num7'=>['neq', '']])->find();
    	$xiaqi = Db::name('twkj')->order('qihao desc')->where(['num7'=>''])->find();

    	$qiarr = [$benqi['qihao'], $xiaqi['qihao']];
    	$this->assign('qiarr', $qiarr);
    	$nextqi = $xiaqi['riqi'];
    	$nextqiarr['rqstr'] = date('m月d日 H点:i分',$nextqi);
    	$nextqiarr['rqtime'] = $nextqi;
    	$nextqiarr['rq'] = date('Y-m-d H:i:s', $nextqi);
    	$nextqiarr['nextweek'] = $week[date('w', $nextqi)];
    	$this->assign('nextqiarr', $nextqiarr);
    	$haomaarr = [$benqi['num1']??'',$benqi['num2']??'',$benqi['num3']??'',$benqi['num4']??'',$benqi['num5']??'',$benqi['num6']??'',$benqi['num7']??''];
    	$balls = [];
    	foreach($haomaarr as $num){
    	    $vo = [];
    	    $vo['ball'] = $num;
    	    if(in_array($num, $ball_r)){
    	        $vo['bgcolor'] = 'red';
    	    }elseif(in_array($num, $ball_b)){
    	        $vo['bgcolor'] = 'rgb(35, 137, 233)';
    	    }else{
    	        $vo['bgcolor'] = 'green';
    	    }
    	    $vo['shengxiao'] = $this->shengxiao($num);
    	    $balls[] = $vo;
    	}
    	$this->assign('balls', $balls);
    	return $this->view->fetch('twkj');
    }

    /**
     * 澳门开奖记录
     */
    public  function twkjls(){
        $lishi = Db::name('twkj')->order('qihao desc')->where(['num7'=>['neq', '']])->select();
    	$return = [];
    	foreach ($lishi as $item){
    	    $vo = [];
    	    $vo['id'] = $item['qihao'];
    	    $vo['issue'] = $item['qihao'];
    	    $vo['open_date'] = date('Y-m-d', $item['riqi']);
    	    $vo['v1'] = $this->getnum($item['num1']);
    	    $vo['v2'] = $this->getnum($item['num2']);
    	    $vo['v3'] = $this->getnum($item['num3']);
    	    $vo['v4'] = $this->getnum($item['num4']);
    	    $vo['v5'] = $this->getnum($item['num5']);
    	    $vo['v6'] = $this->getnum($item['num6']);
    	    $vo['v7'] = $this->getnum($item['num7']);
    	    $return[] = $vo;
    	}
    	
    	echo json_encode($return);
    	
    }
    public  function history_am2(){
        $ck = 'history_am2';
        if(Cache::has($ck)){
            return Cache::get($ck);
        }
        $lishi = Db::name('twkj')->order('qihao desc')->where(['num7'=>['neq', '']])->select();
        $return = [];
        foreach ($lishi as $item){
            $vo = [];
            $vo['issue'] = $item['qihao'];
            $vo['openTime'] = date('Y-m-d', $item['riqi']);
            $vo['openCode'] = $item['num1'].','.$item['num2'].','.$item['num3'].','.$item['num4'].','.$item['num5'].','.$item['num6'].','.$item['num7'];
            $vo['videoUrl'] = '';
            $return[] = $vo;
        }
        $obj = ['code'=>0, 'message'=>'Success', 'data'=>$return];
        $result = 'var historyAO ='. json_encode($obj);
        Cache::set($ck, $result, 60);
        echo $result;

    }


    /**
     * 澳门新彩
     */
    public  function twkjls2(){
        $lishi = Db::name('wmxckj')->order('qihao desc')->where(['num7'=>['neq', '']])->select();
        $return = [];
        foreach ($lishi as $item){
            $vo = [];
            $vo['id'] = $item['qihao'];
            $vo['issue'] = $item['qihao'];
            $vo['open_date'] = date('Y-m-d', $item['riqi']);
            $vo['v1'] = $this->getnum($item['num1']);
            $vo['v2'] = $this->getnum($item['num2']);
            $vo['v3'] = $this->getnum($item['num3']);
            $vo['v4'] = $this->getnum($item['num4']);
            $vo['v5'] = $this->getnum($item['num5']);
            $vo['v6'] = $this->getnum($item['num6']);
            $vo['v7'] = $this->getnum($item['num7']);
            $return[] = $vo;
        }

        echo json_encode($return);

    }
    public  function history_amxc(){
        $ck = 'history_amxc';
        if(Cache::has($ck)){
            return Cache::get($ck);
        }
        $lishi = Db::name('wmxckj')->order('qihao desc')->where(['num7'=>['neq', '']])->select();
        $return = [];
        foreach ($lishi as $item){
            $vo = [];
            $vo['issue'] = $item['qihao'];
            $vo['openTime'] = date('Y-m-d', $item['riqi']);
            $vo['openCode'] = $item['num1'].','.$item['num2'].','.$item['num3'].','.$item['num4'].','.$item['num5'].','.$item['num6'].','.$item['num7'];
            $vo['videoUrl'] = '';
            $return[] = $vo;
        }
        $obj = ['code'=>0, 'message'=>'Success', 'data'=>$return];
        $result = 'var historyAO ='. json_encode($obj);
        Cache::set($ck, $result, 60);
        echo $result;
    }



    private function getnum($num){
        //开奖号码处理
        $sheng = ["猴", "羊", "马", "蛇", "龙", "兔", "虎", "牛", "鼠", "猪", "狗", "鸡"];
    	$ball_r = ["01", "02", "07", "08", "12", "13", "18", "19", "23", "24", "29", "30", "34", "35", "40", "45", "46"];
    	$ball_b = ["03", "04", "09", "10", "14", "15", "20", "25", "26", "31", "36", "37", "41", "42", "47", "48"];
    	$ball_g = ["05", "06", "11", "16", "17", "21", "22", "27", "28", "32", "33", "38", "39", "43", "44", "49"];
    	$wuxin_j = ['01', '02', '09', '10', '23', '24', '31', '32', '39', '40'];
    	$wuxin_m = ['05', '06', '13', '14', '21', '22', '35', '36', '43', '44'];
    	$wuxin_s = ['11', '12', '19', '20', '27', '28', '41', '42', '49'];
    	$wuxin_h = ['07', '08', '15', '16', '29', '30', '37', '38', '45', '46'];
    	$wuxin_t = ['03', '04', '17', '18', '25', '26', '33', '34', '47', '48'];
    	$week = ['星期日', '星期一','星期二','星期三','星期四','星期五','星期六'];
    	
        $vo['num'] = $num;
	    if(in_array($num, $ball_r)){
	        $vo['bs_py'] = 'red';
	    }elseif(in_array($num, $ball_b)){
	        $vo['bs_py'] = 'blue';
	    }else{
	        $vo['bs_py'] = 'green';
	    }
	    if(in_array($num, $wuxin_j)){
	        $vo['wx'] = '金';
	    }elseif(in_array($num, $wuxin_m)){
	        $vo['wx'] = '木';
	    }elseif(in_array($num, $wuxin_s)){
	        $vo['wx'] = '水';
	    }elseif(in_array($num, $wuxin_h)){
	        $vo['wx'] = '火';
	    }elseif(in_array($num, $wuxin_t)){
	        $vo['wx'] = '土';
	    }
	    
	    $vo['sx'] = $this->shengxiao($num);
    	 return $vo;   
    }
    

    private function shengxiao($ball){
        $sx_hou = ['08','20','32','44'];
        $sx_yang = ['09','21','33','45'];
        $sx_ma = ['10','22','34','46'];
        $sx_she = ['11','23','35','47'];
        $sx_long = ['12','24','36','48'];
        $sx_tu = ['01','13','25','37','49'];
        $sx_hu = ['02','14','26','38'];
        $sx_niu = ['03','15','27','39'];
        $sx_shu = ['04','16','28','40'];
        $sx_zhu = ['05','17','29','41'];
        $sx_gou = ['06','18','30','42'];
        $sx_ji = ['07','19','31','43'];
        if(in_array($ball, $sx_hou)){
            return '猴';
        }
        if(in_array($ball, $sx_yang)){
            return '羊';
        }
        if(in_array($ball, $sx_shu)){
            return '鼠';
        }
        if(in_array($ball, $sx_niu)){
            return '牛';
        }
        if(in_array($ball, $sx_hu)){
            return '虎';
        }
        if(in_array($ball, $sx_tu)){
            return '兔';
        }
        if(in_array($ball, $sx_long)){
            return '龙';
        }
        if(in_array($ball, $sx_she)){
            return '蛇';
        }
        if(in_array($ball, $sx_ma)){
            return '马';
        }
        if(in_array($ball, $sx_ji)){
            return '鸡';
        }
        if(in_array($ball, $sx_gou)){
            return '狗';
        }
        if(in_array($ball, $sx_zhu)){
            return '猪';
        }
        return '';
    }
    
    public function gst()
    {
        $this->assign('page', IndexLogic::getBanner());
        $id = $this->request->param('id', 0);
        $this->assign('info', Db::name('lqz_gst')->where(['id'=>$id])->find());
        return $this->view->fetch();
    }

    public function jyt()
    {
        $this->assign('page', IndexLogic::getBanner());
        $id = $this->request->param('id', 0);
        $this->assign('info', Db::name('lqz_jyt')->where(['id'=>$id])->find());
        return $this->view->fetch('gst');
    }


    public function gsb()
    {
        $this->assign('page', IndexLogic::getBanner());
        $id = $this->request->param('id', 0);
        $this->assign('info', Db::name('lqz_gsb')->where(['id'=>$id])->find());
        return $this->view->fetch('gst');
    }



    public function zlinfo()
    {
        $this->assign('page', IndexLogic::getBanner());
        $id = $this->request->param('id', 0);
        $this->assign('info', Db::name('zhzl')->where(['id'=>$id])->find());
        return $this->view->fetch();
    }


    /**
     * 开奖器
     * @return string
     * @throws \think\Exception
     */
    public function kj()
    {
        return $this->view->fetch();
    }
	public function kj2()
    {
        return $this->view->fetch();
    }

}
