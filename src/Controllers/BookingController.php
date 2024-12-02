<?php
require_once __DIR__ . '/../Database.php';

class BookingController
{
    public static function getUserBookings()
    {
        $data = $_GET;  // Fetch query parameter (UID)

        // Check if UID is provided
        if (!isset($data['username'])) {
            http_response_code(400);
            echo json_encode(['error' => 'UNAME is required']);
            return;
        }

        $username = $data['username'];

        try {
            $db = Database::connect();
            
            // Query to fetch all bookings for the user
            $stmt = $db->prepare("SELECT * FROM bookings WHERE UNAME = ?");
            $stmt->execute([$username]);
            
            // Fetch all bookings
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($bookings) > 0) {
                http_response_code(200);
                echo json_encode($bookings);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'No bookings found for this user']);
            }
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
