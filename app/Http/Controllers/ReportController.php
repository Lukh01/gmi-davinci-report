namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function generate(Request $request)
    {
        // 1. Process Davinci Image
        $davinci = $request->file('davinci_img');
        $davinciBase64 = 'data:image/' . $davinci->getClientOriginalExtension() . ';base64,' . base64_encode(file_get_contents($davinci));

        // 2. Process Jibble Image
        $jibble = $request->file('jibble_img');
        $jibbleBase64 = 'data:image/' . $jibble->getClientOriginalExtension() . ';base64,' . base64_encode(file_get_contents($jibble));

        // 3. Load the View and Pass Images
        $pdf = Pdf::loadView('pdf_template', [
            'davinci' => $davinciBase64,
            'jibble' => $jibbleBase64,
            'date' => now()->format('d M Y')
        ]);

        // 4. Download the file
        return $pdf->download('Attendance_Report_'.time().'.pdf');
    }
}