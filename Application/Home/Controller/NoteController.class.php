<?php
namespace Home\Controller;
use Think\Controller;

class NoteController extends Controller{
    //查看所有留言
    public function lst(){
        /***************翻页功能*******************/
        //实例化一个表模型对象
        $note = M('Note');
        //取出表中的总记录书
        $count = $note->count();
        $perPage = 10;
        $page = new \Think\Page($count, $perPage);
        
        //生成翻页用的页面按钮
        $show = $page->show();
        $this->assign('show', $show);
        
        $data = $note->order('id DESC')->limit($page->firstRow, $page->listRows)->select();
        
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
        //验证数据
        if(empty($title)){
            $this->error('标题不能为空');
        }
        if(empty($content)){
            $this->error('内容不能为空');
        }
        //操作数据库
        $note = M('Note');
        $data = array(
            'title' => $title,
            'content' => $content,
            'addtime' => date('Y-m-d H:i:s'),
            'ip' => get_client_ip(1),
        );
        $id = $note->add($data);
        $this->success("添加成功:新添加记录的id为$id", '/index.php/Home/Note/lst');
        //提示成功或失败
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