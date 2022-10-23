<?php

namespace App\Http\Controllers\Admin\Master;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\CustomersExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\customer\editRequest;
use App\Http\Requests\customer\customerRequest;
use App\Repositories\Customer\CustomerResponse;

/*
|--------------------------------------------------------------------------
| Rumah Dev
| Backend Developer : ibudirsan
| Email             : ibnudirsan@gmail.com
| Copyright © RumahDev 2022
|--------------------------------------------------------------------------
*/
class CustomerController extends Controller
{

    protected $CustomerResponse ;
    public function __construct(CustomerResponse  $CustomerResponse)
    {
        $this->CustomerResponse  = $CustomerResponse ;
    }

    public function index(Request $request)
    {
        if($request->ajax()) {
            $result = $this->CustomerResponse->datatable();
                return DataTables::eloquent($result)
                                ->addIndexColumn(['address'])

                                ->addColumn('delete', function ($delete) {
                                    return  '
                                                <button type="button" class="btn btn-danger btn-sm btn-size"
                                                        onclick="isDelete('.$delete->id.')">
                                                            Trash
                                                </button>
                                            ';
                                })

                                ->addColumn('edit', function ($edit) {
                                    return  '
                                                <a href="'.url(route('customer.edit',$edit->uuid)).'" type="button" class="btn btn-success btn-sm btn-size">
                                                            Edit
                                                </a>
                                            ';
                                })

                                ->editColumn('age', function ($age) {
                                    return $age->age. " Years";
                                })

                                ->editColumn('created_at', function ($created) {
                                    $date = Carbon::create($created->created_at)->format('Y-m-d H:i:s');
                                    return $date;
                                })

                                ->rawColumns(['delete','edit'])
                                ->escapeColumns(['delete','edit'])
                                ->smart(true)
                                ->make();
        }
            return view('master.customer.index');
    }

    public function trashData($id)
    {
        try {
            $this->CustomerResponse->trashedData($id);
            $success = true;
        } catch (\Exception $e) {
            $message = "Failed to moving data Trash";
            $success = false;
        }
            if($success == true) {
                /**
                 * Return response true
                 */
                return response()->json([
                    'success' => $success
                ]);
            }elseif($success == false){
                /**
                 * Return response false
                 */
                return response()->json([
                    'success' => $success,
                    'message' => $message,
                ]);
            }
    }

    public function Trash(Request $request)
    {
        if($request->ajax()) {
            $result = $this->CustomerResponse->datatable()
                            ->onlyTrashed();
                return DataTables::of($result)
                        ->addIndexColumn(['address'])

                        ->addColumn('delete', function ($delete) {
                            return  '
                                        <button type="button" class="btn btn-danger btn-sm btn-size"
                                                onclick="isDelete('.$delete->id.')">
                                                    Delete
                                        </button>
                                    ';
                        })

                        ->addColumn('restore', function ($restore) {
                            return  '
                                        <button type="button" class="btn btn-success btn-sm btn-size"
                                                onclick="isRestore('.$restore->id.')">
                                                    Restore
                                        </button>
                                    ';
                        })

                        ->editColumn('age', function ($age) {
                            return $age->age. " Years";
                        })

                        ->editColumn('deleted_at', function ($deleted) {
                            $date = Carbon::create($deleted->deleted_at)->format('Y-m-d H:i:s');
                            return $date;
                        })

                        ->rawColumns(['delete','restore'])
                        ->escapeColumns(['delete','restore'])
                        ->smart(true)
                        ->make();
        }
            return view('master.customer.trash');
    }

    public function create()
    {
        return view('master.customer.create');
    }

    public function Store(customerRequest $request)
    {
        try {
            $this->CustomerResponse->create($request);
                $notification = ['message'      => 'Successfully created new customer.',
                                  'alert-type'  => 'primary',
                                  'gravity'     => 'bottom',
                                  'position'    => 'right'];
                    return redirect()->route('customer.index')->with($notification);
        } catch (\Exception $e) {
            
            $notification = ['message'     => 'Failed to created data new customer.',
                             'alert-type'  => 'danger',
                             'gravity'     => 'bottom',
                             'position'    => 'right'];
                return redirect()->route('customer.index')->with($notification);
            
        }
    }

    public function edit($id)
    {
        $result = $this->CustomerResponse->edit($id);
            return view('master.customer.edit',compact('result'));
    }

    public function update(editRequest $request, $id)
    {
        try {
            $this->CustomerResponse->update($request, $id);
                $notification = ['message'      => 'Successfully updateed customer.',
                                'alert-type'  => 'success',
                                'gravity'     => 'bottom',
                                'position'    => 'right'];
                return redirect()->route('customer.index')->with($notification);
        } catch (\Exception $e) {
                $notification = ['message'     => 'Failed to updated data customer.',
                                'alert-type'  => 'danger',
                                'gravity'     => 'bottom',
                                'position'    => 'right'];
                return redirect()->route('customer.index')->with($notification);
        }
    }

    public function RestoreData($id)
    {
        try {
            $this->CustomerResponse->restore($id);
            $success = true;
        } catch (\Exception $e) {
            $message = "Failed to moving data customer Trash";
            $success = false;
        }
            if($success == true) {
                /**
                 * Return response true
                 */
                return response()->json([
                    'success' => $success
                ]);
            }elseif($success == false){
                /**
                 * Return response false
                 */
                return response()->json([
                    'success' => $success,
                    'message' => $message,
                ]);
            }
    }

    public function delete($id)
    {
        try {
            $this->CustomerResponse->deletePermanent($id);
            $success = true;
        } catch (\Exception $e) {
            $message = "Failed to Delete Permanent data customer Trash";
            $success = false;
        }
            if($success == true) {
                /**
                 * Return response true
                 */
                return response()->json([
                    'success' => $success
                ]);
            }elseif($success == false){
                /**
                 * Return response false
                 */
                return response()->json([
                    'success' => $success,
                    'message' => $message,
                ]);
            }
    }

    public function downloadExcel()
    {
        /** 
         * Maximum Time Setting 1800 seconds
         * (30 Minutes)
         * */
        ini_set('max_execution_time', 1800);
        date_default_timezone_set('Asia/Jakarta');
        $date       = date('Y-m-d-H-i-s');
            return Excel::download(new CustomersExport(), "Customers-$date.xlsx");
    }
}
