<?php

namespace App\Http\Controllers\Backend\Sales;

use Excel;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Exceptions\GeneralException;
use App\Repositories\Backend\Sales\DelightSalesRepository;
use App\Models\Access\User\User;

class DelightSalesController extends Controller
{
    public function index(Request $request, DelightSalesRepository $delightSaleRepo)
    {
        $id = $request->id;
        $sales = $delightSaleRepo->getSales($id);
        $executives = User::whereHas('roles', function($query){
                            $query->where('role_id', '4');
                        })
                        ->where('status', '1')
                        ->select('name', 'id')
                        ->get();  
        return view('backend.sales.index', compact('sales','executives','id'));
    }
    public function updateDelightSale(Request $request, DelightSalesRepository $delightSaleRepo)
    {

        $updateSales = $delightSaleRepo->updateSales($request);
        
    }

    // public function assignDelightSale(Request $request, DelightSalesRepository $delightSaleRepo)
    // {

    //     $updateSales = $delightSaleRepo->assignSales($request);
        
    // }
    
}
