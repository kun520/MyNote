<?php
namespace Common\Controller;
use Think\Controller;

/**
 * 操作表的基类 增 删 改 查
 * 其他操作表的控制器可以继承该控制器 
 */
class DataController extends Controller{
    
    /**
     * 控制器操作的表模型名字 子控制器需要重写该属性
     */
    protected $_model_name = "";
    
    /**
     * lst里每页显示多少记录 默认为5
     */
    protected $_perPage= 5;
    
    /**
     * 显示表单时额外需要的数据
     * @param Model $model 实例化的表模型
     * @return array
     */
    protected function _assign_data($model){
        
        return [];
    }
    
    /**
     * 表记录增加
     * 
     * POST请求是插入数据
     * GET请求是显示新建表单页面
     */
    public function add(){
        
        //实例化$_model_name模型
        $model = D($this->_model_name);
        if(IS_POST){
            
            //接受POST表单数据并验证数据
            if($model->create()){
                //成功返回插入成功的记录的id  
                //失败返回false
                if($id = $model->add()){
                    $this->success("添加成功:新添加记录的id为$id", U('lst'));
                    exit();
                }
            }
            //显示验证失败信息
            $this->error($model->getError());
        }
        else {
            $assign_data = $this->_assign_data($model);
            if($assign_data){                
                $this->assign('cate_data', $assign_data);
            }
            $this->display();
        }
    }
    
    /**
     * 表记录修改
     *
     * POST请求是修改数据
     * GET请求是显示修改表单页面
     */
    public function edit(){
        //实例化$_model_name模型
        $model = D($this->_model_name);
        if(IS_POST){
            //接受数据并保存到实例中,并根据定义的验证规则验证数据
            if($model->create()){
                //成功返回被影响的行数  如果没修改任何字段,被影响的行数是0
                //失败返回false
                if(false !== $model->save()){
                    $this->success("修改成功!", U('lst'));
                    exit();
                }
            }
            //显示验证失败信息
            $this->error($model->getError());
        }
        else {
            
            $assign_data = $this->_assign_data($model);
            if($assign_data){
                $this->assign('cate_data', $assign_data);
            }
            $data = $model->find(I('get.id'));
            $this->assign('data', $data);
            $this->display();
        }
    }
    
    /**
     * 表记录删除
     */
    public function delete(){
        
        $id = (int)I("get.id");
        
        $model = D($this->_model_name);
        $model->delete($id);
        
        //提示信息并跳转到上一个页面
        $this->success('删除成功!');
    }
    
    /**
     * lst方法里搜索功能的where条件  需要在子控制里重写该方法 
     * @RETURN array
     */
    protected function _lst_where(){
        return array();
    }
    
    /**
     * 表记录查询
     * 
     * 功能有搜索,翻页,排序功能
     * 
     * 传入模版的变量有$show记录翻页
     *             $data记录每页的数据
     */
    public function lst(){
        
        //实例化一个表模型对象
        $model = D($this->_model_name);
        /***************搜索功能*******************/        
        $where = $this->_lst_where();
        
        /***************翻页功能*******************/        
        //取出表中该搜索条件的总记录数
        $count = $model->where($where)->count();
        $page = new \Think\Page($count, $this->_perPage);
        
        //生成翻页用的页面按钮
        $show = $page->show();
        $this->assign('show',$show);
        $info['show'] = $show;
        
        /***************查询一页的留言数据*******************/
        $data = $model->where($where)->order('id DESC')->limit($page->firstRow, $page->listRows)->select();
        $this->assign('data',$data);
        
        $this->display();
    }
}
