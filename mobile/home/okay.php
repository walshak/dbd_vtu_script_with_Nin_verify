

<?php
require 'https://dannvtu.com.ng/mobile/assets/dompdf/autoload.inc.php';

use Dompdf\Dompdf;


// HTML content with CSS to center the text
$html = '
<!DOCTYPE html>
<html>
<head>
<style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 900vh;
        margin: 0;
    }
    h5 {
        text-align: center;
    }
</style>
</head>
<body>
    <h5>Hello, World!</h5>
</body>
</html>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the PDF
$dompdf->render();

// Output the PDF to the browser for download
$dompdf->stream('output.pdf', array('Attachment' => 0));
?>
 