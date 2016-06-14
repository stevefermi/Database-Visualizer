<?php

use caoym\util\Verify;
use caoym\util\exceptions\Forbidden;
use caoym\util\Logger;
use caoym\util\exceptions\NotFound;
use caoym\ezsql\Sql;
use caoym\util\exceptions\BadRequest;

/**
 * 
 * linkes manager
 * @path("/like")
 */

 class Like
 {
    /**
     * create like post
     * @route("POST","/")
     * @param({"userid","$._POST.userid"}) userid,required
     * @param({"postid","$._POST.postid"}) postid,required
     * 
     */
     public function createLike($userid,$postid){
         $pdo=$this->db;
         $pdo->beginTransaction();
         try{
             $lid=Sql::insertInto('like')
             ->values([
                 'userid'=>$userid,
                 'postid'=>$postid
             ])->exec($pdo)->lastInsertId();
             return ['lid'=>$lid,'code'=>1];
         }catch(Exception $e){
             return ['code'=>0,'msg'=>$e->getMessage()];
         }
     }

     /**
      * query post's like
      * @route({"GET","/"})
      * @param({"postid","$._GET.postid") postid,required
      *
      */
      public function queryLike($postid){
          $pdo=$this->db;
          $pdo->beginTransaction();
          $result=array();
          try{
              $uidres=Sql::select('userid')->from('like')
              ->where('postid=?',$postid)
              ->get($pdo);
              foreach($uidres as $value){
                  $ures=Sql::select('username')->from('user')
                  ->where('id=?',$value)
                  ->get($pdo);
                  $uname=$ures['0']['username'];
                  $result[] = $uname;
              }
              return ['result'=>$result,'code'=>1];
          }catch(Exception $e){
             return ['code'=>0,'msg'=>$e->getMessage()];
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