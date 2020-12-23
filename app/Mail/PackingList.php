<?php

namespace App\Mail;

use Barryvdh\DomPDF\PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PackingList extends Mailable
{
    use Queueable, SerializesModels;

    public $packingList;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\PackingList $packingList)
    {
        $this->packingList = $packingList;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->packingList->completeInfoPDF();
        $pdf = \PDF::loadView('pdf.packingList', ['packingList' => $this->packingList])->setPaper('letter');

        return $this->markdown('email.packing-list')->attachData($pdf->output(), 'Packing-List.pdf');
    }
}
