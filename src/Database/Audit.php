<?php

namespace Zxg321\Zcms\Database;

use Illuminate\Database\Eloquent\Model;
use Zxg321\Zcms\Database\Audit\User as AuditUser;
use Zxg321\Zcms\Database\Audit\Step as AuditStep;
class Audit extends Model
{
    protected $table = 'zcms_audit';
    
    public static $audit_type=['1' => '仅需一个审核员通过', '2'=> '必须所有审核员通过','3'=>'使用设定审核流程'];
    public static $audit_type1=['1' => '仅需一个审核员通过', '2'=> '必须所有审核员通过'];
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('zcms.db_prefix','zcms_').'audit');
        parent::__construct($attributes);
        //$this->setParentColumn('parent_id');
        //$this->setOrderColumn('sort_order');
        //$this->setTitleColumn('cate_name');
    }
    public function step()
    {
        return $this->hasMany('AuditStep','audit_id','id');
    }
    public function user()
    {
        return $this->hasMany('AuditUser','audit_id','id');
    }
    public static function Start($menu)//开始审核流程
    {
        $row=['st'=>2,'json'=>[]];
        $user=[];
        if($menu->is_a_one==3){//使用设定审核流程
            if(!$audit=Audit::find($menu->audit_id)){
                die('审核流程 (ID:'.$menu->audit_id.') 已经被删除不能进行审核，请重新设置本栏目的审核流程。');
            }
            $step=AuditStep::where('audit_id',$menu->audit_id)->orderBy('id')->get();
            if(count($step)<1){
                $row['st']=1;//没有审核流程，直接通过。
            }
            $user['audit_id']=$menu->audit_id;
            $user['step_json']=$step;

            $step0=$step[0];
            $user['step_id']=$step0->id;
            $user['audit_type']=$step0->audit_type;
            $user['audit_list']=$step0->audit_list;
            $user['title']=AdminAudit::$audit_type[3].'： '.$step0->title;

        }else{
            $user['audit_type']=$menu->is_a_one;
            $user['audit_list']=$menu->audit_list;
            $user['title']=AdminAudit::$audit_type[$menu->is_a_one];
        }
        //$user->save();
        $row['json']=$user;
        return $row;
    }
}
