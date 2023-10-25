<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\order;
use App\Models\product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    function index(){
        $totalOrders  = order::where('status' , '!=' ,'cancelled')->count();
        $totalProducts  = product::count();
        $totalUsers  = User::where('role' ,1)->count();

        $totalSales  = order::where('status' , '!=' ,'cancelled')->sum('grand_total');
        ///This Month revenue
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentData = Carbon::now()->format('Y-m-d');
        $revenueThisMonth  = order::where('status' , '!=' ,'cancelled')
        ->whereDate('created_at','>=', $startOfMonth)
        ->whereDate('created_at','<=', $currentData)
        ->sum('grand_total');

        ///last Month revenue
        $lastMonthStartDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
        $lastMonthEndDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
        $lastMonthName = Carbon::now()->subMonth()->startOfMonth()->format('M');

        $lastThisMonthRevenue  = order::where('status' , '!=' ,'cancelled')
        ->whereDate('created_at','>=', $lastMonthStartDate)
        ->whereDate('created_at','<=', $lastMonthEndDate)
        ->sum('grand_total');

        ///last 30 Days revenue
       $lastThirtyStartDate = Carbon::now()->subDays(30)->format('Y-m-d');

    $lastThirtyDaysRevenue  = order::where('status' , '!=' ,'cancelled')
    ->whereDate('created_at','>=', $lastThirtyStartDate)
    ->whereDate('created_at','<=', $currentData)
    ->sum('grand_total');


        return view('admin.dashboard',[
            'totalOrders'=>$totalOrders,
            'totalProducts'=>$totalProducts,
            'totalUsers' => $totalUsers,
            'totalSales' => $totalSales,
            'revenueThisMonth' => $revenueThisMonth,
            'lastThisMonthRevenue' =>$lastThisMonthRevenue,
            'lastThirtyDaysRevenue' =>$lastThirtyDaysRevenue,
            'lastMonthName' => $lastMonthName
        ]);
    }
    function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
