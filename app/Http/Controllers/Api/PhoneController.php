<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Phone;
use Excel;
use Storage;
use Carbon\Carbon;

class PhoneController extends Controller
{

    public function __construct()
    {
        $this->middleware('superAdmin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $phones = Phone::orderby('id', 'desc')->paginate(env('PAGINATE_DEFAULT'));
        $phonesArray = $this->solvePhoneList($phones);

//      dd($phonesArray);
        return view('number', compact('phonesArray'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        dd($request->all());
        //
        $this->validate(request(), [
            'phone' => 'required',
//            'state' => 'required',
            'initial_sim_id' => 'required| unique:phones',
//            'current_sim_id' => 'required',
            'package_id' => 'required',
            'provider_id' => 'required',
//            'is_special' => 'required',
//            'is_active' => 'required',
//
        ]);
        $number = $this->validateNumber($request->input('phone'));


        $isSpecial = 0;
        if ($request->input('is_special') == 'true')
            $isSpecial = 1;

        $newPhone = Phone::forceCreate([

            'phone' => $number,
            'state' => 'not in use',
            'initial_sim_id' => $request->input('initial_sim_id'),
            'current_sim_id' => $request->input('initial_sim_id'),
            'package_id' => $request->input('package_id'),
            'provider_id' => $request->input('provider_id'),
            'is_special' => $isSpecial,
            'is_active' => '1',


        ]);

        if($newPhone)
            return $newPhone;
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
        $number = Phone::find($id);
        $number = $this->solvePhoneList([$number]);
        return response($number);

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
        //
        $this->validate(request(), [
            'phone' => 'required',
//            'state' => 'required',
//            'initial_sim_id' => 'required',
//            'current_sim_id' => 'required',
            'package_id' => 'required',
            'provider_id' => 'required',
//            'is_special' => 'required',
//            'is_active' => 'required',

        ]);

        $isSpecial = 0;
        if ($request->input('is_special') == 'true')
            $isSpecial = 1;
        //
        $phone = Phone::find($id);

        if ($phone->phone != $request->input('phone')){
            $this->validate(request(), ['phone' => 'unique:phones']);
        }
        $number = $this->validateNumber($request->input('phone'));
        $phone->phone = $number;
//        $phone->state = 'not in use';
        if ($request->has('initial_sim_id')){
            $phone->initial_sim_id = $request->input('initial_sim_id');
            $phone->current_sim_id = $request->input('initial_sim_id');

        }
//         $phone-> current_sim_id = $request->input('lending');
        $phone->package_id = $request->input('package_id');
//        $phone->provider_id = $request->input('provider_id');
        if ($request->has('is_special'))
        $phone->is_special = $isSpecial;

//        $phone->is_active = $request->input('is_active');

        $phone->save();

        return response('number edited.');

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
        $phone = Phone::find($id);
        if ($phone != null & $phone->state == 'not in use')
            $phone->delete();
        else
            return response()->json(['number' => 'deletion not allowed'], 403);

        return response()->json(['number' => 'deleted'], 200);


    }

    public function solvePhoneList($phones){
        $phonesArray = $phones;
        foreach ($phones as $key => $phone) {

            if ($phone->sim != null){
                $phonesArray[$key]['current_sim_id'] = $phone->sim->number;
            $phonesArray[$key]['provider_id'] = $phone->sim->provider->name;
            }
            if ($phone->package != null)
            $phonesArray[$key]['package_id'] = $phone->package->name;
            if ($phonesArray[$key]['deleted_at'] != null){
                $phonesArray[$key]['deleted'] = $phonesArray[$key]['deleted_at']->format('d/m/Y H:i');
//                dd($phonesArray[$key]['deleted']);
            }
//            $phonesArray[$key]['package_id'] = $phone->package->name;
        }
        return $phonesArray;
    }

    public function filter($filter){

        if ($filter == 'special')
            $phones = Phone::where('is_special', 1)->orderby('id', 'desc')->paginate(env('PAGINATE_DEFAULT'));
        elseif ($filter == 'deleted')
            $phones = Phone::onlyTrashed()->orderby('id', 'desc')->paginate(env('PAGINATE_DEFAULT'));
        else
        $phones = Phone::filter($filter)->orderby('id', 'desc')->paginate(env('PAGINATE_DEFAULT'));
        $phonesArray = $this->solvePhoneList($phones);

        return view('number', compact('phonesArray'));
//        dd($phonesArray);
    }

    public function export(Request $request)
    {
        $phones =Phone::with([
            'sim'  => function ($q){
                $q->withTrashed();
            }, 'parking_sim'  => function ($q){
                $q->withTrashed();
            }, 'provider'  => function ($q){
                $q->withTrashed();
            }, 'package',
        ]);
        if ($request->has('filter')){
            if ($request->input('filter') == 'deleted')
             $phones = $phones->onlyTrashed();
            elseif ($request->input('filter') == 'specials')
                $phones = $phones->where('is_special', 1);
            else    $phones = $phones->filter($request->input('filter'));
        }

        Excel::create('phone', function($excel) use ($phones) {
            $excel->sheet('phone', function($sheet) use($phones) {

                $sheet->appendRow(array(
                    'ID', 'Number', 'Status', 'Parking SIM', 'Current SIM', 'Package name', 'Provider', 'Special'
                ));
                $sheet->setColumnFormat(array('B' => '0', 'D' => '0', 'E' => '0'));
                $sheet->freezeFirstRowAndColumn();

                $phones->chunk(100, function($rows) use ($sheet)
                {
                    foreach ($rows as $row)
                    {
                        $sheet->appendRow(array(
                            $row->id, $row->phone, (($row->deleted_at == null)? $row->state : 'deleted'), $row->parking_sim->number, $row->sim->number,
                            $row->package->name, $row->provider->name,  (($row->is_special) ? 'Yes' : 'No')
                        ));
                    }
                });

            });
        })->export('xlsx');

    }

    public function search(Request $request)
    {
        $result = Phone::where('phone', 'LIKE', '%'.$request->input('query').'%')
                ->paginate(env('PAGINATE_DEFAULT'));
        $phonesArray = $this->solvePhoneList($result);

//        dd($phonesArray);
        return view('number', compact('phonesArray'));
    }

    public function import (Request $request)
    {
        $file = null;

        if ($request->hasFile('number-file')){
            $file =  $request->file('number-file')->storeAs('public/phone', 'sim_import.xlsx');


            Excel::load('../storage/app/'.$file, function($reader) {

                // Getting all results
                $reader->ignoreEmpty();
                $results = $reader->get();
                foreach ($results as $row){
                $entity = $row->toArray();
                    $entity['phone'] = $this->validateNumber($entity['phone']);
                    $entity['state'] = 'not in use';
                    $entity['current_sim_id'] = $entity['initial_sim_id'];

                    $query = Phone::forceCreate($entity);
                    if ($query) continue;
                    else {
                        return response('file content error', 443);
//                        return false;
                    }
//
                }


            });

            Storage::delete($file);
        }
        else {return response()->json(['error uploading file'], 443);}
        return response()->json([$file]);

    }

    protected function validateNumber($number)
    {
        $number = (string)$number;
        if ($number[0] == 0)
            return $number;
        else return substr_replace($number,'0',0,0);
    }

    public function specials($packageID)
    {
//        dd( Phone::select('id', 'phone')->where('is_special', 1)->where('package_id', $packageID)->get()->toArray());
        return Phone::select('id', 'phone')->where('is_special', 1)->where('package_id', $packageID)->where('state', 'not in use')->get()->toArray();
    }

    public function recover($id)
    {
        $number = Phone::onlyTrashed()->find($id);
        $number->restore();
        return $number;
    }

    public function cli()
    {
        return view('cli')->with('viewName', 'cli');
    }
    public function cliCheck(Request $request)
    {
        $this->validate(request(), ['number' => 'required']);
        $number = $this->validateNumber($request->input('number'));
        $res = file_get_contents("http://176.35.171.143:8086/api/vfapi.php?key=7963ad6960b1088b94db2c32f2975e86&call=simcheck&cli=".$number);
        return $res;

    }
    public function numberTable (Request $request)
    {
        $q = $request->all();
        $numbers = new Phone();

        if ($request->has('sort')){
            if ($q['sort'] == 'id'){
                $numbers = $numbers->orderBy('id', $request->input('order'));
            }
            elseif ($q['sort'] == 'phone'){
                $numbers = $numbers->orderBy('phone', $request->input('order'));
            }
            elseif ($q['sort'] == 'sim.number'){
                $numbers = $numbers->join('sims', 'phones.current_sim_id', '=', 'sims.id')
                    ->select('phones.*', 'sims.number')
                    ->orderBy('number', $request->input('order'));
            }
            elseif ($q['sort'] == 'package.name'){
                $numbers = $numbers->join('packages', 'phones.package_id', '=', 'packages.id')
                    ->select('phones.*', 'packages.name')
                    ->orderBy('name', $request->input('order'));
            }
            elseif ($q['sort'] == 'state'){
                $numbers = $numbers->orderBy('state', $q['order']);
            }
            elseif ($q['sort'] == 'deleted_at'){
                $numbers = $numbers->orderBy('deleted_at', $q['order']);
            }
//
        }
        else {
            $numbers = $numbers->orderBy('id', 'desc');
        }
        if ($request->has('search')){
            $qs = $request->input('search');
            $numbers = $numbers->withTrashed()->where('phone', 'LIKE', '%'.$request->input('search').'%');
        }
        if ($request->has('filter')){
            if ($request->input('filter') == 'deleted')
                $numbers = $numbers->onlyTrashed();
            elseif ($request->input('filter') == 'specials') {
                $numbers = $numbers->where('is_special', 1);
            }
            else
            $numbers = $numbers->where('state', $q['filter']);
        }
        $total = clone $numbers;
        $total = $total->count();
        if ($q['offset'] != 0 && ($q['offset']/$q['limit']) >= (ceil($total/$q['limit'])))
            $q['offset'] = 0;
        $numbers = $numbers->with(['sim'  => function ($q){
            $q->withTrashed();
        }, 'parking_sim'  => function ($q){
            $q->withTrashed();
        }, 'package' => function ($q) {
            $q->withTrashed();
        }, 'sim.provider'])->take($q['limit'])->skip($q['offset'])->get();

        return  response()->json(['total' => $total, 'rows' => $numbers]);
    }


}
