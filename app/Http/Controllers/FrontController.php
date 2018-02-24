<?php namespace Cmisoft\Http\Controllers;

use Cmisoft\Http\Requests;
use Cmisoft\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class FrontController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('index');
	}

	public function semaforo()
	{
		return view('semaforo');
	}

	public function parametros()
	{
		return view('parametros');
	}

	public function mapa()
	{
		return view('mapa');
	}

	public function agendador()
	{
		return view('agendador');
	}

	public function software()
	{
		return Redirect::to('FERCO/index.php');
	}

	public function admin()
	{
		return view('admin.index');
	}

	public function __construct(){
		// $this->middleware('auth', ['only'=>['semaforo', 'usuarios', 'indicadores', 'software', 'parametros']]);
		$this->middleware('auth', ['only'=>['semaforo', 'usuarios', 'indicadores', 'software', 'parametros']]);
		$this->middleware('admin', ['only'=>['usuarios', 'indicadores', 'parametros']]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
