<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TembakData extends Model
{
    /**
     * Mendapatkan data customer dari dua database dalam rentang tanggal tertentu.
     */
public static function getCustomers($start, $end, $cabang, $waktu)
{
    // Deklarasi koneksi berdasarkan ID cabang
    $connections = [
        2 => ['pagi' => 'bintaropagi', 'malam' => 'bintaromalam'],
        4 => ['pagi' => 'lbpagi', 'malam' => 'lbmalam'],
        5 => ['pagi' => 'alsutpagi', 'malam' => 'alsutmalam'],
        6 => ['pagi' => 'gading'],
        7 => ['pagi' => 'tb'],
        9 => ['pagi' => 'gatsu'],
        11 => ['pagi' => 'margondapagi', 'malam' => 'margondamalam'],
        12 => ['pagi' => 'bogorpagi', 'malam' => 'bogormalam'],
        13 => ['pagi' => 'sutamipagi', 'malam' => 'sutamimalam'],
        14 => ['pagi' => 'cilakipagi', 'malam' => 'cilakimalam'],
        15 => ['pagi' => 'puripagi', 'malam' => 'purimalam'],
        19 => ['pagi' => 'bekasipagi', 'malam' => 'bekasimalam'],
        20 => ['pagi' => 'amperapagi', 'malam' => 'amperamalam'],
        21 => ['pagi' => 'kuncitpagi', 'malam' => 'kuncitmalam'],
        26 => ['pagi' => 'jbtowerpagi', 'malam' => 'jbtowermalam'],
        27 => ['pagi' => 'sunterpagi', 'malam' => 'suntermalam'],
    ];

    if (!isset($connections[$cabang])) {
        throw new \Exception("Koneksi database untuk cabang ID $cabang tidak ditemukan.");
    }

    // Mendapatkan koneksi pagi atau malam berdasarkan waktu
    $dbConnection = isset($connections[$cabang][$waktu]) ? DB::connection($connections[$cabang][$waktu]) : null;

    if (!$dbConnection) {
        throw new \Exception("Koneksi untuk waktu '$waktu' pada cabang ID $cabang tidak ditemukan.");
    }

    // Query SQL untuk mengambil data customer
    $sql = "
    SELECT c.*
    FROM sh_m_customer c
    JOIN sh_t_transactions t ON c.id = t.id_customer
    WHERE t.cabang = ? 
    AND LEFT(c.create_date, 10) >= ? 
    AND LEFT(c.create_date, 10) <= ?";

    $params = [$cabang, $start, $end];
    // Eksekusi query berdasarkan koneksi yang sesuai
    $result = $dbConnection->select($sql, $params);

    return $result;
}
public static function getTransactions($start, $end, $cabang, $waktu)
{
    // Deklarasi koneksi berdasarkan ID cabang
    $connections = [
        2 => ['pagi' => 'bintaropagi', 'malam' => 'bintaromalam'],
        4 => ['pagi' => 'lbpagi', 'malam' => 'lbmalam'],
        5 => ['pagi' => 'alsutpagi', 'malam' => 'alsutmalam'],
        6 => ['pagi' => 'gading'],
        7 => ['pagi' => 'tb'],
        9 => ['pagi' => 'gatsu'],
        11 => ['pagi' => 'margondapagi', 'malam' => 'margondamalam'],
        12 => ['pagi' => 'bogorpagi', 'malam' => 'bogormalam'],
        13 => ['pagi' => 'sutamipagi', 'malam' => 'sutamimalam'],
        14 => ['pagi' => 'cilakipagi', 'malam' => 'cilakimalam'],
        15 => ['pagi' => 'puripagi', 'malam' => 'purimalam'],
        19 => ['pagi' => 'bekasipagi', 'malam' => 'bekasimalam'],
        20 => ['pagi' => 'amperapagi', 'malam' => 'amperamalam'],
        21 => ['pagi' => 'kuncitpagi', 'malam' => 'kuncitmalam'],
        26 => ['pagi' => 'jbtowerpagi', 'malam' => 'jbtowermalam'],
        27 => ['pagi' => 'sunterpagi', 'malam' => 'suntermalam'],
    ];

    if (!isset($connections[$cabang])) {
        throw new \Exception("Koneksi database untuk cabang ID $cabang tidak ditemukan.");
    }

    // Tentukan koneksi berdasarkan waktu
    $dbConnection = isset($connections[$cabang][$waktu]) ? DB::connection($connections[$cabang][$waktu]) : null;

    if (!$dbConnection) {
        throw new \Exception("Koneksi untuk waktu '$waktu' pada cabang ID $cabang tidak ditemukan.");
    }

    // Query SQL untuk mengambil data transaksi
    $sql = "SELECT * FROM sh_t_transactions 
            WHERE LEFT(create_date, 10) >= ? 
            AND LEFT(create_date, 10) <= ?";

    // Eksekusi query berdasarkan koneksi yang sesuai
    $result = $dbConnection->select($sql, [$start, $end]);

    return $result;
}

public static function getDetails($start, $end, $cabang, $waktu)
{
    // Tentukan koneksi berdasarkan ID cabang
    $connections = [
        2 => ['pagi' => 'bintaropagi', 'malam' => 'bintaromalam'],
        4 => ['pagi' => 'lbpagi', 'malam' => 'lbmalam'],
        5 => ['pagi' => 'alsutpagi', 'malam' => 'alsutmalam'],
        6 => ['pagi' => 'gading'],
        7 => ['pagi' => 'tb'],
        9 => ['pagi' => 'gatsu'],
        11 => ['pagi' => 'margondapagi', 'malam' => 'margondamalam'],
        12 => ['pagi' => 'bogorpagi', 'malam' => 'bogormalam'],
        13 => ['pagi' => 'sutamipagi', 'malam' => 'sutamimalam'],
        14 => ['pagi' => 'cilakipagi', 'malam' => 'cilakimalam'],
        15 => ['pagi' => 'puripagi', 'malam' => 'purimalam'],
        19 => ['pagi' => 'bekasipagi', 'malam' => 'bekasimalam'],
        20 => ['pagi' => 'amperapagi', 'malam' => 'amperamalam'],
        21 => ['pagi' => 'kuncitpagi', 'malam' => 'kuncitmalam'],
        26 => ['pagi' => 'jbtowerpagi', 'malam' => 'jbtowermalam'],
        27 => ['pagi' => 'sunterpagi', 'malam' => 'suntermalam'],
    ];

    if (!isset($connections[$cabang])) {
        throw new \Exception("Koneksi database untuk cabang ID $cabang tidak ditemukan.");
    }

    // Tentukan koneksi berdasarkan waktu
    $dbConnection = isset($connections[$cabang][$waktu]) ? DB::connection($connections[$cabang][$waktu]) : null;

    if (!$dbConnection) {
        throw new \Exception("Koneksi untuk waktu '$waktu' pada cabang ID $cabang tidak ditemukan.");
    }

    // Query SQL untuk mengambil detail transaksi
    $sql = "SELECT dt.id as idd, t.create_date, dt.* 
            FROM sh_t_transaction_details dt
            INNER JOIN sh_t_transactions t ON t.id = dt.id_trans
            WHERE LEFT(t.create_date, 10) >= ? 
            AND LEFT(t.create_date, 10) <= ?";

    // Eksekusi query berdasarkan koneksi yang sesuai
    $result = $dbConnection->select($sql, [$start, $end]);

    return $result;
}



}
