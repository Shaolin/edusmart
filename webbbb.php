public function sendResultWhatsapp($studentId)
   {
       $student = Student::with(['school', 'schoolClass', 'guardian', 'results.subject'])->findOrFail($studentId);
       $term = request('term_id') ? Term::find(request('term_id')) : null;
       $session = request('session_id') ? AcademicSession::find(request('session_id')) : null;
       $results = $student->results;
   
       // Generate PDF
    //    $pdf = Pdf::loadView('results.pdf', compact('student', 'results', 'term', 'session'));
    $pdf = Pdf::loadView('results.pdf', [
        'student' => $student,
        'results' => $results,
        'term' => $term,
        'session' => $session,
        'school' => $student->school,
        'position' => $position ?? null,
        'total_students' => $total_students ?? null,
    ]);
    
   
       // Truehost public directory
       $folder = $_SERVER['DOCUMENT_ROOT'] . '/results';
   
       // If folder doesn't exist, create it
       if (!file_exists($folder)) {
           mkdir($folder, 0777, true);
       }
   
       // File path
       $filePath = $folder . '/' . $student->id . '.pdf';
   
       // Save file
       $pdf->save($filePath);
   
       // Public URL
       $pdfUrl = url('results/' . $student->id . '.pdf');
   
       // Parent phone
       $parentPhone = preg_replace('/^0/', '234', $student->guardian_phone ?? $student->guardian->phone);
   
       // WhatsApp message
       $message = "Hello, your child's result is ready. Download PDF here: $pdfUrl";
       $encodedMessage = urlencode($message);
   
       return redirect("https://wa.me/{$parentPhone}?text={$encodedMessage}");
   }
   
   
   
   {{ route('students.sendWhatsapp', $student->id) }}