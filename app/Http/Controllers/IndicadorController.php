<?php

namespace Cmisoft\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Database\QueryException;

use Cmisoft\Indicador;
use Cmisoft\Http\Requests;
use Cmisoft\Http\Requests\IndicadorRequest;
use Cmisoft\Http\Controllers\Controller;

class IndicadorController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('admin');
        $this->beforeFilter('@find',['only'=>['edit','update','destroy']]);
    }

    public function find(Route $route){
        $this->indicador = Indicador::find($route->getParameter('indicador'));
    }

    public function listing() {
        $indicador = Indicador::all();

        return response()->json(
            $indicador->toArray()
        );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('indicadores');
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
    public function store(IndicadorRequest $request)
    {
        if($request->ajax()){
            $nombre = $request->nombre;
            Indicador::create($request->all());
            return response()->json([
                'message' => "Indicador <b>".$nombre."</b> creado correctamente",
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
        return response()->json(
            $this->indicador->toArray()
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(IndicadorRequest $request)
    {
        // $this->indicador->fill($request->all());
        // $this->indicador->save();

        // return response()->json([
        //     "mensaje" => "Datos actualizados",
        // ]);

        if($request->ajax()){
            $nombre = $request->nombre;
            $this->indicador->fill($request->all());
            $this->indicador->save();
            return response()->json([
                'message' => "Indicador <b>".$nombre."</b> actualizado correctamente",
            ]);
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
        try {
            $this->indicador->delete();
            return response()->json(["message"=>"Indicador borrado correctamente"]);
        } catch (QueryException $e) {
            if ($e->getCode()==23000)
            return response()->json(["message"=>"El indicador que intenta borrar está siendo utilizado"]);
        }
    }
}
