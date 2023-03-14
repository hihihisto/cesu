<?php

namespace App\Http\Controllers;

use PDO;
use Illuminate\Http\Request;

class FhsisController extends Controller
{
    public function home() {

    }

    public function report() {
        // Set up a new PDO connection using the ODBC driver
        $mdb_location = storage_path('eFHSIS_be.mdb');
        
        $uname = explode(" ",php_uname());
        print_r($uname);
        $os = $uname[0];
        echo "<br>";
        echo $os;
        switch ($os){
        case 'Windows':
            $driver = '{Microsoft Access Driver (*.mdb, *.accdb)}';
            break;
        case 'Linux':
            $driver = 'MDBTools';
            break;
        default:
            exit("Don't know about this OS");
        }

        $dsn = "odbc:Driver=$driver;Dbq=$mdb_location";
        $username = ""; // leave blank if not required
        $password = ""; // leave blank if not required
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $pdo = new PDO($dsn, $username, $password, $options);

        // Query the database
        $mort_list = [];

        $year = date('Y');

        $stmt = $pdo->query("SELECT DISTINCT(DISEASE) FROM [MORT BHS] WHERE YEAR(DATE) = $year");
        while ($row = $stmt->fetch()) {
            $mort_list[] = $row;
        }

        return view('efhsis.report', [
            'mort_list' => $mort_list,
        ]);
    }
}
