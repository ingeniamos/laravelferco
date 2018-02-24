<?php

namespace Cmisoft\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use Cmisoft\Subgrupo;
use Cmisoft\Http\Requests;
use Cmisoft\Http\Requests\SubgrupoRequest;
use Cmisoft\Http\Controllers\Controller;

class SubgrupoController extends Controller
{
    public function __construct(){
        $this->beforeFilter('@find',['only'=>['edit','update','destroy']]);
    }

    public function find(Route $route){
        $this->subgrupo = Subgrupo::find($route->getParameter('subgrupo'));
    }

    public function listing() {
        $subgrupo = Subgrupo::all();

        return response()->json(
            $subgrupo->toArray()
        );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(SubgrupoRequest $request)
    {
        if($request->ajax()){
            $nombre = $request->nombre;
            Subgrupo::create($request->all());
            return response()->json([
                'message' => "Subgrupo <b>".$nombre."</b> creado correctamente",
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
        $this->subgrupo->delete();
        return response()->json(["message"=>"Subgrupo borrado correctamente"]);
    }
}
