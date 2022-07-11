<?php

namespace App\Repositories\Backend\Sales;

use Exception;
use App\Models\Sales\DelightSales;
use App\Models\Access\User;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use App\Repositories\BaseRepository;
use Auth;
/**
 * Class LeadRepository.
 */
class DelightSalesRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = DelightSales::class;

    function getSales($param)
    {
      $user = Auth::user();
      $role = Auth::user()->roles()->get();

      if($role[0]['name'] == 'Delight Team')
      {
       $sales = DelightSales::where('assigned_to',$user->id)->orderBy('created_at','desc')->paginate(25);
      }
      else
      {
       if($param == 'all' || $param == null)
       {
         $sales = DelightSales::orderBy('created_at','desc')->paginate(25);
       }
       else if($param > 0 && is_numeric($param)) 
       {
         $sales = DelightSales::where('assigned_to',$param)->orderBy('created_at','desc')->paginate(25);
       }
       else if($param == 'none')
       {
         $sales = DelightSales::where('assigned_to',0)->orderBy('created_at','desc')->paginate(25);
       }
       else
       {
         $sales = DelightSales::orderBy('created_at','desc')->paginate(25);
       }
      }
       return $sales;
    }

   function updateSales($request)
   {
      if($request->type == 'f1')
      {
         DelightSales::where('id', $request->id)->update(['followup_1' => $request->value]);
         return true;
      }
      if($request->type == 'f2')
      {
         DelightSales::where('id', $request->id)->update(['followup_2' => $request->value]);
         return true;
      }
      if($request->type == 'save')
      {
         DelightSales::where('id', $request->id)->update(['comment' => $request->value]);
         return true;
      }
   }

   function assignSales()
   {
      $usersCount =  DB::table('role_user')
                    ->select('role_user.user_id')
                    ->join('users', 'users.id', '=', 'role_user.user_id')
                    ->where('role_user.role_id',4)
                    ->where('users.status',1)
                    ->where('users.deleted_at',NULL)
                    ->whereNotIn('user_id', [3])
                    ->get();
      if(count($usersCount) > 0)
      {
         $ids = DB::table('delight_sales')->where('assigned_to',0)->pluck('id');     
         $total = round(count($ids)/count($usersCount));
         $assignSales =  DelightSales::where('assigned_to', 0)->select('id')->get()->toArray();

         $i=0;
         foreach(array_chunk($assignSales, $total) as $row)
         {
            $idd = array_column($row, 'id');
            DelightSales::whereIn('id', $idd)->update(['assigned_to' => $usersCount[$i]->user_id]);
            $i++;
         }
      }
      return true;
   }
}