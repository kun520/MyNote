<?php

namespace Admin\Model;
use Think\Model;

class CategoryModel extends Model{
    protected $_validate = array(
        ['cate_name', 'require', '分类名称不能为空!', 1],
        ['cate_name', '', '分类名称已存在!', 1, 'unique'],
    );
    
    
}
