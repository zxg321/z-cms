<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\NetCategory;
class NetContent extends Model
{
    protected $table = 'net_content';
    //protected $primaryKey='id';
    protected $casts = [
        'audit_json' => 'json',
    ];
    public function __construct(array $attributes = [])
    {
        //parent::__construct($attributes);

        //$this->setParentColumn('parent_id');
        //$this->setOrderColumn('sort_order');
        //$this->setTitleColumn('cate_name');
    }
    public function category()
    {
        return $this->belongsTo(NetCategory::class);
    }
}
