<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $attachmentPath;
    public $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $attachmentPath, $name)
    {
        $this->subject = $subject;
        $this->attachmentPath = $attachmentPath;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.order_email',['name' => $this->name]) // Blade view for email content
                    ->attach($this->attachmentPath, [
                        'as' => 'invoice.pdf', // Set the attachment name
                        'mime' => 'application/pdf',
                    ]);
    }
}
