<?php namespace Elemental\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Services\Registrar;
use App\User;
use Validator;
use Auth;

class UserController extends Controller {

	protected $request;
	protected $user;
	protected $registrar;

    public function __construct(Request $request, User $user, Registrar $registrar)
    {
        $this->request = $request;
        $this->user = $user;
        $this->registrar = $registrar;
        $this->middleware('auth');
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$users = $this->user->all()->toArray();
					
		if(count($users) === 1 ) {
			$users = [array_pop($users)];
		}

		return response()->json($users, 200);

	}



	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = $this->request->all();
		$validator = $this->registrar->validator($input);

		if ($validator->fails())
		{
			return response()->json(['ok'=>0, 'errors'=>$validator->errors()->all()], 500);  
			
		} else {
			try {
				$this->registrar->create($input);
				return response()->json(['ok' => 1]);
			} catch (Exception $e) {
				return response()->json(['ok'=>0, 'errors'=>$e->getMessage()], 500);  
			}
		}
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$user= $this->user->find($id)->toArray();
		return response()->json($user, 200);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = $this->request->all();
		$validator = Validator::make($input,
			[
				'name' => 'required',
				'email' => 'required|email',
				'password' => 'sometimes|required|confirmed|min:6',
			]
		);

		if ($validator->fails())
		{
			return response()->json(['ok'=>0, 'errors'=>$validator->errors()->all()], 500);  
			
		} else {
	
			try {
				$this->user->fill($input);
				return response()->json(['ok' => 1]);
			} catch (Exception $e) {
				return response()->json(['ok'=>0, 'errors'=>$e->getMessage()], 500);  
			}
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
			$this->user->destroy($id);
			return response()->json(['ok' => 1]);
		} catch (Exception $e) {
			return response()->json(['ok'=>0, 'errors'=>$e->getMessage()], 500);  
		}
	}

}
