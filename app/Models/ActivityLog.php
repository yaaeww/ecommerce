<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Helper static untuk merekam aktivitas sistem secara instan.
     */
    public static function record($action, $description, $subject = null, $userId = null)
    {
        try {
            $user = $userId ?: Auth::id();
            $subjectType = null;
            $subjectId = null;

            if ($subject && is_object($subject)) {
                $subjectType = get_class($subject);
                $subjectId = $subject->id ?? null;
            }

            return static::create([
                'user_id' => $user,
                'action' => strtoupper($action),
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'description' => $description,
                'ip_address' => Request::ip() ?? '127.0.0.1',
                'user_agent' => Request::userAgent() ?? 'System',
            ]);
        } catch (\Throwable $e) {
            // Jangan gagalkan alur utama jika gagal logging
            \Illuminate\Support\Facades\Log::warning('ActivityLog failed: ' . $e->getMessage());
            return null;
        }
    }
}
