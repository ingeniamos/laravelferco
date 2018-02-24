<?php 

namespace Cmisoft\Http\Controllers;

use Cmisoft\Agenda;
use Cmisoft\Http\Requests;
use Cmisoft\Http\Requests\AgendaRequest;
use Cmisoft\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->beforeFilter('@find',['only'=>['edit','update','destroy']]);
    }

    public function find(Route $route){
        $this->agenda = Agenda::find($route->getParameter('agenda'));
    }

    public function listing() {
        $agenda = Agenda::all();

        return response()->json(
            $agenda ->toArray()
        );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuario = Auth::user()->id;
        $agenda = Agenda::all();
        $filter = $agenda->where('responsable', $usuario)->sortBy('fecha_limite');
        return response()->json(
            $filter->values()->all()
        );
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
    public function store(AgendaRequest $request)
    {
        if($request->ajax()){
            Agenda::create($request->all());
            return response()->json([
                'message' => "Actividad agendada correctamente ".$usuario,
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
