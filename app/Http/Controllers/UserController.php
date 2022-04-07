<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
 
class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ]);
        }

        $credentials = $request->only('username', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Username or Password is wrong'
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => true,
                'message' => 'Could not create token'
                ]);
        }
        $data = [
            'token'=> $token,
            'user' => JWTAuth::user()
        ];

        return response()->json([
            'success' => true,
            'message' => 'Login Success',
            'data' => $data
        ]);
    }
 
    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:User',
            'id_outlet' => 'required',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = new User();
		$user->nama 	= $request->nama;
		$user->username = $request->username;
		$user->role 	= $request->role;
        $user->id_outlet 	= $request->id_outlet;
		$user->password = Hash::make($request->password);
		$user->save();

        $data = User::where('username','=', $request->username)->first();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambahkan user baru',
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
			'nama' => 'required|string|max:255',
            'username' => 'required|string|max:50',
            'id_outlet' => 'required',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
		]);

		if($validator->fails()){
            return $this->response->errorResponse($validator->errors());
		}

		$user = User::where('id_user', $id)->first();
        $user->nama = $request->nama;
		$user->username = $request->username;
		$user->role 	= $request->role;
        $user->id_outlet 	= $request->id_outlet;
		$user->password = Hash::make($request->password);
		$user -> save();

        return response()->json([
            'success' => true,
            'message' => 'Data User berhasil diubah!',
            'data'    => $user
        ]);
    }

    public function delete($id)
    {
        $delete = User::where('id_user', $id) -> delete();

        if($delete){
            return response()->json([
                'success' => true,
                'message' => 'Data User berhasil dihapus!',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data User gagal dihapus!',
            ]);
        }
    }

    public function getAll($limit = NULL, $offset = NULL)
    {
        $data["count"] = User::count();
        $data['user'] = User::with('outlet')->get();

        return response() -> json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getById($id)
    {   
        $data["user"] = User::where('id_user', $id)->get();

        return response() -> json([
            'success' => true,
            'data'    => $data
        ]);
    }
 
    public function loginCheck(){
		try {
			if(!$user = JWTAuth::parseToken()->authenticate()){
				return $this->response->errorResponse('Invalid token!');
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
			return response()->json([
                'success' => false,
                'message' => 'Token Expired.',
            ]);
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
			return response()->json([
                'success' => false,
                'message' => 'Token invalid.',
            ]);
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e){
			return response()->json([
                'success' => false,
                'message' => 'Authorization token not found.',
            ]);
		}

        return response()->json([
            'success' => true,
            'message' => 'Authentication success',
            'data' => $user
        ]);

	}

    public function logout(Request $request)
    {
        if(JWTAuth::invalidate(JWTAuth::getToken())) {
            return response()->json([
                'success' => true,
                'message' => 'You are logged out.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Logged out failed.',
            ]);
        }
    }

}
