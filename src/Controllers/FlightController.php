<?php
require_once __DIR__ . '/../Database.php';

class FlightController
{
    public static function bookFlight()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $username = $data['username'];
        $fid = $data['fid'];
        $ticketsAmt = (int)$data['tickets_amt'];

        try {
            $db = Database::connect();

            // Start a transaction
            $db->beginTransaction();

            // Fetch user name
            $stmt = $db->prepare("SELECT FNAME FROM users WHERE UNAME = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                $db->rollBack();
                return;
            }

            $fname = $user['FNAME'];

            // Fetch flight details
            $stmt = $db->prepare("SELECT DEPARTURE_TIME, PRICE, DESTINATION, AIRLINE, REGNO, AVAILABLE_SEATS FROM flights WHERE FID = ?");
            $stmt->execute([$fid]);
            $flight = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$flight) {
                http_response_code(404);
                echo json_encode(['error' => 'Flight not found']);
                $db->rollBack();
                return;
            }

            $departureTime = $flight['DEPARTURE_TIME'];
            $price = $flight['PRICE'];
            $destination = $flight['DESTINATION'];
            $airline = $flight['AIRLINE'];
            $regno = $flight['REGNO'];
            $availableSeats = $flight['AVAILABLE_SEATS'];

            // Check seat availability
            if ($availableSeats < $ticketsAmt) {
                http_response_code(400);
                echo json_encode(['error' => 'Not enough seats available']);
                $db->rollBack();
                return;
            }

            // Calculate total price
            $totalPrice = $price * $ticketsAmt;

            // Reduce available seats
            $newAvailableSeats = $availableSeats - $ticketsAmt;
            $stmt = $db->prepare("UPDATE flights SET AVAILABLE_SEATS = ? WHERE FID = ?");
            $stmt->execute([$newAvailableSeats, $fid]);

            // Insert into bookings table
            $stmt = $db->prepare("
                INSERT INTO bookings (REGNO, UID, FID, DEPARTURE_TIME, FNAME, AIRLINE,TICKETS_AMT, DESTINATION, PRICE)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $regno,
                $username,
                $fid,
                $departureTime,
                $fname,
                $airline,
                $ticketsAmt,
                $destination,
                $totalPrice
            ]);

            // Respond with success
            http_response_code(200);
            echo json_encode([
                'message' => 'Booking successful'
                ]
            );

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    public static function getAllFlights()
    {
    try {
        $db = Database::connect();
        
        // Query to fetch all flights
        $stmt = $db->prepare("SELECT * FROM flights");
        $stmt->execute();
        
        // Fetch all flights
        $flights = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($flights) > 0) {
            http_response_code(200);
            echo json_encode($flights);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'No flights found']);
        }
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    }
}
