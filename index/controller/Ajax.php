<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Cache;
use think\Db;
use think\Lang;
use think\Response;

/**
 * Ajax异步请求接口
 * @internal
 */
class Ajax extends Frontend
{

    protected $noNeedLogin = ['lang', 'upload', 'amkj', 'xgkjls', 'amkjls'];
    protected $noNeedRight = ['*'];
    protected $layout = '';

    /**
     * 加载语言包
     */
    public function lang()
    {
        $this->request->get(['callback' => 'define']);
        $header = ['Content-Type' => 'application/javascript'];
        if (!config('app_debug')) {
            $offset = 30 * 60 * 60 * 24; // 缓存一个月
            $header['Cache-Control'] = 'public';
            $header['Pragma'] = 'cache';
            $header['Expires'] = gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        }

        $controllername = input("controllername");
        $this->loadlang($controllername);
        //强制输出JSON Object
        return jsonp(Lang::get(), 200, $header, ['json_encode_param' => JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE]);
    }

    /**
     * 生成后缀图标
     */
    public function icon()
    {
        $suffix = $this->request->request("suffix");
        $suffix = $suffix ? $suffix : "FILE";
        $data = build_suffix_image($suffix);
        $header = ['Content-Type' => 'image/svg+xml'];
        $offset = 30 * 60 * 60 * 24; // 缓存一个月
        $header['Cache-Control'] = 'public';
        $header['Pragma'] = 'cache';
        $header['Expires'] = gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        $response = Response::create($data, '', 200, $header);
        return $response;
    }

    /**
     * 上传文件
     */
    public function upload()
    {
        return action('api/common/upload');
    }

    public function amkj(){
        $cc = $this->request->param('cc', 1);
        if($cc==1){
            //澳门六合彩
            $tb = 'twkj';
        }else{
            //澳门新材
            $tb = 'wmxckj';

        }
        $nextqi = Db::name($tb)->where(['num1'=>['eq', '']])->order('qihao asc')->find();
        $lishi = Db::name($tb)->where(['num1'=>['neq', '']])->order('qihao desc')->select();
        $return = [];
        foreach ($lishi as $ii=>$item){
            //处理开奖号码跳动
            if($ii==0){
                $second = time() - $item['uptime'];
                if($second >= 0){
                    if($item['num1']!=''){
                        $bsec = 10;
                        if($second < $bsec){
                            $item['num1'] = '';
                            $item['num2'] = '';
                            $item['num3'] = '';
                            $item['num4'] = '';
                            $item['num5'] = '';
                            $item['num6'] = '';
                            $item['num7'] = '';
                        }elseif($second < $bsec*2){
                            $item['num2'] = '';
                            $item['num3'] = '';
                            $item['num4'] = '';
                            $item['num5'] = '';
                            $item['num6'] = '';
                            $item['num7'] = '';
                        }elseif($second < $bsec*3){
                            $item['num3'] = '';
                            $item['num4'] = '';
                            $item['num5'] = '';
                            $item['num6'] = '';
                            $item['num7'] = '';
                        }elseif($second < $bsec*4){
                            $item['num4'] = '';
                            $item['num5'] = '';
                            $item['num6'] = '';
                            $item['num7'] = '';
                        }elseif($second < $bsec*5){
                            $item['num5'] = '';
                            $item['num6'] = '';
                            $item['num7'] = '';
                        }elseif($second < $bsec*6){
                            $item['num6'] = '';
                            $item['num7'] = '';
                        }elseif($second < $bsec*7){
                            $item['num7'] = '';
                        }
                    }
                }
            }
            $vo = [];
            $vo['expect'] = $item['qihao'];
            $vo['openCode'] = $item['num1'].','.$item['num2'].','.$item['num3'].','.$item['num4'].','.$item['num5'].','.$item['num6'].','.$item['num7'];
            $vo['zodiac'] = $this->shengxiao($item['num1']).','.$this->shengxiao($item['num2']).','.$this->shengxiao($item['num3']).','.$this->shengxiao($item['num4']).','.$this->shengxiao($item['num5']).','.$this->shengxiao($item['num6']).','.$this->shengxiao($item['num7']);
            $vo['openTime'] = date('Y-m-d H:i:s', $item['riqi']);
            $vo['wave'] = $this->getnum($item['num1'])['bs'].','.$this->getnum($item['num2'])['bs'].','.$this->getnum($item['num3'])['bs'].','.$this->getnum($item['num4'])['bs'].','.$this->getnum($item['num5'])['bs'].','.$this->getnum($item['num6'])['bs'].','.$this->getnum($item['num7'])['bs'];
            $vo['wuxin'] = $this->getnum($item['num1'])['wx'].','.$this->getnum($item['num2'])['wx'].','.$this->getnum($item['num3'])['wx'].','.$this->getnum($item['num4'])['wx'].','.$this->getnum($item['num5'])['wx'].','.$this->getnum($item['num6'])['wx'].','.$this->getnum($item['num7'])['wx'];
            $return[] = $vo;
        }
        if(!$nextqi){
            $nextqi['riqi'] = $lishi[0]['riqi'];
        }
        echo json_encode(['data'=>$return, 'nexttime'=>$nextqi['riqi'], 'nextqi'=>date('Y-m-d H:i:s', $nextqi['riqi'])], JSON_UNESCAPED_UNICODE);
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

    private function getnum($num){
        //开奖号码处理
        $ball_r = ["01", "02", "07", "08", "12", "13", "18", "19", "23", "24", "29", "30", "34", "35", "40", "45", "46"];
        $ball_b = ["03", "04", "09", "10", "14", "15", "20", "25", "26", "31", "36", "37", "41", "42", "47", "48"];
        $ball_g = ["05", "06", "11", "16", "17", "21", "22", "27", "28", "32", "33", "38", "39", "43", "44", "49"];
        $wuxin_j = ['01', '02', '09', '10', '23', '24', '31', '32', '39', '40'];
        $wuxin_m = ['05', '06', '13', '14', '21', '22', '35', '36', '43', '44'];
        $wuxin_s = ['11', '12', '19', '20', '27', '28', '41', '42', '49'];
        $wuxin_h = ['07', '08', '15', '16', '29', '30', '37', '38', '45', '46'];
        $wuxin_t = ['03', '04', '17', '18', '25', '26', '33', '34', '47', '48'];

        $vo = [];
        if(in_array($num, $ball_r)){
            $vo['bs'] = 'red';
        }elseif(in_array($num, $ball_b)){
            $vo['bs'] = 'blue';
        }elseif(in_array($num, $ball_g)){
            $vo['bs'] = 'green';
        }else{
            $vo['bs'] = '';
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
        }else{
            $vo['wx'] = '';
        }
        return $vo;
    }



    public function amkjls(){
        $ck = 'history_amkjls';
        if(Cache::has($ck)){
            return Cache::get($ck);
        }
        $url = 'https://www.49878.am/app-api/api/v2/lottery/getTopResults?page=1&gameId=90&rows=200&openYear=2023';
        $res = file_get_contents($url);
        $arr = json_decode($res, true);
        if($arr){
            $data = [];
            foreach ($arr['data']['data'] as $item){
                $vo = [];
                $vo['issue'] = $item['turnNum'];
                $vo['openCode'] = $item['openNum'];
                $vo['openTime'] = $item['openTime'];
                $vo['nextTime'] = $item['openTime'];
                $vo['videoUrl'] = '';
                $data[] = $vo;
            }
            $obj = ['code'=>0, 'message'=>'Success', 'data'=>$data];
            $result = 'var historyAO ='. json_encode($obj);
            Cache::set($ck, $result, 60);
            echo $result;
        }
    }

    public function xgkjls(){
        $ck = 'history_xgkjls';
        if(Cache::has($ck)){
            return Cache::get($ck);
        }
        $url = 'https://www.49878.am/app-api/api/v2/lottery/getTopResults?page=1&gameId=70&rows=200&openYear=2023';
        $res = file_get_contents($url);
        $arr = json_decode($res, true);
        if($arr){
            $data = [];
            foreach ($arr['data']['data'] as $item){
                $vo = [];
                $vo['issue'] = $item['turnNum'];
                $vo['openCode'] = $item['openNum'];
                $vo['openTime'] = $item['openTime'];
                $vo['nextTime'] = $item['openTime'];
                $vo['videoUrl'] = '';
                $data[] = $vo;
            }
            $obj = ['code'=>0, 'message'=>'Success', 'data'=>$data];
            $result = 'var historyAO ='. json_encode($obj);
            Cache::set($ck, $result, 60);
            echo $result;
        }
    }


}
