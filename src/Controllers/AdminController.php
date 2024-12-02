<?php
require_once __DIR__ . '/../Database.php';

class AdminController
{
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
        $stmt = $db->prepare("SELECT PASS_HASH FROM admins WHERE UNAME = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['PASS_HASH'])) {
            http_response_code(200);
            echo json_encode(['message' => 'Login successful']);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
    }

    public static function editAdmin()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $password = $data['PASSWORD'];
        $username = $data['ANAME'];
        $aid = $data['AID'];

        $db = Database::connect();
        $stmt = $db->prepare("UPDATE admins SET username = ?, password = ? WHERE id = ?");
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $aid]);

        http_response_code(200);
        echo json_encode(['message' => 'Admin updated successfully']);
    }

    public static function addPlane()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO planes (regno, h_hostess, s_hostess, f_class, e_class, capacity, pilot, airline)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['REGNO'],
            $data['H_HOSTESS'],
            $data['S_HOSTESS'],
            $data['F_CLASS'],
            $data['E_CLASS'],
            $data['CAPACITY'],
            $data['PILOT'],
            $data['AIRLINE']
        ]);

        http_response_code(200);
        echo json_encode(['message' => 'Plane added successfully']);
    }

    public static function addFlight()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO flights (destination, terminal, departure_time, price, available_seats, airline, regno, pid)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['DESTINATION'],
            $data['TERMINAL'],
            $data['DEPATURE_TIME'],
            $data['PRICE'],
            $data['AVAILABLE_SEATS'],
            $data['AIRLINE'],
            $data['REGNO'],
            $data['PID']
        ]);

        http_response_code(200);
        echo json_encode(['message' => 'Flight added successfully']);
    }

    public static function editUser()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $uid = $data['UID'];
        $username = $data['USERNAME'];
        $email = $data['EMAIL'];
        $phone = $data['PHONE'];

        $db = Database::connect();
        $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->execute([$username, $email, $phone, $uid]);

        http_response_code(200);
        echo json_encode(['message' => 'User updated successfully']);
    }

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
