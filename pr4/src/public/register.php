<?php
	if(isset($_POST["register"])){
	if(!empty($_POST['email']) && !empty($_POST['username']) && !empty($_POST['password'])) {
		$con = new mysqli("MYSQL", "user", "toor", "appDB");
		$email=htmlspecialchars($_POST['email']);
 		$username=htmlspecialchars($_POST['username']);
 		$password=htmlspecialchars($_POST['password']);
		$query_result = $con->query("SELECT * FROM users WHERE username='".$username."'");
		$result = $query_result->fetch_row();
		if (!empty($result)){
			echo '<script language="javascript">';
			echo 'alert("ЮЗЕР С ТАКИМ ИМЕНЕМ УЖЕ СОЗДАН")';
			echo '</script>';
		} else {
			$password = crypt($password);
			$stmt = $con->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
			$stmt->bind_param('sss', $username, $password, $email);
			$stmt->execute();
			echo '<script language="javascript">';
			echo 'window.location.replace("login.html");';
			echo '</script>';
		}
	}}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html lang="en">
	<head>
	<meta charset="utf-8"> 
 <title>Регистрация</title>
 <link href="style.css" media="screen" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800'rel='stylesheet' type='text/css'>
	</head>
	<body>
<div class="container mregister">
<div id="login">
 <h1>Регистрация</h1>
<form action="register.php" id="registerform" method="post"name="registerform">
<p><label for="user_pass">E-mail<br>
<input class="input" id="email" name="email" size="32"type="email" value=""></label></p>
<p><label for="user_pass">Имя пользователя<br>
<input class="input" id="username" name="username"size="20" type="text" value=""></label></p>
<p><label for="user_pass">Пароль<br>
<input class="input" id="password" name="password"size="32"   type="password" value=""></label></p>
<p class="submit"><input class="button" id="register" name= "register" type="submit" value="Зарегистрироваться"></p>
	  <p class="regtext">Уже зарегистрированы? <a href= "login.php">Введите имя пользователя</a>!</p>
 </form>
</div>
</div>
<footer>
© 2022 Maxawergy. Все права не защищены.
</footer>
</body>
</html>



