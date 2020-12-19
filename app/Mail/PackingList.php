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
        $boxTotal = 0;
        $weightTotal = 0;
        foreach ($this->packingList->orders as $order) {
            foreach ($order->orderDetails as $detail) {
                $boxTotal += $detail->qty;
                $weightTotal += $detail->weight;
            }
        }

        $this->packingList->boxTotal = $boxTotal;
        $this->packingList->weightTotal = $weightTotal;

        $pdf = \PDF::loadView('pdf.packingList', ['packingList' => $this->packingList])->setPaper('letter');

        return $this->markdown('email.packing-list')->attachData($pdf->output(), 'Packing-List.pdf');
    }
}
