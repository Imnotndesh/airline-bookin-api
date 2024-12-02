<?php
require_once __DIR__ . '/../Database.php';

class FlightController
{
    public static function bookFlight()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $uid = $data['UID'];
        $fid = $data['FID'];
        $ticketsAmt = $data['TICKETS_AMT'];

        try {
            $db = Database::connect();

            // Start a transaction
            $db->beginTransaction();

            // Fetch user name
            $stmt = $db->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->execute([$uid]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                $db->rollBack();
                return;
            }

            $fname = $user['username'];

            // Fetch flight details
            $stmt = $db->prepare("SELECT departure_time, price, destination, airline, regno, available_seats FROM flights WHERE id = ?");
            $stmt->execute([$fid]);
            $flight = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$flight) {
                http_response_code(404);
                echo json_encode(['error' => 'Flight not found']);
                $db->rollBack();
                return;
            }

            $departureTime = $flight['departure_time'];
            $price = $flight['price'];
            $destination = $flight['destination'];
            $airline = $flight['airline'];
            $regno = $flight['regno'];
            $availableSeats = $flight['available_seats'];

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
            $stmt = $db->prepare("UPDATE flights SET available_seats = ? WHERE id = ?");
            $stmt->execute([$newAvailableSeats, $fid]);

            // Insert into bookings table
            $stmt = $db->prepare("
                INSERT INTO bookings (regno, user_id, flight_id, departure_time, fname, airline, tickets_amt, destination, price)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $regno,
                $uid,
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
