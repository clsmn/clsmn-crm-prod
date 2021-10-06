<?php

namespace App\Http\Controllers\Backend\Search;

use Illuminate\Http\Request;
use App\Models\Access\User\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
 * Class SearchController.
 */
class SearchController extends Controller
{
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        // if (! $request->has('q')) {
        //     return redirect()
        //         ->route('admin.dashboard')
        //         ->withFlashDanger(trans('strings.backend.search.empty'));
        // }

        /**
         * Process Search Results Here.
         */
        $results = null;
        $leadStage = DB::table('lead_stage')->pluck('message', 'id');
        $executives = User::whereHas('roles', function($query){
                            $query->where('role_id', '3');
                        })
                        ->where('status', '1')
                        ->pluck('name', 'id');

        return view('backend.search.index', compact('leadStage', 'executives'))
            ->withSearchTerm($request->get('q'))
            ->withResults($results);
    }
}
