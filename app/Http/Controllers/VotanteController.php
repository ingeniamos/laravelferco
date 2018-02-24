<?php

namespace Cmisoft\Http\Controllers;
use Illuminate\Http\Request;
use Cmisoft\Lider;
use Cmisoft\Votante;
use Cmisoft\Puesto;
use Cmisoft\User;
use Cmisoft\Http\Requests;
use Cmisoft\Http\Requests\VotanteRequest;
use Cmisoft\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Routing\Route;


class VotanteController extends Controller
{
    public function listing() {
        $votantes = Votante::all();

        return response()->json(
            $votantes->toArray()
        );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $votantes = Votante::paginate(10);
        return view('votante.index',compact('votantes'));
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
        $liders = Lider::lists('nombre','id');
        return view('votante.create',compact('puestos'),compact('liders'),compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VotanteRequest $request)
    {
        if($request->ajax()){
            Votante::create($request->all());
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
