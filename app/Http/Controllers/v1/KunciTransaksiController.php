<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\KunciTransaksi;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class KunciTransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Kunci Transaksi';
        $this->param['pageIcon'] = 'feather icon-bookmark';
        $this->param['parentMenu'] = 'Kunci Transaksi';
        $this->param['current'] = 'Kunci Transaksi';
    }
    public function index(Request $request)
    {
        try {
            $keyword = $request->get('keyword');
            $getKunciTransaksi = KunciTransaksi::orderBy('id', 'ASC');

            if ($keyword) {
                $getKunciTransaksi->where('jenis_transaksi', 'LIKE', "%{$keyword}%");
            }

            $this->param['kunci_transaksi'] = $getKunciTransaksi->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('pages.kunci-transaksi.index', $this->param);
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
    public function store(Request $request)
    {
        //
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
        try {
            $this->param['btnText'] = 'Lihat Kunci Transaksi';
            $this->param['btnLink'] = route('kunci-transaksi.index');
            $this->param['data'] = KunciTransaksi::find($id);
            return view('pages.kunci-transaksi.edit',$this->param);
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan.');
        }

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
        try {
            $editKunciTransaksi = KunciTransaksi::findOrFail($id);
            $editKunciTransaksi->tanggal_mulai_kunci = $request->tanggal_kunci;
            $editKunciTransaksi->save();
            return redirect()->route('kunci-transaksi.index')->withStatus('Berhasil memperbarui data.');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan.');
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
        //
    }
}
