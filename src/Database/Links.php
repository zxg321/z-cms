<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use Encore\Admin\Traits\AdminBuilder;
//use Encore\Admin\Traits\ModelTree;

class NetLinks extends Model
{
    //use ModelTree, AdminBuilder;
    protected $table = 'net_links';
    public $timestamps = false;
    /*
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        //$this->setParentColumn('parent_id');
        //$this->setOrderColumn('sort_order');
        //$this->setTitleColumn('cate_name');
    }*/
}
