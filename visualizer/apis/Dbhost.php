<?php
use caoym\util\Verify;
use caoym\util\exceptions\Forbidden;
use caoym\util\Logger;
use caoym\util\exceptions\NotFound;
use caoym\ezsql\Sql;
use caoym\util\exceptions\BadRequest;

/**
 * @author Xiaozhe Yao
 */
class UnableToConnect extends \Exception
{
}

/**
 * 
 * dbhost manager
 * This file is used to manage DB HOST for each user. including create, delete and modify.
 * @path("/dbhost")
 */
 
class Dbhost
{
    /**
     * get all my dbhost
     * @route({"GET","/"})
     * @param({"userid","$._GET.uid"}) userid,required
     * 
     */
     public function queryDB($userid)
     {
         $pdo=$this->db;
         $pdo->beginTransaction();
         try{
             $res=Sql::select('id','host',
             'dbname','dbusername')
             ->from('dbhost')
             ->get($pdo);
             return ['code'=>1,'result'=>$res];
         }catch(PDOException $e){
             return ['code'=>0,'message'=>$e->getMessage()];
         }
     }
    /**
     * create dbhost
     * @route({"POST","/"})
     * @param({"userid","$._POST.uid"}) user_id,required
     * @param({"host","$._POST.host"}) host,required
     * @param({"username","$._POST.username"}) database_username,required
     * @param({"password","$._POST.password"}) database_password,required
     * @param({"dbname","$._POST.dbname"}) dbname,required
     * @param({"dbport","$._POST.dbport"}) dbport,required
     *
     * @throws({"UnableToConnect","res","3306 Unable to Connect",{"error","Unable to Connect"}}) unable to connect database
     */
     public function createDBHost($userid,$host,$username,$password,$dbname,$dbport){
         $pdo=$this->db;
         $pdo->beginTransaction();
         try{
             $res=Sql::select('id')->from('dbhost')->where(
                 'host= ? and dbusername= ? and dbpassword= ? and dbname=? and dbport=?',$host,$username,$password,$dbname,$dbport
             )->forUpdate()->get($pdo);
             if(count($res)!=0){
                 return ['msg'=>'Database existed.','code'=>0];
             }
             else{
                try{
                    $pdostring="mysql:host=".$host.";port=".$dbport.";dbname=".$dbname;
                    $userdb=new PDO($pdostring,$username,$password);
                }
                catch(PDOException $e)
                {
                    return ['msg'=>$e->getMessage(),'code'=>0];
                }
                 $did=Sql::insertInto('dbhost')->values(['host'=>$host,
                    'dbusername'=>$username,'dbpassword'=>$password,'dbname'=>$dbname,'hostuserid'=>$userid,'dbport'=>$dbport
                 ])->exec($pdo)->lastInsertId();
                 $pdo->commit();
             }
         } catch (Exception $e){
             $pdo->rollBack();
             throw $e;
         }
         return ['msg'=>'Database Created.','code'=>1,'dbid'=>$did];
     }
    /** @inject("ioc_factory") */

/**
 * Show all tables in a database using did
 * @route({"POST","/querytables"})
 * @param({"did","$._POST.did"}) databse id,required
 * @param({"userid","$._POST.userid"}) userid,required
 */
    function queryTables($did,$userid)
    {
        $pdo = $this->db;
        $pdo->beginTransaction();
        try{
            $res=Sql::select('host',
            'dbusername',
            'dbpassword',
            'dbname',
            'hostuserid',
            'dbport')
            ->from('dbhost')
            ->where('id=?',$did)
            ->get($pdo);
            if(count($res)==0){
                return ['msg'=>'Database not Existed','code'=>0];
            }
            if($res[0]['hostuserid']!=$userid){
                return ['msg'=>'User Not Authorized','code'=>0];
            }
            $pdostring="mysql:host=".$res[0]['host'].";port=".$res[0]['dbport'].";dbname=".$res[0]['dbname'];
            $userdb = new \PDO($pdostring, $res[0]['dbusername'], $res[0]['dbpassword']);
            $tables=Sql::select('TABLE_NAME',
                'TABLE_ROWS')
                ->from('INFORMATION_SCHEMA.TABLES')
                ->where('TABLE_SCHEMA=?',$res[0]['dbname'])
                ->get($userdb);
            return ['result'=>$tables,'code'=>1];
        }
        catch (Exception $e){
            return ['msg'=>$e->getMessage(),'code'=>0];
        }
    }
/**
 * Show all tables in a database using did
 * @route({"POST","/querycolumn"})
 * @param({"did","$._POST.did"}) databse id,required
 * @param({"userid","$._POST.userid"}) userid,required
 * @param({"tablename","$._POST.tablename"}) tablename,required
 */
    function queryColumn($did,$tablename,$userid)
    {
        $pdo = $this->db;
        $pdo->beginTransaction();
        try{
            $res=Sql::select('host',
            'dbusername',
            'dbpassword',
            'dbname',
            'hostuserid')
            ->from('dbhost')
            ->where('id=?',$did)
            ->get($pdo);
            if(count($res)==0){
                return ['msg'=>'Database not Existed','code'=>0];
            }
            if($res[0]['hostuserid']!=$userid){
                return ['msg'=>'User Not Authorized','code'=>0];
            }
            $pdostring="mysql:host=".$res[0]['host'].";port=".$res[0]['dbport'].";dbname=".$res[0]['dbname'];
            $userdb = new \PDO($pdostring, $res[0]['dbusername'], $res[0]['dbpassword']);
            $columns=Sql::select('COLUMN_NAME',
                'DATA_TYPE')
                ->from('INFORMATION_SCHEMA.COLUMNS')
                ->where('TABLE_name=?',$tablename)
                ->get($userdb);
            return ['result'=>$columns,'code'=>1];
        }
        catch (Exception $e){
            return ['msg'=>$e->getMessage(),'code'=>0];
        }
    }
 
/**
 * Connect an existing database. and query data. Generating json format.
 * @route({"POST","/querydata"})
 * @param({"did","$._POST.did"}) Database id, required
 * @param({"tablename","$._POST.tablename"}) Table name,required
 * @param({"userid","$._POST.userid"}) userid, required
 */
    function queryData($did,$userid,$tablename){
        $pdo = $this->db;
        $pdo->beginTransaction();
        try{
            $res=Sql::select('host',
            'dbusername',
            'dbpassword',
            'dbname',
            'hostuserid')
            ->from('dbhost')
            ->where('id=?',$did)
            ->get($pdo);
            if(count($res)==0){
                return ['msg'=>'Database not Existed','code'=>0];
            }
            if($res[0]['hostuserid']!=$userid){
                return ['msg'=>'User Not Authorized','code'=>0];
            }
            $pdostring="mysql:host=".$res[0]['host'].";port=".$res[0]['dbport'].";dbname=".$res[0]['dbname'];
            $userdb = new \PDO($pdostring, $res[0]['dbusername'], $res[0]['dbpassword']);
            $data=Sql::select('*')
                ->from($tablename)
                ->get($userdb);
            return ['result'=>$data,'code'=>1];
        }
        catch (Exception $e){
            return ['msg'=>$e->getMessage(),'code'=>0];
        }
    }

     private $factory;
    /**
     * @property({"default":"@db"})  
     * @var PDO
     */
    public $db;
    public $oss;
}