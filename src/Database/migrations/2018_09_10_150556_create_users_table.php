<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('zcms.db_prefix').config('zcms.category'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200)->unique();
            $table->integer('parent_id');
            $table->string('children', 255)->unique();
            $table->tinyInteger('sort_order');
            $table->string('code', 20)->unique();
            $table->tinyInteger('is_url');
            $table->string('url', 200)->unique();
            $table->longText('menu_content');
            $table->tinyInteger('is_nav');
            $table->tinyInteger('st');
            $table->tinyInteger('is_audit');
            $table->tinyInteger('is_a_one');
            $table->json('audit_list');
            $table->integer('audit_id');
            $table->timestamps();
        });
        Schema::create(config('zcms.db_prefix').config('zcms.content'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200)->unique();
            $table->string('title2', 200)->unique();
            $table->integer('category_id');
            $table->string('image', 200)->unique();
            $table->tinyInteger('is_img');
            $table->longText('content');
            $table->text('other_set');
            $table->tinyInteger('is_url');
            $table->string('url', 200)->unique();
            $table->string('author', 50)->unique();
            $table->string('source', 50)->unique();
            $table->integer('sort_order');
            $table->integer('click');
            $table->tinyInteger('st');
            $table->integer('user_id');
            $table->json('audit_json');
            $table->timestamps();
        });

        Schema::create(config('zcms.db_prefix').config('zcms.oauth'), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->tinyInteger('oauth_type');
            $table->string('open_id', 255)->unique();
            $table->json('user_info');
            $table->json('back_info');
            $table->tinyInteger('st');
            $table->timestamps();
        });

        Schema::create(config('zcms.db_prefix').config('zcms.guestbook'), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id');
            $table->string('title', 200)->unique();
            $table->bigInteger('qq');
            $table->longText('content');
            $table->longText('reply');
            $table->integer('zan');
            $table->longText('userinfo');
            $table->tinyInteger('ding');
            $table->string('ip', 20)->unique();
            $table->string('username', 50)->unique();
            $table->tinyInteger('st');
            $table->timestamps();
        });
        Schema::create(config('zcms.db_prefix').config('zcms.links'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200)->unique();
            $table->integer('sort_order');
            $table->string('url', 255)->unique();
            $table->string('img', 255)->unique();
            $table->tinyInteger('type');
            $table->tinyInteger('st');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('zcms.db_prefix').config('zcms.category'));
        Schema::dropIfExists(config('zcms.db_prefix').config('zcms.content'));
        Schema::dropIfExists(config('zcms.db_prefix').config('zcms.oauth'));
        Schema::dropIfExists(config('zcms.db_prefix').config('zcms.guestbook'));
        Schema::dropIfExists(config('zcms.db_prefix').config('zcms.links'));
    }
}
