<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class MemberController extends Controller
{

    // Insert Function for Member (Insert new member data to database)
    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'nama'          => 'required|string',
            'alamat'        => 'required|string',
            'jenis_kelamin' => 'required',
            'telp'          => 'required|string'
		]);

		if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $data
            ]);
		}

		$member = new Member();
		$member -> nama          = $request->nama;
        $member -> alamat        = $request->alamat;
        $member -> jenis_kelamin = $request->jenis_kelamin;
        $member -> telp          = $request->telp;
		$member -> save();

        $data = Member::where('id_member','=', $member -> id_member) -> first();
        return response()->json([
            'success' => true,
            'message' => 'Data Member berhasil ditambahkan!',
            'data'    => $data
        ]);
    }

    // Update Function for Member (Update the member data by ID from database)
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
			'nama'          => 'required|string',
            'alamat'        => 'required|string',
            'jenis_kelamin' => 'required',
            'telp'          => 'required|string'
		]);

		if($validator->fails()){
            return $this->response->errorResponse($validator->errors());
		}

		$member = Member::where('id_member', $id)->first();
		$member -> nama          = $request -> nama;
        $member -> alamat        = $request -> alamat;
        $member -> jenis_kelamin = $request -> jenis_kelamin;
        $member -> telp          = $request -> telp;
		$member -> save();

        return response()->json([
            'success' => true,
            'message' => 'Data Member berhasil diubah!',
            'data'    => $member
        ]);
    }

    // Delete Function for Member (Delete the member data by ID from database)
    public function delete($id)
    {
        $delete = Member::where('id_member', $id) -> delete();

        if($delete){
            return response()->json([
                'success' => true,
                'message' => 'Data Member berhasil dihapus!',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data Member gagal dihapus!',
            ]);
        }
    }

    // Get Function for Member (Get all member data from database)
    public function getAll($limit = NULL, $offset = NULL)
    {
        $data["count"] = Member::count();
        
        if($limit == NULL && $offset == NULL){
            $data["member"] = Member::get();
        } else {
            $data["member"] = Member::take($limit)->skip($offset)->get();
        }

        return response() -> json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Get Function for Member (Get member data by ID from database)
    public function getById($id)
    {   
        $data["member"] = Member::where('id_member', $id)->get();

        return response() -> json([
            'success' => true,
            'data'    => $data
        ]);
    }
}
