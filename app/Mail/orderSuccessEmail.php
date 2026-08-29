<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class orderSuccessEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $emailBody;

    public function __construct($subjectLine, $emailBody)
    {
        $this->subjectLine = $subjectLine;
        $this->emailBody   = $emailBody;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.oreder-details-email')
            ->with([
                'emailBody'   => $this->emailBody,
                'subjectLine' => $this->subjectLine
            ]);
    }
}