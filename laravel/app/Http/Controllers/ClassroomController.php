<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Connection;

class ClassroomController extends Controller
{
    public function getClassrooms(Request $request, Connection $connection)
    {

        $today = date("Y-m-d");
        $dayOfWeekNumber = date('N', strtotime($today));

        $dateStarWeek = date('Y-m-d', strtotime("-" . ($dayOfWeekNumber - 1) . " days", strtotime($today)));
        $dateEndWeek = date('Y-m-d', strtotime("+" . (7 - $dayOfWeekNumber) . " days", strtotime($today)));

        $query = ' SELECT c.name as Nombre, b.date_booking , b.number_booking
                   FROM classrooms c
                   LEFT JOIN bookings b ON c.id = b.id_classroom
                   WHERE b.date_booking BETWEEN "'.$dateStarWeek.'" AND "'.$dateEndWeek.'"';

        $results = $connection->select($query);

        $classrooms = [];
        foreach ($results as $row) {
            $nombre = $row->Nombre;

            if (!isset($classrooms[$nombre])) {
                $classrooms[$nombre] = [
                    'Nombre' => $nombre,
                    'bookings' => []
                ];
            }

            $classrooms[$nombre]['bookings'][] = [
                'date_booking' => $row->date_booking,
                'number_booking' => $row->number_booking
            ];

        }

        $classrooms = $this->createResult(array_values($classrooms));

        return response()->json($classrooms);
    }


    public function createResult($classrooms)
    {
        $result = [];

        foreach ($classrooms as $classroom) {

            switch ($classroom['Nombre']) {
                case 'Clase A':
                        $result[$classroom['Nombre']] = [
                            'days' => [
                                'Monday' => [9, 10, 11, 12, 13 , 14],
                                'Wednesday' => [9, 10, 11, 12, 13 , 14],
                            ]
                        ];

                        foreach ($classroom['bookings'] as $value) {

                            $dayOfBooking = date('l', strtotime($value['date_booking']));
                            $hourOfBooking = substr($value['date_booking'], 11, 2);

                            if (key_exists($dayOfBooking, $result[$classroom['Nombre']]['days'])) {
                                if (in_array($hourOfBooking, $result[$classroom['Nombre']]['days'][$dayOfBooking])) {
                                    $indice = array_search($hourOfBooking, $result[$classroom['Nombre']]['days'][$dayOfBooking]);
                                    unset($result[$classroom['Nombre']]['days'][$dayOfBooking][$indice]);

                                    $result[$classroom['Nombre']]['days'][$dayOfBooking] = array_values($result[$classroom['Nombre']]['days'][$dayOfBooking]);
                                }
                            }

                        }
                    break;

                case 'Clase B':
                        $result[$classroom['Nombre']] = [
                            'days' => [
                                'Monday' => [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
                                'Thursday' => [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
                                'Saturday' => [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
                            ]
                        ];

                        foreach ($classroom['bookings'] as $value) {

                            $dayOfBooking = date('l', strtotime($value['date_booking']));
                            $hourOfBooking = substr($value['date_booking'], 11, 2);

                            if (key_exists($dayOfBooking, $result[$classroom['Nombre']]['days'])) {
                                if (in_array($hourOfBooking, $result[$classroom['Nombre']]['days'][$dayOfBooking])) {
                                    $indice = array_search($hourOfBooking, $result[$classroom['Nombre']]['days'][$dayOfBooking]);
                                    unset($result[$classroom['Nombre']]['days'][$dayOfBooking][$indice]);

                                    $result[$classroom['Nombre']]['days'][$dayOfBooking] = array_values($result[$classroom['Nombre']]['days'][$dayOfBooking]);
                                }
                            }

                        }
                    break;

                case 'Clase C':
                        $result[$classroom['Nombre']] = [
                            'days' => [
                                'Tuesday' => [15, 16, 17, 18, 19, 20, 21, 22],
                                'Friday' => [15, 16, 17, 18, 19, 20, 21, 22],
                                'Saturday' => [15, 16, 17, 18, 19, 20, 21, 22],
                            ]
                        ];

                        foreach ($classroom['bookings'] as $value) {

                            $dayOfBooking = date('l', strtotime($value['date_booking']));
                            $hourOfBooking = substr($value['date_booking'], 11, 2);

                            if (key_exists($dayOfBooking, $result[$classroom['Nombre']]['days'])) {
                                if (in_array($hourOfBooking, $result[$classroom['Nombre']]['days'][$dayOfBooking])) {
                                    $indice = array_search($hourOfBooking, $result[$classroom['Nombre']]['days'][$dayOfBooking]);
                                    unset($result[$classroom['Nombre']]['days'][$dayOfBooking][$indice]);

                                    $result[$classroom['Nombre']]['days'][$dayOfBooking] = array_values($result[$classroom['Nombre']]['days'][$dayOfBooking]);
                                }
                            }

                        }
                    break;

                default:
                    break;
            }

        }

        return $result;
    }

    public function addBooking(Request $request, Connection $connection)
    {

        $classroomName = $request->input('classroom_name');
        $bookingDate = $request->input('booking_date');
        if (!$classroomName || !$bookingDate) {
            return response()->json(['error' => 'Please send the classroom name, and date'], 400);
        }

        $idClassroom = $connection->select('SELECT id FROM classrooms WHERE name = ? LIMIT 1', [$classroomName]);
        if (count($idClassroom) > 0) {
            $item = $idClassroom[0];
            $idClassroom = $item->id;
        } else {
            return response()->json(['message' => 'Classroom not found'], 404);
        }

        $booking = $connection->select('SELECT * FROM bookings WHERE id_classroom = ? AND date_booking = ?', [$idClassroom, $bookingDate]);

        if (count($booking) > 0) {
            $item = $booking[0];
            $aproveUpdateBooking = false;
            switch ($classroomName) {
                case 'Clase A':
                    if ($item->number_booking < 10) {
                        $aproveUpdateBooking = true;
                    }
                    break;

                case 'Clase B':
                    if ($item->number_booking < 15) {
                        $aproveUpdateBooking = true;
                    }
                    break;

                case 'Clase C':
                    if ($item->number_booking < 7) {
                        $aproveUpdateBooking = true;
                    }
                    break;

                default:

                    break;
            }

            if ($aproveUpdateBooking) {
                $numberBooking = $item->number_booking + 1;
                DB::update('UPDATE bookings SET number_booking = ? WHERE id = ?', [$numberBooking, $item->id]);
                return response()->json(['message' => 'Booking saved successful'], 201);
            } else {
                return response()->json(['message' => 'No more reservations can be made.'], 201);
            }

        }

        $aproveInsertBooking = false;
        switch ($classroomName) {
            case 'Clase A':
                $timestamp = strtotime($bookingDate);
                $dayOfWeek = date('N', $timestamp);
                $hour = date('G', $timestamp);

                if ($dayOfWeek >= 1 && $dayOfWeek <= 3) {
                    if ($hour >= 9 && $hour <= 14) {
                        $aproveInsertBooking = true;
                    }
                }

                break;

            case 'Clase B':
                $timestamp = strtotime($bookingDate);
                $dayOfWeek = date('N', $timestamp);
                $hour = date('G', $timestamp);

                if (in_array($dayOfWeek, [1, 4, 6])) {
                    if ($hour >= 8 && $hour <= 18) {
                        $aproveInsertBooking = true;
                    }
                }
                break;

            case 'Clase C':
                $timestamp = strtotime($bookingDate);
                $dayOfWeek = date('N', $timestamp);
                $hour = date('G', $timestamp);

                if (in_array($dayOfWeek, [2, 5, 6])) {
                    if ($hour >= 15 && $hour <= 22) {
                        $aproveInsertBooking = true;
                    }
                }
                break;

            default:

                break;
        }

        if ($aproveInsertBooking) {
            DB::insert('INSERT INTO bookings (number_booking, id_classroom, date_booking) VALUES (?, ?, ?)', [1, $idClassroom, $bookingDate]);

            return response()->json(['message' => 'Booking saved successfully.'], 201);
        } else {
            return response()->json(['message' => 'Reservations are not allowed on that date.'], 201);
        }

    }

    public function deleteBooking($id, Connection $connection)
    {

        $dateBooking = $connection->select('SELECT date_booking FROM bookings WHERE id = ? LIMIT 1', [$id]);

        if (count($dateBooking) > 0) {
            $item = $dateBooking[0];
            $dateBooking = $item->date_booking;
        } else {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $timestamp = strtotime($dateBooking);
        $currentTimestamp = time();
        $difference = $timestamp - $currentTimestamp;
        $differenceInHours = $difference / 3600;

        if ($differenceInHours <= 24 && $differenceInHours >= -24) {
            return response()->json(['message' => 'Cannot cancel before 24 hours.'], 404);
        } else {
            DB::delete('DELETE FROM bookings WHERE id = ?', [$id]);
        }

        return response()->json(['message' => 'Item deleted successfully'], 201);
    }
}
