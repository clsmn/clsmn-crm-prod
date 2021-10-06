<?php

namespace App\Http\Controllers\Backend\DataBank;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Repositories\Backend\DataUser\DataUserRepository;

/**
 * Class DataBankTableController.
 */
class DataBankTableController extends Controller
{
    /**
     * @var DataUserRepository
     */
    protected $dataUserRepository;

    /**
     * @param DataUserRepository $dataUserRepository
     */
    public function __construct(DataUserRepository $dataUserRepository)
    {
        $this->dataUserRepository = $dataUserRepository;
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function getDataBankUsers(Request $request)
    {
        return Datatables::of($this->dataUserRepository->getForDataTable($request))
            ->addColumn('action', function ($user) {
                return '<button class="btn btn-xs assignLead" data-val="'.$user->id.'">Click to Assign</button>';
            })
            ->editColumn('learning', function ($user) {
                return ($user->learning == 1)? '<span class="text-success">Active</span>' : 'Inactive';
            })
            ->editColumn('updated_at', function ($user) {
                return date('d M y, h:i a', strtotime($user->updated_at));
            })
            ->editColumn('lead_last_call', function ($user) {
                return ($user->lead_last_call)? date('d M y, h:i a', strtotime($user->lead_last_call)):null;
            })
            ->editColumn('moved_to_lead', function ($row) {
                return ($row->moved_to_lead == '1')? 'Yes':'No';
            })
            ->editColumn('phase', function ($row) {
                if($row->phase == 'buy_attempt')
                    return 'Buy Attempt';
                else if($row->phase == 'cart')
                    return 'Cart Abandon';
                else if($row->phase == 'trial')
                    return 'Trial Started';
                else if($row->phase == 'kit_purchased')
                    return 'Kit Purchased';
                else
                    return '';
            })
            ->rawColumns(['action', 'messenger', 'learning', 'community'])
            ->make(true);
    }

   

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function searchDataBankUsers(Request $request)
    {
        return Datatables::of($this->dataUserRepository->getForSearchDataTable($request))
            ->addColumn('action', function ($user) {
                return '<button class="btn btn-xs assignLead" data-val="'.$user->id.'">Assigned To</button>';
            })
            ->editColumn('messenger', function ($user) {
                return ($user->messenger == 1)? '<span class="text-success">Active</span>' : 'Inactive';
            })
            ->editColumn('learning', function ($user) {
                return ($user->learning == 1)? '<span class="text-success">Active</span>' : 'Inactive';
            })
            ->editColumn('community', function ($user) {
                return ($user->community == 1)? '<span class="text-success">Active</span>' : 'Inactive';
            })
            ->rawColumns(['action', 'messenger', 'learning', 'community'])
            ->make(true);
    }
    
}
