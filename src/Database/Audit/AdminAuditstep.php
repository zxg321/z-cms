<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdminAuditstep extends Model
{
    protected $table = 'admin_audit_step';
    protected $fillable = [
        'title', 'audit_id','audit_type','audit_list'
    ];
    public $timestamps = false; // 不自动维护created_at 和 updated_at 字段
    protected $casts = ['audit_list' => 'array',];
    public function audit()
    {
        return $this->belongsTo('App\Model\AdminAudit','audit_id','id');
    }
}
