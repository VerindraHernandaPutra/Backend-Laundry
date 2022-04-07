<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Paket;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class PaketController extends Controller
{

    // Insert Function for Paket (Insert new Paket data to database)
    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'jenis' => 'required',
            'harga' => 'required|integer'
		]);

		if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $data
            ]);
		}

		$paket = new Paket();
		$paket->jenis = $request->jenis;
        $paket->harga = $request->harga;
		$paket->save();

        $data = Paket::where('id_paket','=', $paket->id_paket)->first();
        return response()->json([
            'success' => true,
            'message' => 'Data Paket berhasil ditambahkan!',
            'data' => $data
        ]);
    }

    // Update Function for Paket (Update the Paket data by ID from database)
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
			'jenis' => 'required',
            'harga' => 'required|integer'
		]);

		if($validator->fails()){
            return $this->response->errorResponse($validator->errors());
		}

		$paket = Paket::where('id_paket', $id)->first();
		$paket->jenis = $request->jenis;
        $paket->harga = $request->harga;
		$paket->save();

        return response()->json([
            'success' => true,
            'message' => 'Data Paket berhasil diubah!',
            'data' => $paket
        ]);
    }

    // Delete Function for Paket (Delete the Paket data by ID from database)
    public function delete($id)
    {
        $delete = Paket::where('id_paket', $id)->delete();

        if($delete){
            return response()->json([
                'success' => true,
                'message' => 'Data Paket berhasil dihapus!',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data Paket gagal dihapus!',
            ]);
        }
    }

    // Get Function for Paket (Get all Paket data from database)
    public function getAll($limit = NULL, $offset = NULL)
    {
        $data["count"] = Paket::count();
        
        if($limit == NULL && $offset == NULL){
            $data["paket"] = Paket::get();
        } else {
            $data["paket"] = Paket::take($limit)->skip($offset)->get();
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Get Function for Paket (Get Paket data by ID from database)
    public function getById($id)
    {   
        $data["paket"] = Paket::where('id_paket', $id)->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
