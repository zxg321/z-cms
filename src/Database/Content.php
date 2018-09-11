<?php

namespace Zxg321\Zcms\Database;

use Illuminate\Database\Eloquent\Model;
class Content extends Model
{
    protected $table = 'zcms_content';
    protected $casts = [
        'audit_json' => 'json',
    ];
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('zcms.db_prefix','zcms_').'content');
        parent::__construct($attributes);
        //$this->setParentColumn('parent_id');
        //$this->setOrderColumn('sort_order');
        //$this->setTitleColumn('cate_name');
    }
    public function category()
    {
        return $this->belongsTo(\Zxg321\Zcms\Database\Category::class);
    }
}
