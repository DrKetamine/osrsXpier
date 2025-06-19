<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function download()
    {   // Get all audit logs, newest first
        $logs = AuditLog::orderBy('created_at', 'desc')->get();

        // Prepare CSV headers and filename
        $filename = 'audit_logs_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        // Add CSV content directly to output
        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['ID', 'User ID', 'Action', 'Details', 'IP Address', 'Created At']);
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user_id,
                    $log->action,
                    $log->details,
                    $log->ip_address,
                    $log->created_at,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
