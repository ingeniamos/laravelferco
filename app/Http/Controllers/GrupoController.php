<?php

namespace Cmisoft\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Database\QueryException;

use Cmisoft\Grupo;
use Cmisoft\Http\Requests;
use Cmisoft\Http\Requests\GrupoRequest;
use Cmisoft\Http\Controllers\Controller;

class GrupoController extends Controller
{
    public function __construct(){
        $this->beforeFilter('@find',['only'=>['edit','update','destroy']]);
    }

    public function find(Route $route){
        $this->grupo = Grupo::find($route->getParameter('grupo'));
    }

    public function listing() {
        $grupo = Grupo::all();

        return response()->json(
            $grupo->toArray()
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
    public function store(GrupoRequest $request)
    {
        if($request->ajax()){
            $nombre = $request->nombre;
            Grupo::create($request->all());
            return response()->json([
                'message' => "Grupo <b>".$nombre."</b> creado correctamente",
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
    public function destroy()
    {
        try {
            $this->grupo->delete();
            return response()->json(["message"=>"Grupo borrado correctamente"]);
        } catch (QueryException $e) {
            if ($e->getCode()==23000)
            return response()->json(["message"=>"El grupo que intenta borrar posee subgrupos"]);
        }        
    }
}
