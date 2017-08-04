<?php
namespace Home\Model;
use Think\Model;

class NoteModel extends Model{
    
    protected $_validate = array(
        array('title','require','标题不能为空',1),
        array('content','require','内容不能为空',1),
        //array('captcha','require','验证码错误',1,'confirm'),
    );
    
    //修改
    public function save(){
        
        /********************上传图片*********************/
        $upload = new \Think\Upload();
        $upload->maxSize = 2*1024*1024 ;// 设置附件上传大小  单位:字节  2M
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Public/Uploads/'; // 设置附件上传根目录 --> 需手动建立目录
        $upload->savePath = 'Note/'; // 设置附件上传（子）目录 --> 程序自动建立
                
        // 上传文件
        $imgInfo= $upload->upload();
        if($imgInfo) {
            // 上传成功  拼接图片地址路径
            $imgPath = $imgInfo["image"]["savepath"] . $imgInfo["image"]["savename"];
            $imgPath_big = $imgInfo["image"]["savepath"] . 'big_' . $imgInfo["image"]["savename"];
            $imgPath_mid = $imgInfo["image"]["savepath"] . 'mid_'. $imgInfo["image"]["savename"];
            $imgPath_sm = $imgInfo["image"]["savepath"] . 'sm_'. $imgInfo["image"]["savename"];
            
            /**********************根据上传图片生成3张缩略图************************/
            $image= new \Think\Image();
            $image->open($upload->rootPath . $imgPath);
            $image->thumb(500, 500)->save($upload->rootPath . $imgPath_big);
            $image->thumb(300, 300)->save($upload->rootPath . $imgPath_mid);
            $image->thumb(100, 100)->save($upload->rootPath . $imgPath_sm);
            
            $this->img_path = $imgPath;
            $this->img_path_big = $imgPath_big;
            $this->img_path_mid = $imgPath_mid;
            $this->img_path_sm = $imgPath_sm;
            /*******************删除原图片**********************/
            unlink('./Public/Uploads/'.$this->img_path1);
            unlink('./Public/Uploads/'.$this->img_path_big1);
            unlink('./Public/Uploads/'.$this->img_path_mid1);
            unlink('./Public/Uploads/'.$this->img_path_sm1);
        }
        else{
            // 上传错误提示错误信息
            $error = $upload->getError();
            //可以不上传图片,其他的错误就抛出异常
            if($error != "没有文件被上传！"){
                $this->error = $error;
                return;
            }
        }
        
        //赋值一些其他的字段
        $this->ip = get_client_ip(1);
        
        return parent::save();
    }
    
    //添加
    public function add(){
        /********************上传图片*********************/
        $upload = new \Think\Upload();
        $upload->maxSize = 2*1024*1024 ;// 设置附件上传大小  单位:字节  2M
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Public/Uploads/'; // 设置附件上传根目录 --> 需手动建立目录
        $upload->savePath = 'Note/'; // 设置附件上传（子）目录 --> 程序自动建立
                
        // 上传文件
        $imgInfo= $upload->upload();
        if($imgInfo) {
            // 上传成功  拼接图片地址路径
            $imgPath = $imgInfo["image"]["savepath"] . $imgInfo["image"]["savename"];
            $imgPath_big = $imgInfo["image"]["savepath"] . 'big_' . $imgInfo["image"]["savename"];
            $imgPath_mid = $imgInfo["image"]["savepath"] . 'mid_'. $imgInfo["image"]["savename"];
            $imgPath_sm = $imgInfo["image"]["savepath"] . 'sm_'. $imgInfo["image"]["savename"];
            
            /**********************根据上传图片生成3张缩略图************************/
            $image= new \Think\Image();
            $image->open($upload->rootPath . $imgPath);
            $image->thumb(500, 500)->save($upload->rootPath . $imgPath_big);
            $image->thumb(300, 300)->save($upload->rootPath . $imgPath_mid);
            $image->thumb(100, 100)->save($upload->rootPath . $imgPath_sm);
            //把图片的路径存入该实例的属性里(新建的)
            $this->img_path = $imgPath;
            $this->img_path_big = $imgPath_big;
            $this->img_path_mid = $imgPath_mid;
            $this->img_path_sm = $imgPath_sm;
        }
        else{
            // 上传错误提示错误信息
            $error = $upload->getError();
            //可以不上传图片
            if($error != "没有文件被上传！"){
                $this->error = $error;
                return;
            }
        }
        //赋值一些其他的字段
        $this->addtime= date('Y-m-d H:i:s');
        $this->ip = get_client_ip(1);
        
        //调用父类的add方法插入数据
        return parent::add();
    }
    
    //删除一条数据
    public function delete(){
        
        $id = I('get.id');
        
        //查询该记录,取一些信息
        $info = $this->field("img_path,img_path_big,img_path_mid,img_path_sm")
                    ->where([
                        'id' => $id,
                    ])
                    ->find();
                        
        if($info){
            //如果查到记录就删除硬盘上的图片
            unlink('./Public/Uploads/'.$info['img_path']);
            unlink('./Public/Uploads/'.$info['img_path_big']);
            unlink('./Public/Uploads/'.$info['img_path_mid']);
            unlink('./Public/Uploads/'.$info['img_path_sm']);
            
            //在从数据库删除数据
            $this->where([
                            'id' => $id,
                        ]);
            return parent::delete();
        }
        else {
            //返回-1代表数据库没这个记录
            return -1;
        }
    }
    
    //删除多条记录
    public function pldelete(){
        foreach (I('post.selId') as $id){
            //先删除记录的图片
            $info = $this->field("img_path,img_path_big,img_path_mid,img_path_sm")
                        ->where([
                                    'id' => $id,
                                ])
                        ->find();
            
            if($info){
                //如果查到记录就删除硬盘上的图片
                unlink('./Public/Uploads/'.$info['img_path']);
                unlink('./Public/Uploads/'.$info['img_path_big']);
                unlink('./Public/Uploads/'.$info['img_path_mid']);
                unlink('./Public/Uploads/'.$info['img_path_sm']);
                
                //在从数据库删除数据
                $this->where([
                    'id' => $id,
                ]);
                parent::delete();
            }
        }
    }
    
    //查找一条数据
    public function selone(){
        return $this->where([
                                'id' =>I('get.id'),
                            ])
                            ->find();
    }
    
    //根据查询条件查找一些数据
    public function selwhere(){
        
        /***************搜索功能*******************/
        $title = I('get.title');
        $start = I('get.start');
        $end= I('get.end');
        
        $where = [];
        if($title){
            $where['title'] = ['LIKE',"%$title%"];
        }
        
        if($start && $end){//如果2个时间都值就用BETWEEN AND
            $where['addtime'] = ['BETWEEN', [$start,$end]];
        }
        else if($start){//>=开始时间
            $where['addtime'] = ['EGT', $start];
        }
        else if($end){//<=结束始时间
            $where['addtime'] = ['ELT', $end];
        } 
                
        /***************翻页功能*******************/
        //取出表中该搜索条件的总记录数
        $count = $this->where($where)->count();
        $perPage = 10;
        $page = new \Think\Page($count, $perPage);
        
        //生成翻页用的页面按钮
        $show = $page->show();
        $info['show'] = $show;
        
        /***************查询数据*******************/
        $info['data']= $this->where($where)->order('id DESC')->limit($page->firstRow, $page->listRows)->select();
        return $info;
    }
}