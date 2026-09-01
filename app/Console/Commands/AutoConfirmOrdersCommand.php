<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AutoConfirmOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-confirm {--days=3 : Jumlah hari sejak pengiriman}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis menyelesaikan pesanan yang telah dikirim lebih dari N hari (Auto Escrow Release)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $threshold = Carbon::now()->subDays($days);

        $orders = Order::where('status', 'complete')
            ->where('status_pesanan', 'dikirim')
            ->where(function ($q) use ($threshold) {
                $q->where('dikirim_at', '<=', $threshold)
                  ->orWhere(function ($sub) use ($threshold) {
                      $sub->whereNull('dikirim_at')
                          ->where('updated_at', '<=', $threshold);
                  });
            })
            ->get();

        $count = 0;
        foreach ($orders as $order) {
            DB::transaction(function () use ($order, $days) {
                $order->lockForUpdate();

                // Double-check lock: pastikan order belum pernah diterima oleh buyer secara manual
                if ($order->status_pesanan === 'diterima' || $order->is_escrow_released) {
                    return; // Skip, sudah diproses
                }

                $order->update([
                    'status_pesanan' => 'diterima',
                    'diterima_at' => Carbon::now(),
                    'is_escrow_released' => true,
                ]);

                ActivityLog::record(
                    'AUTO_CONFIRM_DELIVERY',
                    "Sistem otomatis mengonfirmasi penerimaan pesanan #{$order->id} ({$days} hari sejak pengiriman). Saldo escrow telah dilepas ke toko.",
                    $order
                );
            });

            $count++;
        }

        $this->info("Berhasil mengonfirmasi otomatis {$count} pesanan.");
        return 0;
    }
}
