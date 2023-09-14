<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;


if ($_ENV['MODE'] == 'dev'){
    $filePath = 'development.json';
    $data = file_get_contents($filePath);
    sleep(2);
    header('Content-Type: application/json; charset=utf-8');
    echo $data;
    exit();
}

$cookieJar = new FileCookieJar("/var/www/html/honeynet/src/cookie.txt", true);

$client = new Client(['cookies' => $cookieJar]);

$tanggal_dari = date("d-m-Y", strtotime($_POST['tanggal_dari']));
$tanggal_sampai = date("d-m-Y", strtotime($_POST['tanggal_sampai']));

$general_payload = [
    'days' => '30',
    'tanggal_dari' => $tanggal_dari ,
    'tanggal_sampai' => $tanggal_sampai
];

$jumlah_serangan = $client->get("https://portal-honeynet.bssn.go.id/honeynetadmin/getSeranganAll/{$general_payload['tanggal_dari']}/{$general_payload['tanggal_sampai']}")->getBody()->getContents();
$jumlah_serangan_malware = $client->get("https://portal-honeynet.bssn.go.id/honeynetadmin/getMalwareAll/{$general_payload['tanggal_dari']}/{$general_payload['tanggal_sampai']}")->getBody()->getContents();
$jumlah_malware_unik = $client->get("https://portal-honeynet.bssn.go.id/honeynetadmin/getMalwareDistinctAll/{$general_payload['tanggal_dari']}/{$general_payload['tanggal_sampai']}")->getBody()->getContents();
$jumlah_ip_unik = $client->get("https://portal-honeynet.bssn.go.id/honeynetadmin/getIPDistinct/{$general_payload['tanggal_dari']}/{$general_payload['tanggal_sampai']}")->getBody()->getContents();


// Top chart IP
$req_top_chart_ip = $client->post("https://portal-honeynet.bssn.go.id/honeynetadmin/topChartIP", [
    'form_params' => $general_payload
])->getBody()->getContents();

$json_req_top_chart_ip = json_decode($req_top_chart_ip, true);
$height = $json_req_top_chart_ip['value'];
$bars = $json_req_top_chart_ip['label'];

$table_ip = "
<div class='hn-wrap'>
        <div class='hn-title'>TOP ATTACKER IP</div>
        <div class='hn-content' style='height:291px'>
            <table class='table'>
                <thead>
                    <tr>
                        <td>No</td>
                        <td>IP</td>
                        <td>Jumlah</td>
                    </tr>
                </thead>
                <tbody>";
            $i = 0;
            foreach ($bars as $x) {
                $table_ip .= "<tr>";
                $table_ip .= "<td>" . strval($i + 1) . "</td>";
                $table_ip .= "<td>" . strval($x) . "</td>";
                $table_ip .= "<td>" . strval($height[$i]) . "</td>";
                $table_ip .= "</tr>";
                $i = $i + 1;
            }
            $table_ip .= "</tbody>
            </table>
        </div>
    </div>
";

$chart_data = [
    'labels' => $bars,
    'datasets' => [[
        'label' => 'Jumlah Serangan',
        'data' => $height,
        'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
        'borderColor' => 'rgba(75, 192, 192, 1)',
        'borderWidth' => 1
    ]]
];

//Top country
$req_top_country = $client->get("https://portal-honeynet.bssn.go.id/honeynetadmin/getDataFilterTopChartCountry/30/{$tanggal_dari}/{$tanggal_sampai}/?draw=3&columns%5B0%5D%5Bdata%5D=0&columns%5B0%5D%5Bname%5D=&columns%5B0%5D%5Bsearchable%5D=true&columns%5B0%5D%5Borderable%5D=false&columns%5B0%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B0%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B1%5D%5Bdata%5D=1&columns%5B1%5D%5Bname%5D=&columns%5B1%5D%5Bsearchable%5D=true&columns%5B1%5D%5Borderable%5D=false&columns%5B1%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B1%5D%5Bsearch%5D%5Bregex%5D=false&columns%5B2%5D%5Bdata%5D=2&columns%5B2%5D%5Bname%5D=&columns%5B2%5D%5Bsearchable%5D=true&columns%5B2%5D%5Borderable%5D=false&columns%5B2%5D%5Bsearch%5D%5Bvalue%5D=&columns%5B2%5D%5Bsearch%5D%5Bregex%5D=false&start=0&length=5&search%5Bvalue%5D=&search%5Bregex%5D=false")->getBody()->getContents();
$req_top_country_json = json_decode($req_top_country, true);

$data = $req_top_country_json['data'];

$table_top_country = "
<div class='hn-wrap'>
        <div class='hn-title'>TOP COUNTRY ATTACKER</div>
        <div class='hn-content' style='height:291px'>
            <table class='table'>
                <thead>
                    <tr>
                        <td>No</td>
                        <td>Negara</td>
                        <td>Jumlah</td>
                    </tr>
                </thead>
                <tbody>";

                foreach ($data as $x) {
                    $i = 0;
                    $table_top_country .= "<tr>";
                    foreach ($x as $y) {
                        if ($i == 1) {
                            $y = str_replace("resources", "https://portal-honeynet.bssn.go.id/honeynetadmin/resources", $y);
                            $y = str_replace("5px;", "5px;width:20px;", $y);
                        }
                        $table_top_country .= "<td>" . $y . "</td>";
                        if ($i == 2) {
                            $table_top_country .= "</tr>";
                        }
                        $i++;
                    }
                }

                $table_top_country .= "
            </table>
            </div>
        </div>";


//world country
$req_map_top_country = $client->post("https://portal-honeynet.bssn.go.id/honeynetadmin/worldAttackAll", [
    'form_params' => $general_payload
])->getBody()->getContents();

$map_top_country = '<script>
        xx = '.$req_map_top_country .';
        //console.log(xx);
        new jvm.Map({
            container: $("#map"),
            map: "world_mill",
            markers: [],
            series: {
                regions: [{
                    values: xx,
                    scale: ["#FFFFFF", "#FF0000"],
                    normalizeFunction: "polynomial",
                    legend: {
                        vertical: true
                    }
                }]
            },
        });
        </script>';

//daily stats
$req_daily_attack = $client->post("https://portal-honeynet.bssn.go.id/honeynetadmin/dailychartline", [
    'form_params' => $general_payload
])->getBody()->getContents();

$req_daily_attack_json = json_decode($req_daily_attack, true);

$names = $req_daily_attack_json['label'];
$size = $req_daily_attack_json['value'];

// Menghitung statistik
$max_serangan_val = max($size);
$min_serangan_val = min($size);

$max_serangan_key = array_search($max_serangan_val, $size);
$min_serangan_key = array_search($min_serangan_val, $size);

$get_max_date = date("d-m-Y", strtotime($names[$max_serangan_key]));
$get_min_date = date("d-m-Y", strtotime($names[$min_serangan_key]));

// Menghitung rata-rata
$get_avg = round(array_sum($size) / count($size), 2);

$daily_attack = "
<div class='hn-wrap'>
    <div class='hn-title'>STATISTIK SERANGAN HARIAN</div>
    <div class='hn-content'>
      <p>Berikut merupakan serangan yang terekam pada tanggal {$tanggal_dari} hingga {$tanggal_sampai}:
          <ul>
            <li>Serangan terbanyak pada tanggal {$get_max_date} yaitu sebanyak {$max_serangan_val} serangan</li>
            <li>Serangan terendah pada tanggal {$get_min_date} yaitu sebanyak {$min_serangan_val} serangan</li>
            <li>Rata-rata sebanyak {$get_avg} serangan per hari</li>
          </ul>
      </p>
      <canvas id=\"daily_attack_chart\" width=\"100%\" style=\"max-height:250px\"></canvas>
    </div>
</div>";


//malware dropping
$req_malware = $client->post("https://portal-honeynet.bssn.go.id/honeynetadmin/dailychartlineMalware", [
    'form_params' => $general_payload
])->getBody()->getContents();

$json_malware_dropping = json_decode($req_malware, true);

$names = $json_malware_dropping['label'];
$size = $json_malware_dropping['value'];

// statistik malware
$max_serangan_val = max($size);
$min_serangan_val = min($size);

$max_serangan_key = array_search($max_serangan_val, $size);
$min_serangan_key = array_search($min_serangan_val, $size);

$get_max_date = date("d-m-Y", strtotime($names[$max_serangan_key]));
$get_min_date = date("d-m-Y", strtotime($names[$min_serangan_key]));

// Menghitung rata-rata
$get_avg = round(array_sum($size) / count($size), 2);

$malware_dropping = "
      <div class='hn-wrap'>
        <div class='hn-title'>STATISTIK MALWARE DROPPING HARIAN</div>
        <div class='hn-content'>
          <p>Berikut merupakan malware yang terekam pada tanggal {$tanggal_dari} hingga {$tanggal_sampai}:
              <ul>
                  <li>Malware dropping terbanyak pada tanggal {$get_max_date} yaitu sebanyak {$max_serangan_val} serangan</li>
                  <li>Malware dropping terendah pada tanggal {$get_min_date} yaitu sebanyak {$min_serangan_val} serangan</li>
                  <li>Rata-rata sebanyak {$get_avg} malware per hari</li>
              </ul>
          </p>
          <canvas id=\"malware_dropping_chart\" width=\"100%\" style=\"max-height:250px\"></canvas>
        </div>
    </div>";

//Top malware

$req_top_malware = $client->post("https://portal-honeynet.bssn.go.id/honeynetadmin/getTopMalware", [
    'form_params' => $general_payload
])->getBody()->getContents();

$req_top_malware_json = json_decode($req_top_malware, true);

$label = $req_top_malware_json['label'];
$value = $req_top_malware_json['value'];

$table_malware = "
<div class='hn-wrap'>
    <div class='hn-title'>TOP MALWARE</div>
        <div class='hn-content'>
        <table class='table'>
            <thead>
                <tr>
                    <td>No</td>
                    <td>Malware</td>
                    <td>Jumlah</td>
                </tr>
            </thead>
            <tbody>";

        $i = 0;
        foreach ($label as $key => $lbl) {
            $table_malware .= "<tr>";
            $table_malware .= "<td>" . ($key + 1) . "</td>";
            $table_malware .= "<td>" . $lbl . "</td>";
            $table_malware .= "<td>" . $value[$key] . "</td>";
            $table_malware .= "</tr>";
        }

        $table_malware .= "
            </tbody>
        </table>
        </div>
    </div>";


$req_top_chart_port = $client->post("https://portal-honeynet.bssn.go.id/honeynetadmin/topChartPort", [
    'form_params' => $general_payload
])->getBody()->getContents();

$json_req_top_chart_port = json_decode($req_top_chart_port, true);
$size = $json_req_top_chart_port['value'];
$names = $json_req_top_chart_port['label'];

$top_port_one = $names[0];

$table_port = "
<div class='hn-wrap'>
    <div class='hn-title'>TOP PORT</div>
        <div class='hn-content'>
        <table class='table'>
    <thead>
        <tr>
            <td>No</td>
            <td>Port</td>
            <td>Jumlah</td>
        </tr>
    </thead>
    <tbody>";

$i = 0;
foreach ($names as $x) {
    $table_port .= "<tr>";
    $table_port .= "<td>" . strval($i + 1) . "</td>";
    $table_port .= "<td>" . strval($x) . "</td>";
    $table_port .= "<td>" . strval($size[$i]) . "</td>";
    $table_port .= "</tr>";
    $i = $i + 1;
}
$table_port .= "</tbody>
        </table>
        </div>
    </div>";



#info
$resume_data = "
    <div class='hn-wrap'>
        <div class='hn-title'>INFORMASI UMUM</div>
        <div class='hn-content' style='height:200px'>
        Periode: {$tanggal_dari} / {$tanggal_sampai}
        <br>
        <br>
        Jumlah serangan: <span>{$jumlah_serangan}</span><br>
        Jumlah dropping malware: <span>{$jumlah_serangan_malware}</span><br>
        Jumlah malware unik: <span>{$jumlah_malware_unik}</span><br>
        Jumlah ip unik: <span>{$jumlah_ip_unik }</span><br>
        Port terbanyak yang diserang: <span>{$top_port_one}</span><br>
        </div>
    </div>
    ";

// response
$response = [
  'resume_data' => [$resume_data],
  'top_ip' => $table_ip,
  'chart_data' => $chart_data,
  'top_country' => $table_top_country,
  'map_top_country' => $map_top_country,
  'daily_attack' => $daily_attack,
  'json_daily_attack' => $req_daily_attack_json,
  'malware_dropping' => $malware_dropping,
  'json_malware_dropping' => $json_malware_dropping,
  'top_malware' => $table_malware,
  'top_port' => $table_port,
  'top_port_one' => $top_port_one
];

$all = json_encode($response);

$filePath = 'result.json';
file_put_contents($filePath, $all);

header('Content-Type: application/json; charset=utf-8');
echo $all;
