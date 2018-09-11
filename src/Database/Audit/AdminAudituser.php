<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdminAudituser extends Model
{
    protected $table = 'admin_audit_user';
    protected $casts = ['step_json' => 'array','audit_list'=>'array'];
    public function audit()
    {
        return $this->belongsTo('App\Model\AdminAudit','audit_id','id');
    }
}
