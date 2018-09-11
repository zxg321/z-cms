<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;

class NetGuestbook extends Model
{
    use ModelTree, AdminBuilder;
    protected $table = 'net_guestbook';
    protected $casts = [
        'userinfo' => 'array',
    ];
}
