public function sendResultWhatsapp($studentId)
   {
       $student = Student::with(['school', 'schoolClass', 'guardian', 'results.subject'])->findOrFail($studentId);
       $term = request('term_id') ? Term::find(request('term_id')) : null;
       $session = request('session_id') ? AcademicSession::find(request('session_id')) : null;
       $results = $student->results;
   
       // Generate PDF
       $pdf = Pdf::loadView('results.pdf', compact('student', 'results', 'term', 'session'));
       $fileName = 'results/' . $student->id . '.pdf';
       Storage::disk('public')->makeDirectory('results');
       Storage::disk('public')->put($fileName, $pdf->output());
   
       // Parent phone in international format
       $parentPhone = preg_replace('/^0/', '234', $student->guardian_phone ?? $student->guardian->phone);
   
       // WhatsApp message
       $pdfPath = asset('storage/' . $fileName);
       $message = "Hello, your child's result is ready. Download PDF here: $pdfPath";
       $encodedMessage = urlencode($message);
   
       // Redirect to WhatsApp link
       return redirect("https://wa.me/{$parentPhone}?text={$encodedMessage}");
   }
   