<?php

namespace Zxg321\Zcms\Controllers;

use Zxg321\Zcms\Database\Content as NetContent;
use Zxg321\Zcms\Database\Category as NetCategory;
use Zxg321\Zcms\Database\Audit as AdminAudit;
use Illuminate\Http\Request;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ContentController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index($mid=0)
    {   
        
        return Admin::content(function (Content $content) use ($mid) {

            $content->header('新闻内容');
            $content->description('');
            if(!$mid)$mid = @$_GET['id'];
            if($mid>0 or $mid=='all')$content->body($this->grid($mid));

            else $content->body(NetCategory::tree(function ($tree) {
                //$tree->isList();
                $tree->isList = true;
                $tree->useAll = true;
                $tree->disableCreate();
                $tree->disableSave();
                $tree->disableRefresh();
                $tree->setView(['tree'   => 'zcms::tree','branch' => 'zcms::tree.list',]);
                $tree->branch(function ($branch) {
                    //$src = config('admin.upload.host') . '/' . $branch['logo'] ;
                    //$logo = "<img src='$src' style='max-width:30px;max-height:30px' class='img'/>";
                    $code='content';
                    if($branch['code'])$code=$branch['code'];
                    $codes=NetCategory::$codes;
                    $coden=$codes[$code];
                    $countInfo=0;
                    $countInfo=NetContent::where('category_id',$branch['id'])->count();
                    return "<a href='/admin/zcms/content?id={$branch['id']}'>{$branch['id']} - {$branch['title']} [$coden] （共 $countInfo 条）".($branch['st']?'':'[关闭]')." ".($branch['is_url']?'[外链]':'')."</a>";
                });
                $tree->query(function ($model) {
                    return $model->whereIn('code', ['news','newsindex','newslist']);
                });
                
                //dd($tree->getItems());
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

            $content->header('新闻内容');
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

            $content->header('新闻内容');
            $content->description('');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid($mid=0)
    {
        
        return Admin::grid(NetContent::class, function (Grid $grid) use ($mid) {
            
            $grid->model()->orderBy('id', 'desc');
            $grid->id('序号')->sortable();
            $grid->column('title','标题')->editable();
            $grid->column('category_id','栏目')->editable('select',NetCategory::selectOptions());
            $grid->column('image','图片')->display(function ($image) {
                if($image){
                    if($this->is_img)return "<i class=\"fa fa-home\" title=\"首页显示\"><a class='thumbnail' title='首页显示' style='max-width:100px;max-height:100px;'><img src='/upload/$image' ></a></i>";
                    else return "<div class='thumbnail' style='max-width:100px;max-height:100px;'><img src='/upload/$image' ></div>";
                }
                else return '[无图片]';
            
            });
            if(config('audit.switch','off')=="on"){//审核
                $grid->column('st','状态')->display(function ($st) {
                    if($st==0){
                        return '<a href="#" class="btn btn-default">不显示</a>';
                    }elseif($st==1){
                        return '<a href="#" class="btn btn-primary">显示</a>';
                    }elseif($st==2){
                        return '<a href="#" class="btn btn-danger">审核</a>';
                    }
                });
            }else{
                $grid->column('st','状态')->switch();
            }
            $grid->created_at('添加时间');
            $grid->updated_at('更新时间');
            if($mid>0) $grid->model()->where('category_id', $mid);

            $grid->filter(function($filter){

                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            
                // 在这里添加字段过滤器
                $filter->like('title', '标题');
                $filter->date('created_at','添加时间');
                $filter->date('updated_at','更新时间');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        
        return Admin::form(NetContent::class, function (Form $form) {
            //保存前回调
            $form->saving(function (Form $form) {
                $form->model()->user_id=auth('admin')->user()->id;
                if(config('audit.switch','off')=="on"){//审核
                    
                    $menu=NetCategory::find($form->category_id);
                    
                    if(@$menu->is_audit==1){
                        $audit=AdminAudit::Start($menu);
                        $form->model()->st=$audit['st'];
                        $form->model()->audit_json=$audit['json'];
                    }
                }else{
                    $form->model()->st=1;
                }
                //dd($form);
                //$form->user_id=111;
                //$_POST['user_id']=112;
                //return $form;
                //dump($form);die();
            });

            $form->tab('基础内容设置', function ($form) {
                $id = @$_GET['id'];
                $form->select('category_id', '栏目')->options(NetCategory::selectOptions())->default($id);
                $form->text('title', '标题');
                $form->editor('content', '内容');
                $form->image('image','图片')->uniqueName();
                $form->switch('is_img', '图片首页显示');
            })->tab('其他信息', function ($form) {
                $form->display('id', '序号');
                $form->text('title2', '副标题');
                $form->text('author', '作者');
                $form->text('source', '来源');
                
                $form->display('created_at', '添加时间');
                $form->display('updated_at', '更新时间');
            });
        });
    }
}
