<?php

use caoym\util\Verify;
use caoym\util\exceptions\Forbidden;
use caoym\util\Logger;
use caoym\util\exceptions\NotFound;
use caoym\ezsql\Sql;
use caoym\util\exceptions\BadRequest;

/**
 * 
 * posts manager
 * @path("/post")
 */
class Posts
{
    /**
     * query post
     * @route({"GET","/"})
     */
     public function queryPosts()
     {
         $pdo = $this->db;
         $pdo->beginTransaction();
         try {
             $res=Sql::select('*')->from('post')->get($pdo);
             return ['result'=>$res,'code'=>1];
         }catch(Exception $e){
             $pdo->rollBack();
             return ['code'=>0,'msg'=>$e->getMessage()];
         }
     }
     /**
       *
       * create posts
       * @route({"POST","/"})
       * @param({"userid","$._POST.userid"}) userid,required
       * @param({"charttype","$._POST.charttype"}) charttype,required
       * @param({"settings","$._POST.settings"}) settings,required
       * @param({"did","$._POST.did"}) did,requried
       * @param({"tablename","$._POST.tablename"}) tablename,required
       * @param({"title","$._POST.title"}) title,required
       */
     public function addPosts($userid,$charttype,$settings,$did,$tablename,$title)
     {
        $pdo = $this->db;
        $pdo->beginTransaction();
        try{
            $pid = Sql::insertInto('post')->values([
                'publisherid'=>$userid,
                'chart_type'=>$charttype,
                'settings'=>$settings,
                'did'=>$did,
                'tablename'=>$tablename,
                'title'=>$title,
                'pub_date'=>date("Y-m-d h:i:sa")
            ])->exec($pdo)->lastInsertId();

            $pdo->commit();
            return ['code'=>1,'pid'=>$pid];
        } catch(Exception $e){
            $pdo->rollBack();
            return [
                'code'=>0,
                'msg'=>$e->getMessage()
                ];
        }
     }
    /** @inject("ioc_factory") */
    private $factory;
    /**
     * @property({"default":"@db"})  
     * @var PDO
     */
    public $db;
    public $oss;
    
}