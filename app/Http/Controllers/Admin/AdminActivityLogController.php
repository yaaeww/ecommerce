<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminActivityLogController extends Controller
{
    /**
     * Tampilkan riwayat jejak audit (Audit Trail) & log aktivitas sistem.
     */
    public function index(Request $request)
    {
        $action = $request->get('action');
        $userId = $request->get('user_id');
        $search = $request->get('search');

        $query = ActivityLog::with('user');

        if ($action && $action !== 'semua') {
            $query->where('action', $action);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        // Distinct actions untuk dropdown filter
        $availableActions = ActivityLog::select('action')->distinct()->pluck('action');
        $users = User::orderBy('name')->get();

        $totalLogs = ActivityLog::count();
        $adminLogsCount = ActivityLog::whereHas('user', function ($u) {
            $u->where('role', 'admin');
        })->count();

        return view('admin.activity_log.index', compact(
            'logs',
            'action',
            'userId',
            'search',
            'availableActions',
            'users',
            'totalLogs',
            'adminLogsCount'
        ));
    }
}
