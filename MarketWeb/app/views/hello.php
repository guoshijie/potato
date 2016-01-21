<!doctype html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<title>Laravel PHP Framework</title>
</head>
<body>
	<h1>获取用户</h1>
	<a href="/server.php/getUser?id=1" target="_black">获取用户ID=1</a>
	<br/>
	<a href="/server.php/getUser?id=2" target="_black">获取用户ID=2</a>


	<h1>创建用户</h1>
	<form action="/server.php/createUser" method="post">
		<input type="text" name="name" value="mignzi" />
		<button type="submit">Submit</button>
	</form>
</body>
</html>
