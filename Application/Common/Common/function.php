<?php

/**
 * 使用HTMLPurifier类处理字符串里包含的危险标签 防止XSS攻击
 * @param string $data
 * @return string
 */
function removeXSS($data){
    //引入这个类
    require_once './htmlpurifier/HTMLPurifier.auto.php';
    //创建一个默认的配置文件
    $_clean_xss_config = \HTMLPurifier_Config::createDefault();
    //设置编码
    $_clean_xss_config->set('Core.Encoding', 'UTF-8');
    //设置允许出现的标签
    $_clean_xss_config->set('HTML.Allowed','table,tbody,tr,td,div,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]');
    //设置允许出现的css
    $_clean_xss_config->set('CSS.AllowedProperties','font,font-size,font-weight,font=style,font-family,text-decoration.padding-left.color.background-color,text-align');
    //a标签上是否允许有_blank属性
    $_clean_xss_config->set('HTML.TargetBlank', TRUE);
    $_clean_xss_obj = new \HTMLPurifier($_clean_xss_config);
    //执行过滤
    return $_clean_xss_obj->purify($data);
}

/**
 * 把传入$data的数据按无限级分类来排序
 * @param array $data 要处理的数据
 * @param int $pid 父分类id
 * @param int $step id所处的级数
 * @return array
 */
function recursiveCategoryData($data,$pid=0,$step=0){
    global  $res;
    foreach($data as $v){
        if($pid == $v['parent_id']){
            if($step > 0)
                $flg = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$step-1) . str_repeat('└―',1);
            $v['cate_name'] = $flg.$v['cate_name'];
            $res[] = $v;
            recursiveCategoryData($data,$v['id'],$step+1);
        }
    }
    
    return $res;
}
