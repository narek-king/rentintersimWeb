<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Rentintersimrepo\orders\CreateHelper as Helper;
use Auth;
use App\Http\Controllers\HomeController;
use Rentintersimrepo\orders\ViewHelper;
use Mail;
use App\Mail\OrderMail;
use DB;
use Excel;
use Rentintersimrepo\users\UserManager;
use App\Models\ManualActivation;

class OrderController extends Controller
{
    protected $helper;
    protected $viewHelper;
    protected $userManager;

    public function __construct(Helper $helper, ViewHelper $viewHelper, UserManager $userManager)
    {
        $this->helper = $helper;
        $this->viewHelper = $viewHelper;
        $this->userManager = $userManager;
        $this->middleware('superAdmin')->only(['activate', 'deactivate']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = Auth::user();

        $orders = null;
        if ($user->level != 'Super admin'){
            $net = $this->userManager->getNetworkFromCache($user->id);
            $orders = Order::whereIn('created_by', $net)->orderby('id', 'desc')->paginate(env('PAGINATE_DEFAULT'));
//            dd($orders);
        }
        if ($user->level == 'Super admin')
            $orders = Order::orderby('id', 'desc')->paginate(env('PAGINATE_DEFAULT'));
        $ordersArray = $this->viewHelper->solveOrderList($orders);
        $counts = $this->viewHelper->getCounts($this->userManager);

//        dd($specials);


//        var_dump($ordersArray);
        return view('home', compact('ordersArray'), compact('counts'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('ordercreate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $number = null;

        $status ='waiting';
        $this->validate(request(), [
//        'from' => 'required',
//        'to' =>  'required',
        'sim' => 'required',
        'landing_string' =>  'required',
        'departure_string' =>  'required',
            'package_id' => 'required'
//        'reference_number' =>  'required',
//        'status' =>  'required',
//        'remark' =>  'required',
//        'costomer_id' =>  'required',
//        'employee_id' =>  'required',
//        'created_by' =>  'required',
//        'updated_by' =>  'required',

        ]);

        if ($request->input('landing') >= $request->input('departure') ||
            ($request->input('departure') - $request->input('landing')) < 2700 )
            return response()->json(['sim' => 'The landing or departure selection is not correct'], 403);



        $newOrder = new Order();
            $newOrder->from = $this->helper->setStartTime($request->input('landing_string'));
            $newOrder->to =  $this->helper->setEndTime($request->input('departure_string'));
            $newOrder->landing =  $request->input('landing_string');
            $newOrder->departure =  $request->input('departure_string');
            $newOrder->reference_number =  $request->input('reference_number');
            $newOrder->status =  $status;
            $newOrder->costumer_number =  $request->input('costumer_number');
            $newOrder->package_id = $request->input('package_id');
            $newOrder->remark =  $request->input('remark');
            $newOrder->created_by =  Auth::user()->id;
            $newOrder->updated_by =  Auth::user()->id;
            $newOrder->phone_id = 0;

        $sim = $this->helper->getSim($request->input('sim'));
        if($sim != null){
            if (!$this->helper->validateSim($sim, $newOrder))
                return response()->json(['sim'=>'The sim is already in use in these dates.'], 403);
            $sim->state = 'pending';
            $sim->save();
        } else {return response()->json(['sim' => 'sim not found'], 403);}
//                dd($simId);

        $newOrder->sim_id = $sim->id;
        $newOrder->save();

        if($request->has('phone_id') && $request->input('phone_id') != ''){
            if (Auth::user()->level == 'Super admin'){
                $number = $this->helper->setNumber($newOrder->id, $request->input('phone_id'));
                if ($number != $request->input('phone_id'))
                    return response()->json(['sim' => $number], 403);
            }
            } else {
            $number = $this->getNumber($newOrder->id);
            }
            if ($number != null){
                return $this->edit($newOrder->id);
              }
                $order = Order::with('phone')->find($newOrder->id);
            if ($order->status == 'waiting')
                $this->helper->sendMail($order->id);
        return response($order->toArray(), 200);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $order = Order::withTrashed()->find($id);
//        dd($order);
        $orderSolved = $this->viewHelper->solveOrderList(array($order));


        return response()->json($orderSolved, 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $Order = Order::find($id);
        $now = Carbon::now();
        $now->addDay();
        $from = $this->helper->setStartTime($request->input('landing_string'));
        $to = $this->helper->setEndTime($request->input('departure_string'));


        if ($request->input('landing') >= $request->input('departure') ||
            ($request->input('departure') - $request->input('landing')) < 2700
        )
            return response()->json(['sim' => 'The landing or departure selection is not correct'], 403);

        if ($Order->status == 'pending' || $Order->status == 'waiting') {
            if ($Order->from <= $now->timestamp) {
                return response(['sim' => 'Sorry. The activation time is less then 24 hours.'], 403);
            } //               elseif ($from <= $Order->to + 72000  && $to >= $Order->from - 72000){
            elseif ($from <= $Order->to && $to >= $Order->from) {
                $Order->from = $from;
                $Order->to = $to;
                $Order->landing = $request->input('landing_string');
                $Order->departure = $request->input('departure_string');
                $Order->reference_number = $request->input('reference_number');
//            $Order->costumer_number =  $request->input('costumer_number');
//            $Order->package_id = $request->input('package_id');
                $Order->remark = $request->input('remark');
                $Order->updated_by = Auth::user()->id;

                /*
                            if ($request->has('sim')) {
                                $sim = $this->helper->getSim($request->input('sim'));
                                if ($sim != null) {
                                    if ($sim->number != $request->input('sim')) {
                                        if ($sim->state != 'available')
                                            return response()->json(['sim' => 'sim is already taken'], 403);
                                        $sim->state = 'pending';
                                        $sim->save();
                                        $Order->sim_id = $sim->id;
                                    }
                                } else {
                                    return response()->json(['sim' => 'sim not found'], 403);
                                }
                            }
                            */
                $oldOrder = $Order::find($id);
                if ($this->helper->isNumberCompatible($Order, $oldOrder))
                    $Order->save();
                else
                    return response(['sim' => 'Conflict with other number'], 403);

                $number = null;
                if ($request->has('phone_id') && $request->input('phone_id') != '' && $request->input('phone_id') != $Order->phone_id) {
                    if (Auth::user()->level == 'Super admin') {
                        $number = $this->helper->setNumber($Order->id, $request->input('phone_id'));
                    }
                }
                if ($number != null) {
                    return $this->edit($Order->id);
                }

                return response($Order->toArray(), 200);
            } else
                return response(['sim' => 'Sorry. The new time is grater than 24 hours.'], 403);
        } else {
            return response(['sim' => 'This order cannot be edited due to status.'], 403);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $Order = Order::find($id);
        if ($Order != null){
            if ($Order->status != 'active'){
//                $this->helper->deactivate($id);
                if($Order->status == 'done'){
                    $Order->delete();
                    return response()->json(['deleted'], 200);
                } else {
                $this->helper->freeResources($Order, 'deleted');
                return response()->json(['deleted'], 200);
                }
            }
            return response()->json(['not allowed'], 403);
        }
    }

    public function filter($filter){
        $user = Auth::user();
        $orders = null;
        if ($user->level != 'Super admin'){
            $net = $this->userManager->getNetworkFromCache($user->id);
            $orders = Order::whereIn('created_by', $net)->filter($filter)->orderby('id', 'desc')->paginate(env('PAGINATE_DEFAULT'));
        }
        if ($user->level == 'Super admin')
            $orders = Order::filter($filter)->orderby('id', 'desc')->paginate(env('PAGINATE_DEFAULT'));
        $ordersArray = $this->viewHelper->solveOrderList($orders);
        $counts = $this->viewHelper->getCounts($this->userManager);

        return view('home', compact('ordersArray'), compact('counts'));
    }

    public function getNumber($orderid)
    {   $number = null;
        $order = Order::find($orderid);
        if($order->exists){
            if ($order->phone_id == 0)
        $number = $this->helper->getNumber($order);
        else return $order->phone_id;
        }
        return $number;
    }

    public function getNumberExternal($orderid)
    {   $number = null;
        $order = Order::find($orderid);
        if($order->exists){
            if ($order->phone_id == 0)
                $number = $this->helper->getNumber($order);
            else return $order->phone_id;
        }
        $orderNew = Order::find($orderid);
        if ($orderNew->phone_id != 0) {

            return response()->json(['number' => $order->phone->phone], 200);
        }
        else

        return response()->json(['number' => 'not found'], 403);

    }

    public function activate($id)
    {

        $order = Order::find($id);
        if ($order->status == 'pending'){
            DB::transaction(function () use($order) {
                $log = ManualActivation::forceCreate([
                    'phone_number' => $order->phone_id,
                    'sim_number' => $order->sim_id,
                    'call' => 'activate',
                    'old_time' => $order->landing,
                    'order_id' => $order->id,
                ]);
                $order->landing = Carbon::now()->format('d/m/Y H:i');
                $order->from = Carbon::now()->timestamp;
                $order->save();
                $this->helper->activate($order->id);
            }, 5);


            return response('success');
        }
        else return response('error', 403);
    }

    public function deactivate($id)
    {
        $order = Order::find($id);
        if ($order->status == 'active'){
            DB::transaction(function () use($order) {
                $log = ManualActivation::forceCreate([
                   'phone_number' => $order->phone_id,
                    'sim_number' => $order->sim_id,
                    'call' => 'deactivate',
                    'old_time' => $order->departure,
                    'order_id' => $order->id,
                ]);
                $order->departure = Carbon::now()->format('d/m/Y H:i');
                $order->to = Carbon::now()->timestamp;
                $order->save();
                $this->helper->deactivate($order->id);
            }, 5);

        }

        else {return response('suspension error', 403);}
        return response('success');
    }

    public function sendMail($orderID, Request $request)
    {
        $this->validate(request(), [
        'email' => 'required|email'
        ]);

        $data = array(
           'order' => $orderID,
            'text' => $request->input('remark')
        );

        Mail::to($request->input('email'))->queue(new OrderMail($data));


    }

    public function search(Request $request)
    {
        $query = stripcslashes($request->input('query'));
        $net = $this->userManager->getNetworkFromCache(Auth::user()->id);

        $result = Order::where(function ($q) use ($query) {
            $q->whereIn('phone_id', function ($q) use ($query) {
                $q->select('id')->from('phones')->
                where('phone', 'LIKE', '%' . $query . '%');
            })
                ->orWhereIn('sim_id', function ($q) use ($query) {
                $q->select('id')->from('sims')->where('number', 'LIKE', '%' . $query . '%');
            })
                ->orWhere('reference_number', 'LIKE', '%' . $query . '%');
        })
            ->whereIn('created_by', $net)
            ->paginate(env('PAGINATE_DEFAULT'));

        $ordersArray = $this->viewHelper->solveOrderList($result);
        $counts = $this->viewHelper->getCounts($this->userManager);

        return view('home', compact('ordersArray'), compact('counts'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $orders = Order::with(['phone'  => function ($q){
            $q->withTrashed();
        }, 'sim'  => function ($q){
            $q->withTrashed();
        }, 'creator'  => function ($q){
            $q->withTrashed();
        }, 'editor'  => function ($q){
            $q->withTrashed();
        }, 'package', 'sim.provider']);
        if ($request->has('filter')){
            $orders = $orders->filter($request->input('filter'));
        }
        else $orders = $orders->where('id', '>', 0);

        if ($user->level != 'Super admin'){
            $net = $this->userManager->getNetworkFromCache(Auth::user()->id);
            $orders = $orders->whereIn('created_by', $net)->orderby('id', 'desc');
        }

//        $ordersArray = $this->viewHelper->prepareExport($this->viewHelper->solveOrderList($orders), 'order');

        Excel::create('Orders', function($excel) use ($orders) {

            $excel->sheet('orders', function($sheet) use($orders) {
                $sheet->appendRow(array(
                    'ID', 'Phone', 'Sim number', 'Provider', 'Type', 'From', 'To', 'Dealer', 'Reference #', 'Status'
                ));
                $sheet->setColumnFormat(array('C' => '0', 'I' => '@ '));
                $sheet->freezeFirstRowAndColumn();

                $orders->chunk(100, function($rows) use ($sheet)
                {
                    foreach ($rows as $row)
                    {
                        $sheet->appendRow(array(
                            $row->id,
                            (($row->phone == null) ? 'No Number' : $row->phone->phone),
                            $row->sim->number,
                            $row->sim->provider->name,
                            $row->package->name, $row->landing, $row->departure, $row->creator->login,
                            $row->reference_number, $row->status
                        ));
                    }
                });
            });

        })->download('xlsx');
    }

    public function orderTable (Request $request)
    {
        $q = $request->all();
//        dd($q);


        //
        $user = Auth::user();
        $orders = new Order();


        if ($user->level != 'Super admin'){

            $net = $this->userManager->getNetworkFromCache($user->id);
            $orders = $orders->whereIn('created_by', $net);
//            dd($orders);

        }


        if ($request->has('sort')){
            if ($q['sort'] == 'phone.phone'){
                $orders = $orders->join('phones', 'orders.phone_id', '=', 'phones.id')
                    ->select('orders.*', 'phones.phone')
                    ->orderBy('phone', $request->input('order'));

            }
            elseif ($q['sort'] == 'sim.number'){
                $orders = $orders->join('sims', 'orders.sim_id', '=', 'sims.id')
                    ->select('orders.*', 'sims.number')
                    ->orderBy('number', $request->input('order'));

            }
            elseif ($q['sort'] == 'landing'){
                $orders = $orders->orderBy('from', $q['order']);
            }
            elseif ($q['sort'] == 'departure'){
            $orders = $orders->orderBy('to', $q['order']);
            }
            elseif ($q['sort'] == 'creator.login'){
                $orders = $orders->join('users', 'orders.created_by', '=', 'users.id')
                    ->select('orders.*', 'users.login')
                    ->orderBy('login', $request->input('order'));

            }
            elseif ($q['sort'] == 'status'){
                $orders = $orders->orderBy('status', $q['order']);
            }
//            $orders = $orders->with(['phone', 'sim', 'creator', 'sim.provider']);


        }
        else {
            $orders = $orders->orderBy('id', 'desc');
        }
        if ($request->has('search')){

            $qs = $request->input('search');
            $orders = $orders->where(function ($q) use ($qs) {
                $q->orWhereIn('orders.phone_id', function ($q) use ($qs) {
                    $q->select('id')->from('phones')->
                    where('phone', 'LIKE', '%' . $qs . '%');
                })
                    ->orWhereIn('sim_id', function ($q) use ($qs) {
                        $q->select('sims.id')->from('sims')->where('number', 'LIKE', '%' . $qs . '%');
                    })
                    ->orWhere('reference_number', 'LIKE', '%' . $qs . '%');
            });
        }
        if ($request->has('filter')){
            $orders = $orders->where('status', $q['filter']);
        }

        $total = clone $orders;
        $total = $total->count();
        if ($q['offset'] != 0 && ($q['offset']/$q['limit']) >= (ceil($total/$q['limit'])))
            $q['offset'] = 0;
        $orders = $orders->with(['phone'  => function ($q){
            $q->withTrashed();
        }, 'sim'  => function ($q){
            $q->withTrashed();
        }, 'creator'  => function ($q){
            $q->withTrashed();
        }, 'editor'  => function ($q){
            $q->withTrashed();
        }, 'package', 'sim.provider'])->take($q['limit'])->skip($q['offset'])->get();

        return  response()->json(['total' => $total, 'rows' => $orders]);
    }


}
