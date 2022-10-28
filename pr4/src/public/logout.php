<?php
	header('HTTP/1.1 401 Unauthorized', true, 401);
	header('Location: login.html');
	exit;
?>