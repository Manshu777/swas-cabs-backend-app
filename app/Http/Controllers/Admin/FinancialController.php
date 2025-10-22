<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
      public function index()
    {
        // $wallets = DriverWallet::with('user')->paginate(10);
        return view('admin.financial.index');
    }
}
