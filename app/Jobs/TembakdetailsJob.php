<?php

namespace App\Jobs;

use App\Models\Cabang; // Pastikan sudah mengimpor model yang diperlukan
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Jobs\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\LogInsert;

class TembakdetailsJob extends ShouldQueue
{
    protected $cabang;
    protected $start;
    protected $end;

    public function __construct($cabang, $start, $end)
    {
        $this->cabang = $cabang;
        $this->start = $start;
        $this->end = $end;
    }

    public function handle()
    {
        // Panggil logika Tembakcustomer
        $this->Tembakdetails($this->cabang, $this->start, $this->end);
    }

    private function Tembakcustomer($cabang, $start, $end)
    {
        // Implementasikan logika Tembakcustomer di sini
    }
}
