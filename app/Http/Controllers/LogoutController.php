<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\AuditLogger;


class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        // Log the logout action
        AuditLogger::log('logout', [
            'user_id' => Auth::id(),
            'email' => Auth::user()?->email,
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
