<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
    namespace Mob\Controller;

use Common\Controller\BaseAuthController;
use Common\Util\VideoUploadUtil;
use Think\Controller;

class DbimportController extends Controller
{
    public function makeArr()
    {
        # code...
        //新表与旧表的
        set_time_limit(0);
        $sql = "SELECT uo.uid uouid,uo.realname uorealname,u.uid,u.realname,u.branch_id from user_old uo JOIN user u on uo.realname = u.realname where uo.uid !=1 ";
        $arr = M()->query($sql);
        // var_dump($arr);
        
        foreach ($arr as $k=>$v) {
            if ($v['uouid'] != $v['uid']) {
                $note = M('review_old')->where(array('uid'=>$v['uouid']))->select();
                     
                foreach ($note as $nk=>$nv) {
                   
                   
                        M('review_change_log')->add(array('old_uid'=>$v['uouid'],'new_uid'=>$v['uid'],'note_id'=>$nv['id']));
                    
                }
                echo 'insert success:'.$k;
								echo '<br />';
						
									M('review_old')->where(array('uid'=>$v['uouid']))->save(array('uid'=>$v['uid'],'branch_id'=>$v['branch_id']));
									echo 'update num:'.$k;
									echo '<br />';
								
              
            }
            //改旧notes的uid和branch_id
            //$notes_arr = M('notes_old')->where(array('uid'=>$v['uouid'],'status'=>0))->select();
        }
    }
    public function importSql($arr=array())
    {
        # code...
        $str = "INSERT INTO `notes` (`uid`,`title`,`content`,`create_time`,`status`,`type`,`study_time`,`branch_id`,`review_id`,`article_id`,`address`) VALUES ";
        //	$arr = array(0=>array('uid'=>10477,'title'=>'caseaea','content'=>'dasdsd','create_time'=>1553759388,'status'=>0,'type'=>0,'study_time'=>0.5,'branch_id'=>319,'review_id'=>1,'address'=>'珠海妇幼'));
        foreach ($arr as $k=>$v) {
            $str = $str."(".$v['uid'].",'".$v['title']."','".$v['content']."',".$v['create_time'].",".$v['status'].",".$v['type'].",".$v['study_time'].",".$v['branch_id'].",".$v['review_id'].",".$v['article_id'].",'".$v['address']."'),";
            // "(10477,'ce','dasdsd',1553759388,0,0,0.5,319,1,0,NULL,NULL,NULL,'珠海妇幼')"
        }
        //删除字符串最后一个逗号
        file_put_contents('./xxx.sql', $str);
    }
}
