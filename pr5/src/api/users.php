<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
header('Content-Type: application/json');
$con = new mysqli("MYSQL", "user", "toor", "appDB");
$answer = array(
    "status" => "",
);
switch($requestMethod){
    case 'GET':
        if (empty(isset($_GET['id']))){
            $result = $con->query("SELECT * FROM users;");
            while($row = $result->fetch_assoc()){
                $answer[] = $row;
            }
        } else {
            $result = $con->query("SELECT * FROM users WHERE ID = " . $_GET['id'] . ";");
            while($row = $result->fetch_assoc()){
                $answer[] = $row;
            }
        }
        if (count($answer) == 1){
            $answer["status"] = "Error. User(-s) not found.";
            http_response_code(404);
        } else {
            $answer["status"] = "Success. User(-s) found";
            http_response_code(200);
        }
        echo json_encode($answer);
        break;
    case 'POST':
        $json = file_get_contents('php://input'); 
        $obj = json_decode($json);
        if (!empty($obj->{'username'}) && !empty($obj->{'password'}) && !empty($obj->{'email'})){
            $username = $obj->{'username'};
            $password = $obj->{'password'};
            $email = $obj->{'email'};
            $query_result = $con->query("SELECT * FROM users WHERE username='".$username."'");
            $result = $query_result->fetch_row();
            if (!empty($result)){
                $answer["status"] = "Error. User with this username already exists.";
                http_response_code(409);
            } else {
                $password = crypt($password);
                $stmt = $con->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
                $stmt->bind_param('sss', $username, $password, $email);
                $stmt->execute();
                $answer["status"] = "Success. User created.";
                http_response_code(200);
            }
        } else {
            $answer["status"] = "Error. Need username, password and email in JSON BODY.";
            http_response_code(422);
        }
        echo json_encode($answer);
        break;
    case 'PATCH':
        $json = file_get_contents('php://input'); 
        $obj = json_decode($json);
        if (!empty($obj->{'username'}) && !empty($obj->{'email'})){
            if (empty(isset($_GET['id']))){
                $answer["status"] = "Error. Need ID Param";
                http_response_code(422);
            } else {
                $query_result = $con->query("SELECT * FROM users WHERE ID='".$_GET['id']."'");
                $result = $query_result->fetch_row();
                if (!empty($result)){
                    $query_result = $con->query("SELECT * FROM users WHERE username='".$obj->{'username'}."' AND ID!='".$_GET['id']."'");
                    $result = $query_result->fetch_row();
                    if (!empty($result)){
                    $answer["status"] = "Error. User with this username already exists.";
                    http_response_code(409);
                    } else {
                    $con->query("UPDATE users SET username='".$obj->{'username'}."', email='".$obj->{'email'}."' WHERE ID='".$_GET['id']."'");
                    $answer["status"] = "Success. User updated.";
                    http_response_code(200);
                    }
                } else {
                    $answer["status"] = "Error. User not found.";
                    http_response_code(404);
                }
            }
        } else {
            $answer["status"] = "Error. Need username and email in JSON BODY.";
            http_response_code(422);
        }
        echo json_encode($answer);
        break;
    case 'DELETE':
        if (empty(isset($_GET['id']))){
            $answer["status"] = "Error. Need ID Param";
            http_response_code(422);
        } else {
            $query_result = $con->query("SELECT * FROM users WHERE ID='".$_GET['id']."'");
            $result = $query_result->fetch_row();
            if (!empty($result)){
                $query_result = $con->query("DELETE FROM users WHERE ID='".$_GET['id']."'");
                $answer["status"] = "Success. User Deleted.";
                http_response_code(200);
            } else {
                $answer["status"] = "Error. User not found.";
                http_response_code(404);
            }
        }
        echo json_encode($answer);
        break;
    default:
        $answer["status"] = "This REST Method not allowed.";
        http_response_code(500);
        echo json_encode($answer);
        break;
}
?>