<?php
namespace Admin\Controller;

class CategoryController extends \Common\Controller\DataController{
    
    protected $_model_name = "Category";
    protected $_perPage= 50;
    
    protected function _assign_data($model){
        return recursiveCategoryData($model->select());
    }
    
    public function lst(){
        
        //实例化一个表模型对象
        $model = M($this->_model_name);
        /***************搜索功能*******************/
        $where = $this->_lst_where();
        
        
        /***************查询一页的留言数据*******************/
        $data = recursiveCategoryData($model->where($where)->select());
        $this->assign('data',$data);
        
        $this->display();
    }
}