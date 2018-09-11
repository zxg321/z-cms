<?php

namespace Zxg321\Zcms\Controllers;

use Zxg321\Zcms\Database\Category as NetCategory;
use Zxg321\Zcms\Database\Audit as AdminAudit;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Auth\Database\Menu;
use Illuminate\Support\Facades\Cache;
use Encore\Admin\Auth\Database\Administrator;
class CategoryController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    //protected $code=['index'=>'网站首页','newsindex'=>'新闻首页','newslist'=>'新闻列表','content'=>'内容显示'];
    public function index()
    {
        $this->setChildrenMenu();
        return Admin::content(function (Content $content) {

            $content->header('导航菜单');
            $content->description('');

            $content->body(NetCategory::tree(function ($tree) {
                $tree->branch(function ($branch) {
                    //$src = config('admin.upload.host') . '/' . $branch['logo'] ;
                    //$logo = "<img src='$src' style='max-width:30px;max-height:30px' class='img'/>";
                    $code='content';
                    if($branch['code'])$code=$branch['code'];
                    $codes=NetCategory::$codes;
                    $coden=$codes[$code];
                    $nav=$branch['is_nav']?'<i class="fa fa-bars" title="导航显示"></i>':'';

                    return "{$branch['id']} - {$branch['title']} [$coden] $nav ".($branch['st']?'':'[关闭]')." ".($branch['is_url']?'[外链]':'');
                });
            }));//$this->grid()
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('导航菜单');
            $content->description('');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('导航菜单');
            $content->description('');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(NetCategory::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->created_at('建立时间');
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function setChildrenMenu($parent_id=0,$p_menu_id=0){
        //die();
        //设置子查询
        $childrenText=[];
        if($parent_id==0){
            Menu::where('menu_type',1)->update(['menu_type'=>2]);//类别改变 可能有些菜单被删除掉了 调整
        }
        //return ;
        $category=NetCategory::where('parent_id',$parent_id)->get();
        if(!$p_menu=Menu::where([['menu_type',2],['menu_id',$parent_id]])->first()){//批量导入菜单，可能没有生成父菜单
            if($parent_id==0){
                $menu_name=config('zcms.cms_root_name','网站内容');
            }else{
                $menu_name='Temp_Dir';
            }
            $p_menu=new Menu();
            $p_menu->menu_type=2;
            $p_menu->title=$menu_name;
            $p_menu->menu_id=$parent_id;
            $p_menu->icon='fa-chrome';
            $p_menu->save();
        }
        foreach ($category as $key0 => $value0) {
            array_merge($childrenText,$this->setChildrenMenu($value0->id,$p_menu->id));
            array_push($childrenText,$value0->id);
        }
        if($parent_id!=0){
            $mymenu=NetCategory::find($parent_id);
            if(count($childrenText)>0){
                $mymenu->children_list=implode(',',$childrenText);
            }else{
                $mymenu->children_list='';
            }
            $mymenu->save();
            if(!$c_menu=Menu::where([['menu_type',2],['menu_id',$parent_id]])->first()){
                $c_menu=new Menu();
            }
            $c_menu->title=$mymenu->title;
            $c_menu->menu_type=1;
            $c_menu->menu_id=$mymenu->id;
            $c_menu->order=$mymenu->sort_order?$mymenu->sort_order:$mymenu->id;
            $c_menu->parent_id=$p_menu_id;
            $code=$mymenu->code;
            if(in_array($code,['news','newsindex','newslist'])){
                $c_menu->uri='zcms/content?id='.$mymenu->id;
                $c_menu->icon='fa-newspaper-o';
                $c_menu->save();
            }elseif($code=='content'){
                $c_menu->uri='zcms/category/'.$mymenu->id.'/edit';
                $c_menu->icon='fa-compass';
                $c_menu->save();
            }elseif($code=='guestbook'){
                $c_menu->uri='zcms/guestbook?pid='.$mymenu->id;
                $c_menu->icon='fa-at';
                $c_menu->save();
            }elseif($code=='link'){
                //$c_menu->uri='category/'.$mymenu->id.'/edit';
                //$c_menu->icon='fa-chrome';
                //$c_menu->save();
            }
        }else{
            Menu::where([['menu_type',2],['menu_id',0]])->update(['menu_type'=>1]);
            Menu::where('menu_type',2)->delete();
        }
        return $childrenText;
    }
    protected function form()
    {
        Cache::forget('main_menu');
        //$this->setChildrenMenu();
        return Admin::form(NetCategory::class, function (Form $form) {
            // $form->saved(function (Form $form) {
                
            //     $menu_mod=new Menu();
            //     if($menu=$menu_mod->where([['menu_type',1],['menu_id',0]])->first()){
            //         //设置左边栏目
            //         //dd($form->model());
            //         if($form->model()->id>0){
            //             if(!$c_menu=$menu_mod->where([['parent_id',$menu->id],['menu_type',1],['menu_id',$form->model()->id]])->first()){
            //                 $c_menu=new Menu();
            //             }
            //             $c_menu->title=$form->model()->title;
            //             $c_menu->menu_type=1;
            //             $c_menu->menu_id=$form->model()->id;
            //             $c_menu->order=$form->model()->sort_order?$form->model()->sort_order:$form->model()->id;
                        
            //             $c_menu->parent_id=$menu->id;
            //             $parent_id=$form->model()->parent_id;
            //             if($parent_id>0 && $p_menu=$menu_mod->where([['parent_id',$menu->id],['menu_type',1],['menu_id',$parent_id]])->first()){
            //                 $c_menu->parent_id=$p_menu->id;
            //             }
                        
            //             $code=$form->model()->code;
            //             if(in_array($code,['news','newsindex','newslist'])){
            //                 $c_menu->uri='content?id='.$form->model()->id;
            //                 $c_menu->icon='fa-newspaper-o';
            //                 $c_menu->save();
            //             }elseif($code=='content'){
            //                 $c_menu->uri='category/'.$form->model()->id.'/edit';
            //                 $c_menu->icon='fa-compass';
            //                 $c_menu->save();
            //             }elseif($code=='guestbook'){
            //                 $c_menu->uri='guestbook?pid='.$form->model()->id;
            //                 $c_menu->icon='fa-at';
            //                 $c_menu->save();
            //             }elseif($code=='link'){
            //                 //$c_menu->uri='category/'.$form->model()->id.'/edit';
            //                 //$c_menu->icon='fa-chrome';
            //                 //$c_menu->save();
            //             }
                        
                        
            //             //dd($_POST);
            //         }else{
            //             //$_POST['_order']
            //             //dd($_POST);
            //             $_order=json_decode($_POST['_order']);
            //             function set_menu($_order,$p_id){
            //                 $menu_mod=new Menu();
            //                 foreach ($_order as $key => $value) {
            //                     if(!$c_menu=$menu_mod->where([['menu_type',1],['menu_id',$value->id]])->first()){
            //                         $c_menu=new Menu();
            //                     }
            //                     $category=NetCategory::find($value->id);
            //                     $c_menu->title=$category->title;
            //                     $code=$category->code;
            //                     $menu_add=false;
            //                     if(in_array($code,['news','newsindex','newslist'])){
            //                         $c_menu->uri='content?id='.$category->id;
            //                         $c_menu->icon='fa-newspaper-o';
            //                         $menu_add=true;
            //                     }elseif($code=='content'){
            //                         $c_menu->uri='category/'.$category->id.'/edit';
            //                         $c_menu->icon='fa-compass';
            //                         $menu_add=true;
            //                     }elseif($code=='guestbook'){
            //                         $c_menu->uri='guestbook?pid='.$category->id;
            //                         $c_menu->icon='fa-at';
            //                         $menu_add=true;
            //                     }elseif($code=='link'){
            //                         //$c_menu->uri='category/'.$category->id.'/edit';
            //                         //$c_menu->icon='fa-chrome';
            //                     }
            //                     if($menu_add){
            //                         $c_menu->menu_type=1;
            //                         $c_menu->menu_id=$category->id;
            //                         $c_menu->order=$category->sort_order?$category->sort_order:$category->id;
                                    
            //                         $c_menu->parent_id=$p_id;
                                    
            //                         /*
            //                         $parent_id=$category->parent_id;
            //                         if($parent_id>0 && $p_menu=$menu_mod->where([['parent_id',$p_id],['menu_type',1],['menu_id',$parent_id]])->first()){
            //                             $c_menu->parent_id=$p_menu->id;
            //                         }*/
                                    
            //                         $c_menu->save();
            //                         if(@count($value->children)>0){
            //                             //$p_menu=$menu_mod->where([['parent_id',$menu->id],['menu_type',1],['menu_id',$parent_id]])->first();
            //                             set_menu($value->children,$c_menu->id);
            //                         }
            //                     }
            //                     //echo " ".$c_menu->id." $p_id\n";
                                
            //                 }
            //             }
            //             set_menu($_order,$menu->id);
            //             //dd($_order);
            //         }
            //     }
            //     //return redirect('/admin/category?r='.rand());
            //     //die();
                
            // });

            $form->tab('基础菜单设置', function ($form) {
                $form->display('id', '序号');
            
                $form->select('parent_id', '上级菜单')->options(NetCategory::selectOptions());
                $form->text('title', '标题');
                $form->select('code', '使用模版')->options(NetCategory::$codes);
                
                $form->switch('is_nav', '导航栏显示');
                
            })->tab('连接设置', function ($form) {
                $form->switch('is_url', '连接开关');
                $form->text('url', '连接地址');
            })->tab('内容显示信息', function ($form) {
                $form->editor('menu_content', '内容显示')->help('使用模版选择【内容显示】的时候显示内容。');
            });
            if(config('audit.switch','off')=="on"){
                $form->tab('内容审核', function ($form) {
                    $form->switch('is_audit', '打开内容审核');
                    $form->radio('is_a_one','审核类别')->options(NetCategory::$audit_type)->default('1')->help('审核通过的条件');
                    $form->multipleSelect('audit_list', '审核员列表')->options(Administrator::all()->pluck('username', 'id'));

                    $form->select('audit_id','审核步骤')->options(AdminAudit::all()->pluck('title', 'id'))->help('<a href="/admin/audit">审核步骤编辑在【管理权限设置】->【审核】</a>');
                    /*
                    $form->html("
                    <script>
                    jQuery(function() {
                        $('.dd').nestable();
                    });
                    </script>");*/
                });
            }
            $form->tab('其他信息', function ($form) {
                $form->display('created_at', '建立时间');
                $form->display('updated_at', '更新时间');
            

                $form->html("
                    <script>
                    $(document).ready(function(){
                        var is_a_one_value=1;
                        function is_audit_change(){
                            var va=$(\"input[name='is_audit']\").val();
                            if(va=='on'){
                                $('#form_div_is_a_one').show();
                                is_a_one_value=$(\"input[name='is_a_one']:checked\").val();
                                
                                if(is_a_one_value=='3'){
                                    $('#form_div_audit_list').hide();
                                    $('#form_div_audit_id').show();
                                }else{
                                    $('#form_div_audit_list').show();
                                    $('#form_div_audit_id').hide();
                                }
                            }else{
                                $('#form_div_is_a_one').hide();
                                $('#form_div_audit_list').hide();
                                $('#form_div_audit_id').hide();
                            }

                        }
                        $(\"input[name='is_audit']\").on(\"change\",function(e){
                            is_audit_change();
                        });
                        $(\"input[name='is_a_one']\").on(\"ifChecked\",function(e){
                            is_audit_change()
                        });
                        is_audit_change();

                        function is_url_change(){
                            var va=$(\"input[name='is_url']\").val();
                            if(va=='on'){
                                $('#table_li_form-3').hide();
                                $('#table_li_form-4').hide();
                                $('#form_div_url').show();
                                
                            }else{
                                $('#table_li_form-3').show();
                                $('#table_li_form-4').show();
                                $('#form_div_url').hide();
                            }
                        }
                        $(\"input[name='is_url']\").on(\"change\",function(e){
                            is_url_change();
                        });
                        is_url_change();
                    });
                    </script>
                ");
            });


        });
    }
}
