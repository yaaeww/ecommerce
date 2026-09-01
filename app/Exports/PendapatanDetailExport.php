<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PendapatanDetailExport implements FromView
{
    protected $detail;

    public function __construct($detail)
    {
        $this->detail = $detail;
    }

    public function view(): View
    {
        return view('penjual.exports.pendapatan-detail', [
            'detail' => $this->detail,
        ]);
    }
}
