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
            $result = $con->query("SELECT * FROM tickets;");
            while($row = $result->fetch_assoc()){
                $answer[] = $row;
            }
        } else {
            $result = $con->query("SELECT * FROM tickets WHERE ID = " . $_GET['id'] . ";");
            while($row = $result->fetch_assoc()){
                $answer[] = $row;
            }
        }
        if (count($answer) == 1){
            $answer["status"] = "Error. Ticket(-s) not found.";
            http_response_code(404);
        } else {
            $answer["status"] = "Success. Ticket(-s) found";
            http_response_code(200);
        }
        echo json_encode($answer);
        break;
    case 'POST':
        $json = file_get_contents('php://input'); 
        $obj = json_decode($json);
        if (!empty($obj->{'price'}) && !empty($obj->{'source'}) && !empty($obj->{'destination'}) && !empty($obj->{'title'})){
            $price = $obj->{'price'};
            $source = $obj->{'source'};
            $destination = $obj->{'destination'};
            $title = $obj->{'title'};
            $query_result = $con->query("SELECT * FROM tickets WHERE title='".$title."'");
            $result=$query_result->fetch_row();
            if (!empty($result)){
                $answer["status"] = "Error. Ticket with this title already exists.";
                http_response_code(409);
            } else {
                $stmt = $con->prepare("INSERT INTO tickets (price, source, destination, title) VALUES (?, ?, ?, ?)");
                $stmt->bind_param('ssss', $price, $source, $destination, $title);
                $stmt->execute();
                $answer["status"] = "Success. Ticket created.";
                http_response_code(200);
            }
        } else {
            $answer["status"] = "Error. Need price, source, destination and title in JSON BODY.";
            http_response_code(422);
        }
        echo json_encode($answer);
        break;
    case 'PATCH':
        $json = file_get_contents('php://input'); 
        $obj = json_decode($json);
        if (!empty($obj->{'price'}) && !empty($obj->{'source'}) && !empty($obj->{'destination'}) && !empty($obj->{'title'})){
            if (empty(isset($_GET['id']))){
                $answer["status"] = "Error. Need ID Param";
                http_response_code(422);
            } else {
                $query_result = $con->query("SELECT * FROM tickets WHERE ID='".$_GET['id']."'");
                $result = $query_result->fetch_row();
                if (!empty($result)){
                    $query_result = $con->query("SELECT * FROM tickets WHERE title='".$obj->{'title'}."' AND ID!='".$_GET['id']."'");
                    $result = $query_result->fetch_row();
                    if (!empty($result)){
                    $answer["status"] = "Error. Ticket with this title already exists.";
                    http_response_code(409);
                    } else {
                    $con->query("UPDATE tickets SET price='".$obj->{'price'}."', source='".$obj->{'source'}."' , destination='".$obj->{'destination'}."' , title='".$obj->{'title'}."' WHERE ID='".$_GET['id']."'");
                    $answer["status"] = "Success. Ticket updated.";
                    http_response_code(201);
                    }
                } else {
                    $answer["status"] = "Error. Ticket not found.";
                    http_response_code(404);
                }
            }
        } else {
            $answer["status"] = "Error. Need price, source, destination and title in JSON BODY.";
            http_response_code(422);
        }
        echo json_encode($answer);
        break;
    case 'DELETE':
        if (empty(isset($_GET['id']))){
            $answer["status"] = "Error. Need ID Param";
            http_response_code(422);
        } else {
            $query_result = $con->query("SELECT * FROM tickets WHERE ID='".$_GET['id']."'");
            $result = $query_result->fetch_row();
            if (!empty($result)){
                $query_result = $con->query("DELETE FROM tickets WHERE ID='".$_GET['id']."'");
                $answer["status"] = "Success. Ticket Deleted.";
                http_response_code(200);
            } else {
                $answer["status"] = "Error. Ticket not found.";
                http_response_code(204);
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