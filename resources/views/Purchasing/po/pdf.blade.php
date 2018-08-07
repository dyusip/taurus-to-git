Route::get('/', function (Codedge\Fpdf\Fpdf\Fpdf $fpdf) {

$fpdf->AddPage();
$fpdf->SetFont('Courier', 'B', 18);
$fpdf->Cell(50, 25, 'Hello World!');
$fpdf->Output();

});