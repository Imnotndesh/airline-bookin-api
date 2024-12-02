<?php
require_once __DIR__ . '/../Database.php';

class UserController
{
    public static function register()
    {
        $db = Database::connect();
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare('INSERT INTO users (UNAME, EMAIL, PASS_HASH, PHONE,FNAME,BALANCE) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$data['username'], $data['email'], password_hash($data['password'], PASSWORD_DEFAULT), $data['phone'],$data['fname'],$data['balance']]);
        echo json_encode(['message' => 'User registered successfully']);
    }

    public static function login()
    {
        $db = Database::connect();
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error'=> 'missing login credentials']);
            return;
        }
        $trialusername = $data['username'];
        $trialpassword = $data['password'];
        try{
            $stmt = $db->prepare('SELECT PASS_HASH FROM users WHERE UNAME = :uname');
            $stmt->bindParam(':uname',$trialusername,PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt ->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($data['password'], $user['PASS_HASH'])) {
                echo json_encode(['message' => 'Login successful']);
                return;
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
                return;
            }
        }catch(PDOException $e){
            echo json_encode(['error'=> 'database error:',$e->getMessage()]);
        }
    }

    public static function getUserDetails()
    {
        $data = $_GET;  // Fetch query parameter (UID)

        // Check if UID is provided
        if (!isset($data['username'])) {
            http_response_code(400);
            echo json_encode(['error' => 'username is required']);
            return;
        }

        $username = $data['username'];
        try {
            $db = Database::connect();
            
            // Query to fetch all bookings for the user
            $stmt = $db->prepare("SELECT * FROM users WHERE UNAME = ?");
            $stmt->execute([$username]);
            
            // Fetch all bookings
            $userDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($userDetails){
                http_response_code(200);
                echo json_encode($userDetails);
            }else{
                http_response_code(500);
                echo json_encode(["error"=> "User not found"]);
            }
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public static function editUser()
    {
        $db = Database::connect();
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare('UPDATE users SET EMAIL = ?, PHONE = ?, FNAME = ? WHERE UNAME = ?');
        $stmt->execute([$data['email'], $data['phone'], $data['fname'],$data['username']]);
        http_response_code(200);
        echo json_encode(['message' => 'User updated successfully']);
    }

    public static function deleteUser()
    {
        if (isset($data['username'])){
            http_response_code(400);
            echo json_encode(['error' => 'username is required']);
            return;
        }
        try{
            $db = Database::connect();
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $db->prepare('DELETE FROM users WHERE UNAME = ?');
            $stmt->execute([$data['username']]);
            echo json_encode(['message' => 'User deleted successfully']);
        }catch (PDOException $e) {
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    public static function topUpBalance(){
        $db = Database::connect();
        $data = json_decode(file_get_contents('php://input'),true);
        if (isset($data['balance'])?(int)$data['balance']:null){
            http_response_code(400);
            echo json_encode(['error'=> 'Balance to top up required']);
        }
        $username = $data['username'];
        $topUpAmount = (int)$data['balance'];
        try {
            $stmt = $db->prepare('SELECT BALANCE FROM users WHERE UNAME = :username LIMIT 1');
            $stmt->bindParam(':username',$username,PDO::PARAM_STR);
            $stmt ->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user){
                $currentBalance = $user['BALANCE'];
            }else{
                echo json_encode(['error'=> 'User not found']);
                http_response_code(404);
                return;
            }
            $newBalance = $currentBalance + $topUpAmount;
            $updateStmt = $db->prepare("UPDATE users SET BALANCE = :newBalance WHERE UNAME = :uname");
            $updateStmt->bindParam(':newBalance', $newBalance, PDO::PARAM_INT);
            $updateStmt->bindParam(':uname', $username, PDO::PARAM_STR);
            $updateStmt->execute();
            http_response_code(200);
            echo json_encode(['Message'=> 'Updated balance successfully']);
        }catch (PDOException $e) {
            echo json_encode(['error'=> 'Database error: '. $e->getMessage()]);
        }
    }
}
