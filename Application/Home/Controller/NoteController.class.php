<?php

namespace Home\Controller;
use Think\Controller;
use Think\Upload;

class NoteController extends Controller{
    //生成验证码
    public function captcha(){
        $Verify = new \Think\Verify();
        $Verify->entry();
    }
    
    //修改留言  --> 处理表单,修改数据库
    public function doedit(){
        
        /********************接受*********************/
        $id = trim($_POST['id']);
        $title = trim($_POST['title']);
        $content= trim($_POST['content']);
        $captcha= trim($_POST['captcha']);
        //验证数据        
        if(empty($id)){
            $this->error('参数错误');
        }
        $verify = new \Think\Verify();
        if(!$verify->check($captcha)){
            $this->error('验证码不正确');
        }
        
        if(empty($title)){
            $this->error('标题不能为空');
        }
        if(empty($content)){
            $this->error('内容不能为空');
        }
        /********************上传图片*********************/
        $upload = new \Think\Upload();
        $upload->maxSize = 2*1024*1024 ;// 设置附件上传大小  单位:字节  2M
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Public/Uploads/'; // 设置附件上传根目录 --> 需手动建立目录
        $upload->savePath = 'Note/'; // 设置附件上传（子）目录 --> 程序自动建立
        
        //要插入数据库的数据
        $data = array(
            'title' => $title,
            'content' => $content,
            'addtime' => date('Y-m-d H:i:s'),
            'ip' => get_client_ip(1),
        );
        
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
            
            $data['img_path'] = $imgPath;
            $data['img_path_big'] = $imgPath_big;
            $data['img_path_mid'] = $imgPath_mid;
            $data['img_path_sm'] = $imgPath_sm;
            
            /*******************删除原图片**********************/            
            unlink('./Public/Uploads/'.$_POST['img_path']);
            unlink('./Public/Uploads/'.$_POST['img_path_big']);
            unlink('./Public/Uploads/'.$_POST['img_path_mid']);
            unlink('./Public/Uploads/'.$_POST['img_path_sm']);
        }
        else{
            // 上传错误提示错误信息
            $error = $upload->getError();
            //可以不上传图片
            if($error != "没有文件被上传！"){
                $this->error($error);
            }
        }
        //操作数据库
        $note = M('Note');
        $id = $note->where([
                            'id' => $id,
                           ])
                   ->save($data);
        //提示成功或失败
        $this->success("修改成功!", '/index.php/Home/Note/lst');
    }
    
    //修改留言  --> 显示修改留言的表单
    public function edit(){
        
        /********************取数据********************/
        if(empty($_GET['id'])){
            $this->error("参数错误!");
        }
        $id = $_GET['id'];
        
        /*********************得到该记录的信息 并传到模版中***********************/
        $note = M("Note");
        $info = $note->where([
                                'id' => $id,
                            ])
                     ->find();
        $this->assign('info',$info);
        
        /*************************显示模版*******************************/
        $this->display();
    }
    
    //批量删除留言
    public function pldelete(){
        
        $note = M("Note");
        foreach ( $_POST['selId'] as $id){
            //先删除记录的图片
            $info = $note->field("img_path,img_path_big,img_path_mid,img_path_sm")
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
                $note->where([
                    'id' => $id,
                ])
                ->delete();
            }
        }
        //提示信息并跳转到上一个页面
        $this->success('批量删除成功!');
    }
    
    //删除留言
    public function delete(){
        if(empty($_GET['id'])){
            $this->error("参数错误!");
        }
        $id = $_GET['id'];
        
        $note = M("Note");
        //先删除记录的图片
        $info = $note->field("img_path,img_path_big,img_path_mid,img_path_sm")
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
            $note->where([
                            'id' => $id,
                         ])
                 ->delete();
            
            //提示信息并跳转到上一个页面
            $this->success('删除成功!');
        }
        else {
            $this->error("无此记录");
        }
        
    }
    
    //查看所有留言
    public function lst(){
        
        /***************搜索功能*******************/
        $title = isset($_GET['title']) ? $_GET['title'] : '';
        $start = isset($_GET['start']) ? $_GET['start'] : '';
        $end= isset($_GET['end']) ? $_GET['end'] : '';
        
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
        //实例化一个表模型对象
        $note = M('Note');
        //取出表中该搜索条件的总记录数
        $count = $note->where($where)->count();
        $perPage = 10;
        $page = new \Think\Page($count, $perPage);
        
        //生成翻页用的页面按钮
        $show = $page->show();
        $this->assign('show', $show);
        
        /***************查询数据*******************/
        $data = $note->where($where)->order('id DESC')->limit($page->firstRow, $page->listRows)->select();
        
        //把数据传到页面中
        $this->assign('data', $data);
                
        $this->display();
    }
    
    //添加留言 - 显示新建表单
    public function add(){
        
        $this->display();
    }
    
    //添加留言 - 处理表单,插入数据
    public function doadd(){
                
        //接受提交的数据
        $title = trim($_POST['title']);
        $content= trim($_POST['content']);
        $captcha= trim($_POST['captcha']);
        //验证数据
        $verify = new \Think\Verify(); 
        if(!$verify->check($captcha)){            
            $this->error('验证码不正确');
        }
        
        if(empty($title)){
            $this->error('标题不能为空');
        }
        if(empty($content)){
            $this->error('内容不能为空');
        }
        /********************上传图片*********************/
        $upload = new \Think\Upload();
        $upload->maxSize = 2*1024*1024 ;// 设置附件上传大小  单位:字节  2M
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Public/Uploads/'; // 设置附件上传根目录 --> 需手动建立目录
        $upload->savePath = 'Note/'; // 设置附件上传（子）目录 --> 程序自动建立
        
        //要插入数据库的数据
        $data = array(
            'title' => $title,
            'content' => $content,
            'addtime' => date('Y-m-d H:i:s'),
            'ip' => get_client_ip(1),
        );
        
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
            
            $data['img_path'] = $imgPath;
            $data['img_path_big'] = $imgPath_big;
            $data['img_path_mid'] = $imgPath_mid;
            $data['img_path_sm'] = $imgPath_sm;
        }
        else{
            // 上传错误提示错误信息
            $error = $upload->getError();
            //可以不上传图片
            if($error != "没有文件被上传！"){
                $this->error($error);
            }
        }
        //操作数据库
        $note = M('Note');
        $id = $note->add($data);
        //提示成功或失败
        $this->success("添加成功:新添加记录的id为$id", '/index.php/Home/Note/lst');
        
    }
    
    
    //批量插入500条测试数据
    public function testInsert(){
        $str = '古有“指南针、造纸术、火药、印刷术”四大发明，现有“高铁、支付宝、共享单车、网购”新四大发明。以摩拜、ofo、小蓝等为首的共享单车开始走出国门，你怎么看呢？

共享单车海外圈地并非我们想象的那般粗暴，出海之路风险未知，而且很烧钱，反而要步步谨慎。

摩拜表示并不是每个城市都适合骑单车出行，它们进驻海外城市之前，会展开全面调研，并且与当地政府和交通管理部门进行沟通，了解彼此的意愿，只有当一切都合适后，才会进行尝试运营。

小蓝单车曾经也表示会优先考虑人口密度较大且有骑行文化或国际知名的城市，在这些城市落脚会更合适些，而非盲目扩张。

目前，摩拜、ofo、小蓝在海外市场的单车投放累计总数估计还不超过3万辆，甚至还不如广州一个行政区所投放的单车数量，只是雷声大雨点小而已，海外影响力有限。

各家海外布局情况

摩拜：今年实现全球200城小目标

摩拜海外扩张节奏：2017年3月21日，新加坡正式运营，投放数量未知；2017年6月13日，英国曼彻斯特与索尔福德，合计投放1000辆单车。

2017年6月22日，宣布在日本福冈成立分公司，投放数量未知；2017年6月23日，宣布在日本札幌提供服务，投放数量未知；2017年7月25日，宣布在意大利佛罗伦萨与米兰运营，投放数量未知。摩拜的优势在于产品，尤其是它那把智能锁，扫码即解锁在我国繁荣的移动互联里习以为常，但放之海外市场仍是一件新鲜事。

更重要的是，智能锁整合了北斗、GPS、格洛纳斯多模卫星导航芯片和物联网通信芯片，通过大数据手段实现各国网络的无缝切换，实现全球精细化智能运维。

“它不是辆普通单车”的自信让摩拜迫不及待地立下“年底全球200城”的目标，当然，国内城市还是大头。小蓝应该是最早有出海动作的国内共享单车平台，与前面两位大佬对比，小蓝的出海布局貌似只是试试水而已。

截止目前所了解到的信息，小蓝单车暂时没有其他海外城市运营的消息。这也是可以理解，小蓝在国内共享单车处于第二集团的位置，单车数、用户量、订单量以及资本都落后于前面两者，出海计划就显得更“小巧”。';
        
        $note = M('Note');
        for($i=1; $i<501; $i++){           
        
            $data = array(
                'title' => mb_substr($str, rand(0,100),rand(20,40),'utf8'),
                'content' => mb_substr($str, rand(10,100),rand(200,600),'utf8'),
                'addtime' => date('Y-m-d H:i:s'),
                'ip' => ip2long(rand(1,200).'.'.rand(0,255).'.'.rand(0,255).'.'.rand(1,255)),
            );
            $id = $note->add($data);
        }
    }
}