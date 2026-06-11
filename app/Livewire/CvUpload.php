<?php

namespace App\Livewire;

use App\JobiBot\Lai;
use App\JobiBot\Exceptions\LaiException;
use App\Models\Candidate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class CvUpload extends Component
{
    use WithFileUploads;

    public $cv;
    public $cvText = '';
    public $summary = '';
    public $loading = false;

    public function updatedCv(): void
    {
        $this->validate([
            'cv' => 'file|mimes:pdf,doc,docx,txt|max:' . (config('jobibot.cv_max_size_kb', 5120)),
        ]);

        $this->loading = true;

        try {
            // Extract text from uploaded file
            $this->cvText = $this->extractText();
            $this->summary = '';

            // AI summary
            $result = Lai::summarize($this->cvText);
            $this->summary = $result['candidate_cv_summary'];

            // Save to the candidate record
            $candidate = Candidate::firstOrCreate(
                ['user_id' => Auth::id()],
                ['cv' => '', 'bio' => '']
            );

            $candidate->update([
                'cv'      => $this->cvText,
                'cv_path' => $this->cv->store('cvs', config('jobibot.cv_disk', 'local')),
                'bio'     => $this->summary,
            ]);

        } catch (LaiException $e) {
            session()->flash('error', 'AI summarization failed: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function removeCv(): void
    {
        $this->cv = null;
        $this->cvText = '';
        $this->summary = '';

        Candidate::where('user_id', Auth::id())->delete();
    }

    protected function extractText(): string
    {
        $path = $this->cv->getRealPath();
        $ext = strtolower($this->cv->getClientOriginalExtension());

        if ($ext === 'txt') {
            return file_get_contents($path);
        }

        if ($ext === 'pdf') {
            return $this->extractPdfText($path);
        }

        // For doc/docx, return a note — full parsing needs a library
        return '(Document uploaded: ' . $this->cv->getClientOriginalName() . '). '
            . 'For best results, use .txt or .pdf format. '
            . 'Content: ' . substr(strip_tags(file_get_contents($path) ?: ''), 0, 5000);
    }

    protected function extractPdfText(string $path): string
    {
        // Simple PDF text extraction using pdftotext if available
        $output = shell_exec('pdftotext ' . escapeshellarg($path) . ' - 2>/dev/null');
        if ($output) {
            return mb_substr($output, 0, 10000);
        }

        // Fallback: read raw content
        $content = file_get_contents($path);
        // Strip binary noise
        $text = preg_replace('/[^\x20-\x7E\x0A\x0D]/', ' ', $content);
        $text = preg_replace('/\s+/', ' ', $text);
        return mb_substr(trim($text), 0, 5000);
    }

    public function render()
    {
        $candidate = Candidate::where('user_id', Auth::id())->first();
        return view('livewire.cv-upload', [
            'candidate' => $candidate,
        ]);
    }
}