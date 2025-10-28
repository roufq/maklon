<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceSentMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public string $invoiceUrl;
    public string $summaryUrl;

    public function __construct(Invoice $invoice, string $invoiceUrl, string $summaryUrl)
    {
        $this->invoice = $invoice;
        $this->invoiceUrl = $invoiceUrl;
        $this->summaryUrl = $summaryUrl;
    }

    public function build()
    {
        return $this->subject('Invoice '.$this->invoice->number)
            ->view('emails.invoice-sent')
            ->with([
                'invoice' => $this->invoice,
                'invoiceUrl' => $this->invoiceUrl,
                'summaryUrl' => $this->summaryUrl,
            ]);
    }
}

