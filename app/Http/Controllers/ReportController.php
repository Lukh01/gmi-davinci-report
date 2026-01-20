<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function generate(Request $request)
    {
        // Validate uploaded files, type, and optional messages/comments
        $request->validate([
            'davinci_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'jibble_img'  => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'type'        => 'required|in:pdf,word',
            'messages'    => 'nullable|array',
            'comments'    => 'nullable|string',
            'file_name'   => 'nullable|string', // optional user-defined file name
        ]);

        $type = $request->input('type');
        $messages = $request->input('messages', []); // optional chat messages
        $comments = $request->input('comments', ''); // optional comments

        // Get user-provided file name or default
        $fileNameInput = $request->input('file_name', '');
        $fileNameInput = preg_replace('/[^A-Za-z0-9_\-]/', '_', $fileNameInput); // sanitize filename

        if (empty($fileNameInput)) {
            $fileBaseName = 'Attendance_Report_' . time();
        } else {
            $fileBaseName = $fileNameInput;
        }

        $davinci = $request->file('davinci_img');
        $jibble  = $request->file('jibble_img');

        if ($type === 'word') {
            // --- Generate Word Document ---
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            // Title & date
            $section->addText("Attendance Report", ['bold' => true, 'size' => 16], [
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
            ]);
            $section->addText("Date: " . now()->format('d M Y'), ['italic' => true], [
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
            ]);
            $section->addTextBreak(2);

            // Images with width resize
            $section->addImage($davinci->getRealPath(), [
                'width' => 600,
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
            ]);
            $section->addTextBreak(1);
            $section->addImage($jibble->getRealPath(), [
                'width' => 600,
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
            ]);

            // Optional chat messages
            if (!empty($messages)) {
                $section->addTextBreak(2);
                $section->addText("Chat / Messages:", ['bold' => true]);
                foreach ($messages as $msg) {
                    if (isset($msg['text']) && $msg['text'] !== '') {
                        $section->addText("- " . $msg['text']);
                    }
                    if (isset($msg['image']) && $msg['image'] !== '') {
                        $section->addImage($msg['image'], [
                            'width' => 600,
                            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
                        ]);
                    }
                }
            }

            // Optional comments
            if (!empty($comments)) {
                $section->addTextBreak(1);
                $section->addText("Additional Comments: " . $comments);
            }

            $fileName = $fileBaseName . '.docx';
            $tempFile = tempnam(sys_get_temp_dir(), $fileName);
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($tempFile);

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);

        } else {
            // --- Generate PDF ---
            $davinciBase64 = 'data:image/' . $davinci->getClientOriginalExtension() . ';base64,' . base64_encode(file_get_contents($davinci->getRealPath()));
            $jibbleBase64  = 'data:image/' . $jibble->getClientOriginalExtension() . ';base64,' . base64_encode(file_get_contents($jibble->getRealPath()));

            $pdf = Pdf::loadView('pdf_template', [
                'davinci'  => $davinciBase64,
                'jibble'   => $jibbleBase64,
                'date'     => now()->format('d M Y'),
                'messages' => $messages,
                'comments' => $comments
            ]);

            $fileName = $fileBaseName . '.pdf';
            return $pdf->download($fileName);
        }
    }
}
