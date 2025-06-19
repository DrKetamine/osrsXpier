<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public static function log(string $action, ?array $details = null)
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'details' => $details ? json_encode($details) : null,
            'ip_address' => Request::ip(),
        ]);
    }
}
