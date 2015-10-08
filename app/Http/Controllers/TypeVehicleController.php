<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\TypeVehicleRepositoryEloquent;
use App\Entities\TypeVehicle;
use App\Entities\ModelVehicle;
use Log;
use Input;
use Lang;
use Session;
use Redirect;
use Response;
use Illuminate\Support\Facades\View;
use Prettus\Validator\Exceptions\ValidatorException;

class TypeVehicleController extends Controller
{

    protected $repository;
    
    public function __construct(TypeVehicleRepositoryEloquent $repository)
    {
        $this->middleware('auth');
        $this->repository = $repository;
        //$mp = new ModelVehicleController();
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $typevehicles = $this->repository->all();
        if (Request::isJson()) {
            return $typevehicles;
        }
        return View::make("typevehicle.index", compact('typevehicles'));        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $typevehicle = new TypeVehicle();
        return view("typevehicle.edit", compact('typevehicle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $this->repository->validator();
            $this->repository->create(Input::all());
            Session::flash(
                'message',
                Lang::get(
                    'general.succefullcreate',
                    ['table'=> Lang::get('general.TypeVehicle')]
                )
            );
            return Redirect::to('typevehicle');
        } catch (ValidatorException $e) {
            return Redirect::back()->withInput()
                   ->with('errors', $e->getMessageBag());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $typevehicle= $this->repository->find($id);
        return View::make("typevehicle.show", compact('typevehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $typevehicle = $this->repository->find($id);
        return View::make("typevehicle.edit", compact('typevehicle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int     $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $this->repository->validator();
            $this->repository->update(Input::all(), $id);
            Session::flash(
                'message',
                Lang::get(
                    'general.succefullupdate',
                    ['table'=> Lang::get('general.TypeVehicle')]
                )
            );
            return Redirect::to('typevehicle');
        } catch (ValidatorException $e) {
            return Redirect::back()->withInput()
                    ->with('errors', $e->getMessageBag());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        Log::info('Delete field: '.$id);

        if ($this->repository->find($id)) {
            if (\Illuminate\Support\Facades\DB::table('model_vehicles')->where('type_vehicle_id', $id)->count() > 0) {
                Session::flash('danger', Lang::get(
                    "general.notdeletedregister",
                    array("tabela" => Lang::get("general.TypeVehicle"))
                ));
            } else {
                $this->repository->delete($id);
                Session::flash('message', Lang::get("general.deletedregister"));
            }
        }
        return Redirect::to('typevehicle');
    }
}
