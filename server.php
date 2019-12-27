<?php
// http://www.fit4php.net/funktionsbibliothek/xml-und-json/rest-schnittstellen/
// https://www.leaseweb.com/labs/2015/10/creating-a-simple-rest-api-in-php/
// https://www.codeofaninja.com/2017/02/create-simple-rest-api-in-php.html
// https://www.roytuts.com/crud-example-using-sql-scripts-in-php-and-sqlite3/
// https://poe-php.de/tutorial/rest-einfuehrung-in-die-api-erstellung/4


// curl -X GET "https://api.adcore.de/server.php/?v1/user?id=5"
// curl -X PUT "https://api.adcore.de/server.php/?v1/user" -d '{"id":"5","username":"stefan"}'
// curl -X DELETE "https://api.adcore.de/server.php/?v1/user?id=5"

// curl -X POST "https://api.adcore.de/server.php/?v1/piko42" -d '{"current":"159","total":"17907","daily":"2.73","voltage":"497","power":"0.38","availability":"online"}'

// curl -X POST "https://api.adcore.de/server.php/?v1/strom" -d '{"Time":"2019-12-15T15:38:02","HTU21":{"Temperature":24.0,"Humidity":38.6},"SML":{"Total_in":20481.9505,"Total_out":10898.4095,"Power_curr":281,"Meter_number":"0901454d4800004735c7"},"Gas":{"Count":0.14},"TempUnit":"C"}'

// ---------------------------------------------------------------------------------------------------
// init
// ---------------------------------------------------------------------------------------------------
$smarthomedb = '/home/mbx/varwww/dashboard.adcore.de/smarthome.db';

// ---------------------------------------------------------------------------------------------------
// required headers
// ---------------------------------------------------------------------------------------------------
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// get the HTTP method, path and body of the request
// https://www.php.net/manual/en/reserved.variables.server.php
$method = $_SERVER['REQUEST_METHOD'];
// REQUEST_URI - /server.php/?v1/piko42
// QUERY_STRING - v1/piko42
$request = explode('/', trim($_SERVER['QUERY_STRING'],'/'));
$daten = file_get_contents('php://input');
$input = json_decode(file_get_contents('php://input'), true);

// file_put_contents('daten.log', $daten.'\n', FILE_APPEND | LOCK_EX);
// file_put_contents('request.log', $request.'\n', FILE_APPEND | LOCK_EX);

$response_code = 503;

// create SQL based on HTTP method
switch ($method) {
  case 'GET':
    $response = 'GET is not implemented yet!';
    // set response code - 503 service unavailable
    $response_code = 503;
    //$sql = "select * from `$table`".($key?" WHERE id=$key":'');
    break;
  case 'PUT':
    $response = 'PUT is not implemented yet!';
    // set response code - 503 service unavailable
    $response_code = 503;
    //$sql = "update `$table` set $set where id=$key";
    break;
  case 'POST':
    $response = 'ok.';
    // set response code - 201 created
    $response_code = 201;

    // https://www.php.net/manual/de/function.date.php
    $timestamp = date('Y-m-d H:i:s'); // %Y-%m-%d %H:%M:%S // 2019-11-17 16:07:16
    $timestampM = date('Y-m-d'); // %Y-%m-%d %H:%M:%S // 2019-11-17 16:07:16
    if ($request[1] == "piko42"){
        $sql = "INSERT INTO PIKO42 (TIMESTAMP, CURRENT, TOTAL, DAILY, VOLTAGE, POWER) VALUES ('".$timestamp."',".$input['current'].",".$input[
'total'].",".$input['daily'].",".$input['voltage'].",".$input['power'].");";
        $sqlcurrent = "UPDATE 'CURRENT' SET 'PVCURRENT'=".$input['current'].", 'PVDAILY'=".$input['daily']." , 'PVTOTAL'=".$input[
'total']." WHERE 'TIMESTAMP'=".$timestampM.";";
    }
    if ($request[1] == "strom"){
        $sql = "INSERT INTO STROM (TIMESTAMP, V180, V280, V167) VALUES ('".$timestamp."',".$input['SML']['Total_in'].",".$input['SML']['Total_out'].",".$input['SML']['Power_curr'].");";
        $sqlcurrent = "UPDATE 'CURRENT' SET 'V180'=".$input['SML']['Total_in'].", 'V280'=".$input['SML']['Total_out']." WHERE 'TIMESTAMP'=".$timestampM.";";
    }
    $response = doSQL($sql);
    break;
  case 'DELETE':
    $response = 'DELETE is not implemented yet!';
    // set response code - 503 service unavailable
    $response_code = 503;
    //$sql = "delete `$table` where id=$key";
    break;
}

http_response_code($response_code);
echo json_encode(array("message" => $response))."\n";

// echo ("\n---------------------------------\n");
// echo json_encode(array("method" => $method))."\n";
// echo json_encode(array("request" => $request))."\n";
// echo json_encode(array("daten" => $daten))."\n";
// echo json_encode(array("input" => $input))."\n";
// echo json_encode(array("sql" => $sql))."\n";
// echo server_vars();
// echo ("\n---------------------------------\n");

function doSQL($sql){
    global $smarthomedb;
    // connect to the sqlite database
    $db = new SQLite3($smarthomedb);


    // tables "piko42" or "strom"
    $ret = $db->exec($sql);
    if(!$ret){
        $response .= $db->lastErrorMsg();
    } else {
        $response .= $db->changes()." done successfully";
    }
    //file_put_contents('sql.log', $sql.'-->'.$response.'\r\n', FILE_APPEND | LOCK_EX);

/*    

    // INSERT OR IGNORE INTO book(id) VALUES(1001);
    $ret = $db->exec("INSERT INTO 'CURRENT' ('TIMESTAMP','V180','V280','PVCURRENT','PVDAILY','PVTOTAL') VALUES ('".$ts."',NULL,NULL,NULL,NULL,NULL);");
    //file_put_contents('sql.log', 'INSERT INTO CURRENT-->'.$ret.'\r\n', FILE_APPEND | LOCK_EX);

    // table "current"
    $ret = $db->exec($sqlcurrent);
    if(!$ret){
        $response .= $db->lastErrorMsg();
    } else {
        $response .= $db->changes()." done successfully";
    }    
    //file_put_contents('sql.log', $sqlcurrent.'-->'.$response.'\r\n', FILE_APPEND | LOCK_EX);
*/

    $db->close();

    return $response;
}

function server_vars(){
    $indicesServer = array('PHP_SELF', 
    'argv', 
    'argc', 
    'GATEWAY_INTERFACE', 
    'SERVER_ADDR', 
    'SERVER_NAME', 
    'SERVER_SOFTWARE', 
    'SERVER_PROTOCOL', 
    'REQUEST_METHOD', 
    'REQUEST_TIME', 
    'REQUEST_TIME_FLOAT', 
    'QUERY_STRING', 
    'DOCUMENT_ROOT', 
    'HTTP_ACCEPT', 
    'HTTP_ACCEPT_CHARSET', 
    'HTTP_ACCEPT_ENCODING', 
    'HTTP_ACCEPT_LANGUAGE', 
    'HTTP_CONNECTION', 
    'HTTP_HOST', 
    'HTTP_REFERER', 
    'HTTP_USER_AGENT', 
    'HTTPS', 
    'REMOTE_ADDR', 
    'REMOTE_HOST', 
    'REMOTE_PORT', 
    'REMOTE_USER', 
    'REDIRECT_REMOTE_USER', 
    'SCRIPT_FILENAME', 
    'SERVER_ADMIN', 
    'SERVER_PORT', 
    'SERVER_SIGNATURE', 
    'PATH_TRANSLATED', 
    'SCRIPT_NAME', 
    'REQUEST_URI', 
    'PHP_AUTH_DIGEST', 
    'PHP_AUTH_USER', 
    'PHP_AUTH_PW', 
    'AUTH_TYPE', 
    'PATH_INFO', 
    'ORIG_PATH_INFO') ; 
    
    echo '\n' ; 
    foreach ($indicesServer as $arg) { 
        if (isset($_SERVER[$arg])) { 
            echo $arg.' - ' . $_SERVER[$arg] . '\n' ; 
        } 
        else { 
            echo 'null - '.$arg.'\n' ; 
        } 
    } 
    echo '</table>' ; 
}

/*
switch ($method) {
  case 'GET':
    if ($dateiname == NULL)
    { // Verzeichnis auflisten
      foreach (scandir('files/') as $datei)
      {
        if(substr($datei,-strlen(".txt")) == ".txt")
            echo ("<a href='$datei'> $datei</a><br>");
      }
    } else
        echo (file_get_contents('files/'.$dateiname));
    break;
  case 'POST':
    file_put_contents('files/'.$dateiname, $daten);
    break;
  case 'PUT':
    file_put_contents('files/'.$dateiname, $daten, FILE_APPEND);
    break;
  case 'DELETE':
    unlink('files/'.$dateiname);
    break;
}
*/
?>

