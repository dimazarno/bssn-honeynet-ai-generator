<?php
require '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://portal-honeynet.bssn.go.id/honeynetadmin/resources/mar/jvectormaps/jquery-jvectormap-2.0.3.min.js"></script>
    <link   href="https://portal-honeynet.bssn.go.id/honeynetadmin/resources/mar/jvectormaps/jquery-jvectormap-2.0.3.css"   rel="stylesheet">
    <script src="https://portal-honeynet.bssn.go.id/honeynetadmin/resources/mar/jvectormaps/jquery-jvectormap-asia-mill.js"></script>
    <script src="https://portal-honeynet.bssn.go.id/honeynetadmin/resources/mar/jvectormaps/jquery-jvectormap-world-mill.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-adapter-moment/1.0.0/chartjs-adapter-moment.min.js"></script>
    <link href="./template.css" rel="stylesheet">
</head>
<body>
    <div class="container" style="margin-top:20px">
        <h1 style="font-size: 20px;">Dashboard</h1>
        <div class="row" style="margin-top:20px;">
            <div class="col-md-3" style="margin-bottom:20px;padding:10px;border:1px solid black;background-color: #f7f4e4;"> <!-- Kolom kiri (25%) -->
                <p style="font-size:18px;font-weight: bold;">Filter Periode Pencarian</p>
                <form id="dateForm">
                    <div class="mb-3">
                        <label for="tanggal_dari" class="form-label">DARI</label>
                        <input type="date" class="form-control" id="tanggal_dari" name="tanggal_dari" value="2023-08-01">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_sampai" class="form-label">HINGGA</label>
                        <input type="date" class="form-control" id="tanggal_sampai" name="tanggal_sampai" value="2023-08-31">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>

                <p style="margin-top:30px;font-size:18px;font-weight: bold;">AI Generator</p>
                <form id="aiForm">
                    <div class="mb-3">
                        <label for="password" class="form-label">PIN CODE</label>
                        <input type="password" class="form-control" id="pincode" name="pincode">
                    </div>
                    <button type="submit" id="btnAI" class="btn btn-primary">Generate</button>
                </form>

                <p style="margin-top:30px;font-size:18px;font-weight: bold;">Tools</p>
                <button class="btn btn-primary" id="toggleButton">Aktifkan Sortable</button>
                <button id="printButton" class="btn btn-primary">Print Preview</button>
            </div>

            <div class="col-md-9" style="padding-left: 20px;"> <!-- Kolom kanan (75%) -->
                <div id="report_area" style="margin-bottom:20px" contenteditable="true">
                    <div id="kop" style="padding-bottom:10px;border-bottom: 4px solid black;">
                        <center>
                            <table border=0>
                                <tr>
                                    <td><img src="<?php echo $_ENV['LOGO'];?>" height="90px" style="margin-right:15px"></td>
                                    <td>
                                        <h3><?php echo $_ENV['H3'];?></h3>
                                        <h4><?php echo $_ENV['H4'];?></h4>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </div>

                    <div id="parent_resume_data" class="row col-md-12" style="margin-top: 20px;">
                        <div id="resume_data" class="col-md-5" style="max-width: 38%;">
                          
                        </div>
                        <div id="honeynet" class="col-md-7" style="max-width: 60%;">
                            <div class='hn-wrap'>
                                <div class='hn-title'>
                                    HONEYNET
                                </div>
                                <div class='hn-content' style="height:200px">
                                    <?php echo $_ENV['HONEYNET'];?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="parent_top_id" class="row col-md-12" style="margin-top: 20px;">
                        <div id="top_ip" class="col-md-5" style="max-width: 38%;">
                          
                        </div>
                        <div id="chart_top_ip" class="col-md-7"  style="max-width: 60%;">
                            <div class='hn-wrap'>
                                <div class='hn-content-only' style="height:330px">
                                    <center style="height:330px">
                                        <canvas id="topIPChart" width="100%" height="100%"></canvas>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="parent_top_country" class="row col-md-12" style="margin-top: 20px;">
                        <div id="top_country" class="col-md-5" style="max-width: 38%;">
                          
                        </div>
                        <div class="col-md-7"  style="max-width: 60%;">
                            <div class='hn-wrap' style="width:100%;height:100%">
                                <div class='hn-content-only' style="width:100%;height:330px">
                                    <div id="map" style="width:100%;height:100%"></div>
                                </div>
                            </div>
                        </div>
                        <div id="map_top_country"></div>
                    </div>
                    
                    <div id="daily_attack_chart_parent" class="row col-md-12" style="margin-top: 20px;">
                        <div id="daily_attack_info" class="row col-md-12"></div>
                    </div>

                    <div id="malware_parent" class="row col-md-12" style="margin-top: 20px;">
                        <div id="malware_dropping_info" class="row col-md-12"></div>
                    </div>

                    <div id="parent_top_malware" class="row col-md-12" style="margin-top: 20px;">
                        <div id="top_malware" class="col-md-6" style="max-width: 50%;">
                          
                        </div>
                        <div id="top_port" class="col-md-6" style="max-width: 48%;">
                          
                        </div>
                    </div>

                    <div id="summary" class="row col-md-12" style="margin-top: 20px;" contenteditable="true">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <center style="position: relative; top: 50%;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </center>
    </div>

    <script>
        var myChart; 
        var dailyAttackChart; 
        var MalwareDroppingChart; 

        $(document).ready(function() {
            $("#report_area").hide();
            $("#dateForm").submit(function(event) {
                event.preventDefault();
                $("#loading").show();
                const tanggalDari = $("#tanggal_dari").val();
                const tanggalSampai = $("#tanggal_sampai").val();
                fetchData(tanggalDari, tanggalSampai);
            });

            $('#printButton').click(function() {
                window.print();
            });
            
            $('#aiForm').submit(function(event) {
                event.preventDefault();
                $("#loading").show();

                $.post("ai.php", {
                    pincode: $("#pincode").val()
                }, function(data) {
                    if (data == 'salah') {
                        alert('Pin anda salah.');
                    } else {
                        $("#summary").html(data.analysis_output);
                    }                
                    $("#loading").hide();
                });
            });

            let isSortableActive = false;

            $("#toggleButton").on("click", function() {
              if (isSortableActive) {
                // Menonaktifkan sortable
                $("#report_area").sortable("disable");
                $(this).text("Aktifkan Sortable");
              } else {
                // Mengaktifkan sortable
                $("#report_area").sortable("enable");
                $(this).text("Nonaktifkan Sortable");
              }
              
              isSortableActive = !isSortableActive;
            });

            // Inisialisasi sortable tapi nonaktifkan dulu
            $("#report_area").sortable();
            $("#report_area").sortable("disable");

        });
     
        function fetchData(tanggalDari, tanggalSampai) {
            $.post("script.php", {
                tanggal_dari: tanggalDari,
                tanggal_sampai: tanggalSampai
            }, function(data) {
                console.log(data);
                $("#report_area").show();

                $("#resume_data").html(data.resume_data);
                $("#top_ip").html(data.top_ip);
                $("#top_country").html(data.top_country);
                $("#top_malware").html(data.top_malware);
                $("#top_port").html(data.top_port);
                $("#daily_attack_info").html(data.daily_attack);
                $("#malware_dropping_info").html(data.malware_dropping);
                $('#map').empty();
                $("#map_top_country").html(data.map_top_country);

                if (myChart !== null && typeof myChart !== "undefined") {
                    myChart.destroy();  
                }
                var canvasElem = document.getElementById('topIPChart');
                if (canvasElem) {
                    var ctx = canvasElem.getContext('2d');
                    myChart = new Chart(ctx, {
                        type: 'bar',
                        data: data.chart_data
                    });
                }

                if (dailyAttackChart !== null && typeof dailyAttackChart !== "undefined") {
                    dailyAttackChart.destroy();  
                }
                var jsonDailyAttack = data.json_daily_attack;  
                var canvasElemDailyAttack = document.getElementById('daily_attack_chart');
                if (canvasElemDailyAttack) {
                    var ctxDailyAttack = canvasElemDailyAttack.getContext('2d');
                    dailyAttackChart = new Chart(ctxDailyAttack, {
                        type: 'line',
                        data: {
                            labels: jsonDailyAttack.label,
                            datasets: [{
                                label: 'Serangan Harian',
                                data: jsonDailyAttack.value,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(0, 0, 0, 0)'
                            }]
                        },
                        options: {
                            scales: {
                                x: {
                                    type: 'time',
                                    time: {
                                        unit: 'day'
                                    }
                                },
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }


                if (MalwareDroppingChart !== null && typeof MalwareDroppingChart !== "undefined") {
                    MalwareDroppingChart.destroy();
                }
                var jsonMalwareDropping = data.json_malware_dropping;  
                var canvasElemMalwareDropping = document.getElementById('malware_dropping_chart');
                if (canvasElemMalwareDropping) {
                    var ctxMalwareDropping = canvasElemMalwareDropping.getContext('2d');
                    MalwareDroppingChart = new Chart(ctxMalwareDropping, {
                        type: 'line',
                        data: {
                            labels: jsonMalwareDropping.label,
                            datasets: [{
                                label: 'Malware Dropping Harian',
                                data: jsonMalwareDropping.value,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(0, 0, 0, 0)'
                            }]
                        },
                        options: {
                            scales: {
                                x: {
                                    type: 'time',
                                    time: {
                                        unit: 'day'
                                    }
                                },
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                $("#loading").hide();
            });
        }

        
    </script>
</body>
</html>
