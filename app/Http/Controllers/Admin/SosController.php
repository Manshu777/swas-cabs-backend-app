<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SosAlert;
use Illuminate\Http\Request;

class SosController extends Controller
{
    public function index()
    {
        $sosAlerts = SosAlert::with('user', 'ride')->where('status', 'pending')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.sos.index', compact('sosAlerts'));
    }
}