<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnquiryConfirmedMailAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $contact_data;

    public function __construct($contactData)
    {
        $this->contact_data = $contactData;
    }

    public function build()
    {
        $subject = "New Enquiry Submitted | Vastra";

        return $this->to($this->contact_data['to'])
            ->cc($this->contact_data['cc'])
            ->subject($subject)
            ->view('emails.enquiry_confirmed_admin', [
                'first_name'       => $this->contact_data['first_name'],
                'heading1'         => $this->contact_data['heading1'],
                'content'          => $this->contact_data['content'],
                'image'            => $this->contact_data['image'],
                'white_logo'       => $this->contact_data['white_logo'],
                'content_footnote' => $this->contact_data['content_footnote'],
            ]);
    }
}
