<?php

namespace Cmisoft\Http\Controllers;

use Cmisoft\Http\Requests;
use Cmisoft\Http\Requests\LiderRequest;
use Cmisoft\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Cmisoft\Lider;
use Cmisoft\Puesto;
use Cmisoft\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class LiderController extends Controller
{
    public function __construct() {
        $this->beforeFilter('@find',['only'=>['edit','update','destroy']]);
    }

    public function find(Route $route) {
        $this->lider = Lider::find($route->getParameter('id'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function listing() {
        $liders = Lider::all();

        return response()->json(
            $liders->toArray()
        );
    }

    public function index()
    {   
        $liders = Lider::paginate(10);
        return view('lider.index',compact('liders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $puestos = Puesto::lists('nombre','id');
        $users = User::lists('name','id');
        return view('lider.create',compact('puestos'),compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LiderRequest $request)
    {
        if($request->ajax()){
            Lider::create($request->all());
            return response()->json([
                'message' => "creado OK",
            ]);
        }
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
    }
}
