<?php
namespace app\index\logic;


use think\Cache;
use think\Db;
use think\Env;

class IndexLogic
{


    public static function getBanner(){
        $ck = 'bannerkey';
        if(Cache::has($ck) && !Env::get('app.debug')){
            return Cache::get($ck);
        }
        $data = Db::name('lqz_page'.session('tabexp'))->column('id,name,content', 'id');
        Cache::set($ck, $data);
        return $data;
    }

    public static function daohang(){
        $ck = 'daohang';
        if(Cache::has($ck) && !Env::get('app.debug')){
            return Cache::get($ck);
        }
        $data = Db::name('lqz_page'.session('tabexp'))->where(['name'=>'导航跳转'])->find();
        Cache::set($ck, $data);
        return $data;
    }

    public static function getcontent(){
        $ck = 'getcontent_wuoo';
        if(Cache::has($ck) && !Env::get('app.debug')){
            return Cache::get($ck);
        }
        $data['gst'] = Db::name('lqz_gst'.session('tabexp'))->order('id desc')->field('id,name,qi,zhuozhe')->order('id desc')->limit(16)->select();
        $data['ptyx'] = Db::name('lqz_ptyx'.session('tabexp'))->order('id desc')->order('qi desc')->where(['status'=>1])->select();
        $data['yxym'] = Db::name('lqz_yxym'.session('tabexp'))->order('id desc')->order('qi desc')->where(['status'=>1])->select();
        $data['xjpt'] = Db::name('lqz_xjpt'.session('tabexp'))->order('id desc')->order('qi desc')->where(['status'=>1])->select();
        $data['gst'] = Db::name('lqz_gst'.session('tabexp'))->order('id desc')->order('qi desc')->where(['status'=>1])->select();
        $data['jyt'] = Db::name('lqz_jyt'.session('tabexp'))->order('id desc')->order('qi desc')->where(['status'=>1])->select();
        $data['bxzt'] = Db::name('lqz_bxzt'.session('tabexp'))->order('id desc')->order('qi desc')->where(['status'=>1])->select();
        $data['gjpyjh'] = Db::name('lqz_gjpyjh'.session('tabexp'))->order('id desc')->order('qi desc')->where(['status'=>1])->select();
        $data['yjzt'] = Db::name('lqz_yjzt'.session('tabexp'))->order('id desc')->order('qi desc')->where(['status'=>1])->select();
        $data['amgp'] = Db::name('lqz_amgp'.session('tabexp'))->order('id desc')->order('qi desc')->where(['status'=>1])->select();
        $data['wzsb'] = Db::name('lqz_wzsb'.session('tabexp'))->order('id desc')->order('qi desc')->where(['status'=>1])->select();
        $data['jz36m'] = Db::name('lqz_jz36m'.session('tabexp'))->order('id desc')->order('qi desc')->where(['status'=>1])->select();
        Cache::set($ck, $data);
        return $data;
    }

    public static function getjingyingtie(){
        $ck = 'getjingyingtie';
        if(Cache::has($ck) && !Env::get('app.debug')){
            return Cache::get($ck);
        }
        $data = Db::name('lqz_jyt'.session('tabexp'))->order('id desc')->field('id,name,qi,zhuozhe')->order('id desc')->limit(16)->select();
        Cache::set($ck, $data);
        return $data;
    }

    public static function getgongshibang(){
        $ck = 'getgongshibang';
        if(Cache::has($ck) && !Env::get('app.debug')){
            return Cache::get($ck);
        }
        $data = Db::name('lqz_gsb'.session('tabexp'))->order('id desc')->field('id,name,qi,zhuozhe')->order('id desc')->limit(16)->select();
        Cache::set($ck, $data);
        return $data;
    }


    public static function getzbtuku(){
        $ck = 'getzbtuku';
        if(Cache::has($ck) && !Env::get('app.debug')){
            return Cache::get($ck);
        }
        $data = [];
        if(session('tabexp')==''){
            $turl = [
                ROOT_PATH.'/public/amtkjson/1.json',
                ROOT_PATH.'/public/amtkjson/2.json',
                ROOT_PATH.'/public/amtkjson/3.json',
                ROOT_PATH.'/public/amtkjson/4.json',
                ROOT_PATH.'/public/amtkjson/5.json',
            ];
        }else{
            $turl = [
                ROOT_PATH.'/public/xgtkjson/1.json',
                ROOT_PATH.'/public/xgtkjson/2.json',
                ROOT_PATH.'/public/xgtkjson/3.json',
                ROOT_PATH.'/public/xgtkjson/4.json',
                ROOT_PATH.'/public/xgtkjson/5.json',
            ];
        }
        foreach ($turl as $u){
            $res = file_get_contents($u);
            $arr = json_decode($res, true);
            $data = array_merge($data, $arr['list']);
        }

        $return = [];
        $li = [];
        foreach ($data as $i=>$item){
            if($i%3==0){
                $li = [];
            }
            $li[] = $item;
            if($i%3==2){
                $return[] = $li;
            }
        }
        if(count($data)>0 && count($data)%3!=0){
            $return[] = $li;
        }
        Cache::set($ck, $return);
        return $return;
    }

    public static function getndzhzl(){
        $ck = 'getndzhzl';
        if(Cache::has($ck) && !Env::get('app.debug')){
            return Cache::get($ck);
        }
        $data = Db::name('zhzl'.session('tabexp'))->order('id desc')->field('id,name')->order('id desc')->select();
        $return = [];
        $li = [];
        foreach ($data as $i=>$item){
            if($i%3==0){
                $li = [];
            }
            $li[] = $item;
            if($i%3==2){
                $return[] = $li;
            }
        }
        if(count($data)>0 && count($data)%3!=0){
            $return[] = $li;
        }
        Cache::set($ck, $return);
        return $return;
    }

}