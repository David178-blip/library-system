<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserProfileController extends Controller {
    public function index() {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function qr() {
        $user = Auth::user();
        $qr = QrCode::size(200)->generate("USER:{$user->id} - {$user->name}");
        return view('profile.qr', compact('user','qr'));
    }
}
