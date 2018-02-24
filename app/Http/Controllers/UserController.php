<?php 

namespace Cmisoft\Http\Controllers;

use Cmisoft\User;
use Cmisoft\Http\Requests;
use Cmisoft\Http\Requests\userCreateRequest;
use Cmisoft\Http\Requests\userUpdateRequest;
use Cmisoft\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class UserController extends Controller {

	public function __construct(){
		$this->middleware('auth');
		$this->middleware('admin');
		$this->beforeFilter('@find',['only'=>['edit','update','destroy']]);
	}

	public function find(Route $route){
		$this->user = User::find($route->getParameter('user'));
	}

	public function listing() {
        $user = User::all();

        return response()->json(
            $user->toArray()
        );
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$users = User::paginate(10);
		return view('user.index',compact('users'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('user.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(userCreateRequest $request)
	{
		//User::create($request->all());
		// User::create([
		// 	'name'=> $request['name'],
		// 	'email'=>$request['email'],
		// 	'password'=>bcrypt($request['password']),
		// ]);
		// Session::flash('message','Usuario creado correctamente');
		// return redirect('/user')->with('message','Usuario creado correctamente');

		if($request->ajax()){
            $nombre = $request->nombre;
            User::create($request->all());
            return response()->json([
                'message' => "Usuario <b>".$nombre."</b> creado correctamente",
            ]);
        }

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
		// return view('user.edit', ['user'=>$this->user]);
		return response()->json(
            $this->user->toArray()
        );
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(userCreateRequest $request)
	{
		if($request->ajax()){
            $nombre = $request->nombre;
            $this->user->fill($request->all());
            $this->user->save();
            return response()->json([
                'message' => "Usuario <b>".$nombre."</b> actualizado correctamente",
            ]);
        }
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try {
            $this->user->delete();
            return response()->json(["message"=>"Usuario borrado correctamente"]);
        } catch (QueryException $e) {
            if ($e->getCode()==23000)
            return response()->json(["message"=>"El usuario que intenta borrar está siendo utilizado"]);
        }
	}

}
