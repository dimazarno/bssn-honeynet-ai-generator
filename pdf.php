<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

require '../vendor/autoload.php';

use Mpdf\Mpdf;

if (isset($_POST['generate_pdf'])) {
    // Buat instance mPDF
    $mpdfTmpDir = __DIR__ . '/tmp'; // Sesuaikan dengan path yang sesuai
    $mpdfConfig = [
        'tempDir' => $mpdfTmpDir,
    ];

    $mpdf = new Mpdf($mpdfConfig);

    // Ambil konten dari report_area
    $content = $_POST['report_content'];

    // Tambahkan konten ke PDF
    $mpdf->WriteHTML($content);

    $mpdf->Output('report.pdf', 'D'); // 'D' akan mengirimkan PDF sebagai respons untuk diunduh
    exit();
}
?>
