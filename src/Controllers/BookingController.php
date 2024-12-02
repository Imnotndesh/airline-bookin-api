<?php
require_once __DIR__ . '/../Database.php';

class BookingController
{
    public static function getUserBookings()
    {
        $data = $_GET;  // Fetch query parameter (UID)

        // Check if UID is provided
        if (!isset($data['UID'])) {
            http_response_code(400);
            echo json_encode(['error' => 'UID is required']);
            return;
        }

        $uid = $data['UID'];

        try {
            $db = Database::connect();
            
            // Query to fetch all bookings for the user
            $stmt = $db->prepare("SELECT * FROM bookings WHERE UID = ?");
            $stmt->execute([$uid]);
            
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
