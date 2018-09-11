<?php

namespace Zxg321\Zcms\Database\Audit;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'admin_audit_user';
    protected $casts = ['step_json' => 'array','audit_list'=>'array'];
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('zcms.db_prefix','zcms_').'audit_user');
        parent::__construct($attributes);
        //$this->setParentColumn('parent_id');
        //$this->setOrderColumn('sort_order');
        //$this->setTitleColumn('cate_name');
    }
    public function audit()
    {
        return $this->belongsTo(\Zxg321\Zcms\Database\Audit::class,'audit_id','id');
    }
}
