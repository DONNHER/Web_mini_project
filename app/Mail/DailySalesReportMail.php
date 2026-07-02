<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class DailySalesReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reportData;
    public $filePath;

    public function __construct($reportData, $filePath = null)
    {
        $this->reportData = $reportData;
        $this->filePath = $filePath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Sales Report - ' . now()->format('Y-m-d'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily_sales_report',
        );
    }

    public function attachments(): array
    {
        if ($this->filePath && file_exists($this->filePath)) {
            return [
                Attachment::fromPath($this->filePath)
                    ->as('daily_orders_' . now()->format('Y-m-d') . '.xlsx')
            ];
        }
        return [];
    }
}
