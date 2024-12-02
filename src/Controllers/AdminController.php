<?php
require_once __DIR__ . '/../Database.php';

class AdminController
{
    public static function dbError(PDOException $e){
        http_response_code(500);
        echo json_encode(['error'=>'Database error',$e->getMessage()]);
    }
    public static function register()
    {
        $db = Database::connect();
        $data = json_decode(file_get_contents('php://input'), true);
        try{
            $stmt = $db->prepare('INSERT INTO users (UNAME, EMAIL, PASS_HASH, PHONE,FNAME,BALANCE) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$data['username'], $data['email'], password_hash($data['password'], PASSWORD_DEFAULT), $data['phone'],$data['fname'],$data['balance']]);
            echo json_encode(['message' => 'User registered successfully']);
        }catch(PDOException $e){
            self::dbError($e);
            return;
        }
    }
    // credential stuff
    public static function login()
    {
        $db = Database::connect();
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'];
        $password = $data['password'];
        if (!isset($username) || !isset($password)) {
            http_response_code(400);
            echo json_encode(['error'=>'Missing login credentials']);
            return;
        }
        try{
            $stmt = $db->prepare("SELECT PASS_HASH FROM admins WHERE UNAME = ? LIMIT 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($admin && password_verify($password, $admin['PASS_HASH'])) {
                http_response_code(200);
                echo json_encode(['message' => 'Login successful']);
                return;
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
                return;
            }
        }catch(PDOException $e){
            self::dbError($e);
            return;
        }
    }

    // adding stuff
    public static function addPlane()
    {
        $db = Database::connect();
        $data = json_decode(file_get_contents('php://input'), true);
        try{
            $stmt = $db->prepare("INSERT INTO planes (REGNO, H_HOSTESS, S_HOSTESS, F_CLASS, E_CLASS, CAPACITY, PILOT, AIRLINE)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['regno'],
                $data['h_hostess'],
                $data['s_hostess'],
                $data['f_class_seats'],
                $data['e_class_seats'],
                $data['capacity'],
                $data['pilot'],
                $data['airline']
            ]);
            
            http_response_code(200);
            echo json_encode(['message' => 'Plane added successfully']);
        }catch(PDOException $e){
            self::dbError($e);
            return;
        }
    }
    
    public static function addFlight()
    {
        $db = Database::connect();
        $data = json_decode(file_get_contents('php://input'), true);
        try{

            $stmt = $db->prepare("INSERT INTO flights (DESTINATION, TERMINAL, DEPATURE_TIME, PRICE, AVAILABLE_SEATS, AIRLINE, REGNO, PID)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['destination'],
                $data['terminal'],
                $data['depature_time'],
                $data['price'],
                $data['available_seats'],
                $data['airline'],
                $data['regno'],
                $data['pid']
            ]);
            http_response_code(200);
            echo json_encode(['message' => 'Flight added successfully']);
        }catch(PDOException $e){
            self::dbError($e);
            return;
        }
    }

    // editing stuff
    public static function editAdmin()
    {
        $db = Database::connect();
        $data = json_decode(file_get_contents('php://input'), true);
        try{
            $stmt = $db->prepare('UPDATE users SET FNAME = ?, PASS_HASH = ? WHERE UNAME = ?');
            $stmt->execute([$data['fname'],password_hash($data['password'], PASSWORD_DEFAULT),$data['username']]);
            http_response_code(200);
            echo json_encode(['message' => 'User updated successfully']);
        }catch(PDOException $e){
            self::dbError($e);
            return;
        }
    }
    
    public static function editUser()
    {
        $db = Database::connect();
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['username'])){
            http_response_code(400);
            echo json_encode(['error'=> 'Username needed to edit user']);
            return;
        }
        try{
            $stmt = $db->prepare('UPDATE users SET EMAIL = ?, PHONE = ?, FNAME = ? , PASS_HASH = ? WHERE UNAME = ?');
            $stmt->execute([$data['email'], $data['phone'], $data['fname'],password_hash($data['password'],PASSWORD_DEFAULT),$data['username']]);
            http_response_code(200);
            echo json_encode(['message' => 'User updated successfully']);
        }catch (PDOException $e){
            self::dbError($e);
            return;
        }
    }

    // deleting stuff
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
            self::dbError($e);
            return;
        }
    }
    public static function deletePlane()
    {
        if (isset($data['pid'])){
            http_response_code(400);
            echo json_encode(['error' => 'username is required']);
            return;
        }
        try{
            $db = Database::connect();
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $db->prepare('DELETE FROM planes WHERE PID = ?');
            $stmt->execute([$data['pid']]);
            echo json_encode(['message' => 'Plane removed successfully']);
        }catch (PDOException $e) {
            self::dbError($e);
            return;
        }
    }
    public static function deleteFlight()
    {
        if (isset($data['fid'])){
            http_response_code(400);
            echo json_encode(['error' => 'username is required']);
            return;
        }
        try{
            $db = Database::connect();
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $db->prepare('DELETE FROM flights WHERE FID = ?');
            $stmt->execute([$data['fid']]);
            echo json_encode(['message' => 'Flight removed successfully']);
        }catch (PDOException $e) {
            self::dbError($e);
            return;
        }
    }
    //viewing stuff
    public static function viewPlanes()
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT * FROM planes");
        $planes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($planes);
    }

    public static function viewUsers()
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT * FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($users);
    }

    public static function viewFlights()
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT * FROM flights");
        $flights = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($flights);
    }
}
