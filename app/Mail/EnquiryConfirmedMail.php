<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnquiryConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $contact_data;
    /**
     * Create a new message instance.
     */
    public function __construct($contactData)
    {
        $this->contact_data = $contactData;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $content = $this->contact_data['content'];

        $subject = " Enquiry Received & Under Process | Vastra";
        return $this->to($this->contact_data['to'])->cc($this->contact_data['cc'])->subject($subject)->view('emails.enquiry_confirmed', ['image' => $this->contact_data['image'], 'first_name' => $this->contact_data['first_name'], 'heading1' => $this->contact_data['heading1'], 'white_logo' => $this->contact_data['white_logo'], 'content' => $content, 'content_footnote' => $this->contact_data['content_footnote']]);
    }
}
