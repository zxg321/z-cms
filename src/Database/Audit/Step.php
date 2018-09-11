<?php

namespace Zxg321\Zcms\Database\Audit;

use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    protected $table = 'zcms_audit_step';
    protected $fillable = [
        'title', 'audit_id','audit_type','audit_list'
    ];
    public $timestamps = false; // 不自动维护created_at 和 updated_at 字段
    protected $casts = ['audit_list' => 'array',];
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('zcms.db_prefix','zcms_').'audit_step');
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
