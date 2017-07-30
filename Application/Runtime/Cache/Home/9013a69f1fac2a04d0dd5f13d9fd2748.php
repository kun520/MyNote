<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<title>添加留言</title>
	<meta charset="utf-8">
</head>
<body>

<h1>添加留言</h1>
<h2><a href="/index.php/Home/Note/lst">留言列表</a></h2>

<form action="/index.php/Home/Note/doadd" method="post">
	
	<div>
		标题:<input size="60" type="text" name="title" />
	</div>
	<div>
		内容:<textarea name="content" rows="10" cols="60"></textarea>		
	</div>
	<div>
		<input type="submit" value="添加" />
		<input type="reset" value="重填" />
	</div>
	
</form>

</body>
</html>