<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class TransaksiController extends Controller
{

    // Insert Function for Transaksi (Insert new Transaksi data to database)
    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'id_member' => 'required|numeric',
            'tgl' => 'required|date',
            'lama_pengerjaan' => 'required|integer',
            'id_user' => 'required|numeric'
		]);

		if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ]);
		}

        // Deadline (Batas Waktu)
        $tgl_transaksi = date_create($request->tgl);
        date_add($tgl_transaksi, date_interval_create_from_date_string($request->lama_pengerjaan. "days"));
        $batas_waktu = date_format($tgl_transaksi, 'Y,m,d');

		$transaksi = new transaksi();
		$transaksi->id_member = $request->id_member;
        $transaksi->tgl = $request->tgl;
        $transaksi->batas_waktu = $batas_waktu;
        $transaksi->id_user = $request->id_user;
		$transaksi->save();

        for($i = 0; $i < count($request->detail); $i++) {
            $detail_transaksi = new DetailTransaksi();
            $detail_transaksi -> id_transaksi = $transaksi->id_transaksi;
            $detail_transaksi -> id_paket = $request -> detail [$i]['id_paket'];
            $detail_transaksi -> weight = $request -> detail [$i]['weight'];
            $detail_transaksi -> save();    
        }

        $data = Transaksi::where('id_transaksi','=', $transaksi->id_transaksi)->first();
        return response()->json([
            'success' => true,
            'message' => 'Data Transaksi berhasil ditambahkan!',
            'data' => $data
        ]);
    }

    // Update Function for Transaksi (Update the 'Status' data from 'Transaksi' database)
    public function update_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'id_transaksi' => 'required|numeric',
            'status' => 'required|string',
		]);

		if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ]);
		}

		$transaksi = Transaksi::where('id_transaksi', $request->id_transaksi)->first();
		$transaksi -> status = $request->status;
		$transaksi->save();

        return response()->json([
            'success' => true,
            'message' => 'Data Transaksi berhasil diubah menjadi '.$request->status,
        ]);
    }

    // Update Function for Transaksi (Update the 'Bayar' data from 'Transaksi' database)
    public function update_bayar(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'id_transaksi' => 'required|numeric',
            'dibayar' => 'required|string',
		]);

		if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ]);
		}

		$transaksi = Transaksi::where('id_transaksi', $request->id_transaksi)->first();
		$transaksi -> dibayar = $request->dibayar;

        if ($request->dibayar == 'dibayar') {
            $transaksi->tgl_bayar = date('Y-m-d H:i:s');
        } else {
            $transaksi->tgl_bayar = NULL;
        }

		$transaksi->save();

        return response()->json([
            'success' => true,
            'message' => 'Data Pembayaran berhasil diubah menjadi '.$request->dibayar,
        ]);
    }

    // Delete Function for Transaksi (Delete the Transaksi data by ID from database)
    public function delete($id)
    {
        $delete = Transaksi::where('id_transaksi', $id)->delete();

        if($delete){
            return response()->json([
                'success' => true,
                'message' => 'Data Transaksi berhasil dihapus!',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data Transaksi gagal dihapus!',
            ]);
        }
    }

    // Get Function for Transaksi (Get all Transaksi data from database)
    public function getAll($limit = NULL, $offset = NULL)
    {
        $data["count"] = Transaksi::count();
        
        if($limit == NULL && $offset == NULL){
            $data["transaksi"] = Transaksi::get();
        } else {
            $data["transaksi"] = Transaksi::take($limit)->skip($offset)->get();
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Get Function for Transaksi (Get Transaksi data by ID from database)
    public function getById($id)
    {   
        $data["transaksi"] = Transaksi::where('id_transaksi', $id)->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Report Function for Transaksi (Get Transaksi data (Report) by Year from database)
    public function report(Request $request) {

        $user = JWTAuth::parseToken()->authenticate();

        $query = DB::table('transaksi')
            ->select('transaksi.id_transaksi', 'transaksi.tgl', 'transaksi.status', 'transaksi.dibayar', 'transaksi.tgl_bayar', 'user.nama as nama_user', 'member.nama as nama_member')
            ->join('user', 'user.id_user', '=', 'transaksi.id_user')
            ->join('outlet', 'outlet.id_outlet', '=', 'user.id_outlet')
            ->join('member', 'member.id_member', '=', 'transaksi.id_member')
            ->where('user.id_outlet', '=', $user['id_outlet']);
            
            if($request->tahun == null) {
                $query->whereYear('transaksi.tgl', '=', date('Y'));
            }
            else {
                $query->WhereYear('transaksi.tgl', '=', $request->tahun);
            }

            if($request->bulan != NULL) {
                $query->WhereMonth('transaksi.tgl', '=', $request->bulan);
            }
            if($request->tgl != NULL) {
                $query->WhereDay('transaksi.tgl', '=', $request->tgl);
            }

            if(count($query->get()) > 0) {
                $data['status'] = true;
                $i = 0;
                foreach($query->get() as 
                $list) {

                    // Get Total Transaksi
                    $get_total_transaksi = DB::table('detailtransaksi')
                        ->select('detailtransaksi.id_detailtransaksi', 'detailtransaksi.id_paket', 'paket.jenis', 'detailtransaksi.weight', DB::raw('paket.harga * detailtransaksi.weight as sub_total'))
                        ->join('paket', 'paket.id_paket', "=", "detailtransaksi.id_paket")
                        ->where('detailtransaksi.id_transaksi', '=', $list->id_transaksi)
                        ->get();

                    $total = 0;
                    foreach($get_total_transaksi as $sub_total) {
                        $total += $sub_total->sub_total;
                    }

                    $data['data'][$i]['id_transaksi'] = $list->id_transaksi;
                    $data['data'][$i]['tgl'] = $list->tgl;
                    $data['data'][$i]['status'] = $list->status;
                    $data['data'][$i]['dibayar'] = $list->dibayar;
                    $data['data'][$i]['tgl_bayar'] = $list->tgl_bayar;
                    $data['data'][$i]['kasir'] = $list->nama_user;
                    $data['data'][$i]['nama_member'] = $list->nama_member;
                    $data['data'][$i]['total'] = $total;
                    $data['data'][$i]['detail_transaksi'] = $get_total_transaksi;

                    $i++;
                }
            } else {
                $data['status'] = false;
                $data['data'] = NULL;
            }

            return response()->json($data);

    }

    
}
