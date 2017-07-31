<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<title>留言列表</title>
	<meta charset="utf-8">
	<style type="text/css" >
		.page{
			margin:10px 0px;
		}
		.page a{
			margin:10px;
			padding:3px 6px;
			border:1px solid red;
			
		}
	</style>
</head>
<body>

<h1>留言列表</h1>
<h2><a href="/index.php/Home/Note/add">添加留言</a></h2>

<form>
标题:<input name="title" type="text" value="<?php echo ($_GET['title']); ?>" />
时间从:<input name="start" type="text" value="<?php echo ($_GET['start']); ?>" />
到:<input name="end" type="text" value="<?php echo ($_GET['end']); ?>" />
<input type="submit" value="查询" />
</form>

<div class="page"><?php echo ($show); ?></div>
<table border="1" width="100%">
	<tr><th>Id</th><th>标题</th><th>内容</th><th>添加时间</th><th>ip</th></tr>
	<?php if(is_array($data)): foreach($data as $key=>$lst): ?><tr><td><?php echo ($lst["id"]); ?></td><td><?php echo (htmlspecialchars($lst["title"])); ?></td><td><?php echo (htmlspecialchars(mb_substr($lst["content"],0,70,'utf8'))); if((mb_strlen($lst["content"],'utf8')) > "70"): ?>...<?php endif; ?></td><td><?php echo ($lst["addtime"]); ?></td><td><?php echo (long2ip($lst["ip"])); ?></td></tr><?php endforeach; endif; ?>
</table>
<div class="page"><?php echo ($show); ?></div>

</body>
</html>