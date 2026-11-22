<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'User';
        $this->param['pageIcon'] = 'feather icon-users';
        $this->param['parentMenu'] = 'user';
        $this->param['current'] = 'User';
    }

    public function index(Request $request)
    {
        $this->param['btnText'] = 'Tambah User';
        $this->param['btnLink'] = route('user.create');
        $this->param['btnTrashText'] = 'Lihat Sampah';
        $this->param['btnTrashLink'] = route('user.trash');

        try {
            $keyword = $request->get('keyword');
            $getUsers = User::orderBy('name', 'ASC');

            if ($keyword) {
                $getUsers->where('name', 'LIKE', "%{$keyword}%")->orWhere('email', 'LIKE', "%{$keyword}%");
            }

            $this->param['user'] = $getUsers->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);
            return back()->withError('Terjadi kesalahan.');
        }
        catch (Exception $e) {
            report($e);
            return back()->withError('Terjadi kesalahan.');
        }

        return view('pages.users.index', $this->param);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->param['btnText'] = 'Lihat User';
        $this->param['btnLink'] = route('user.index');

        return view('pages.users.create',$this->param);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request;
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'level' => 'required',
        ]);
        try {
            $addUser = new User;
            $addUser->name = $request->name;
            $addUser->username = $request->username;
            $addUser->email = $request->email;
            $addUser->level = $request->level;
            $addUser->password = Hash::make($request->password);
            $addUser->save();
            return redirect()->route('user.index')->withStatus('Berhasil menambahkan data');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan.');
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
        try {
            $this->param['btnText'] = 'Lihat User';
            $this->param['btnLink'] = route('user.index');
            $this->param['data'] = User::findOrFail($id);
            return view('pages.users.edit',$this->param);
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
        $user = User::findOrFail($id);
        $isUniqueUsername = $user->username == $request->username ? '' : '|unique:users';
        $isUniqueEmail = $user->email == $request->email ? '' : '|unique:users';
        $request->validate([
            'name' => 'required',
            'username' => 'required'.$isUniqueUsername,
            'email' => 'required|email'.$isUniqueEmail,
            'level' => 'required',
        ]);
        try {
            $addUser = User::find($id);
            $addUser->name = $request->name;
            $addUser->username = $request->username;
            $addUser->email = $request->email;
            $addUser->level = $request->level;
            $addUser->save();
            return redirect()->route('user.index')->withStatus('Berhasil memperbarui data');
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
        try {
            $trashUser = User::findOrFail($id);
            $trashUser->deleted_by = auth()->user()->id;
            $trashUser->save();
            $trashUser->delete();
            return redirect()->route('user.index')->withStatus('Berhasil memindahkan ke sampah');

        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }

    public function changePassword()
    {
        $this->param['user'] = User::find(auth()->user()->id);

        return view('pages.users.change-password', $this->param);
    }

    public function updatePassword(Request $request, $id)
    {
        // $id diabaikan: user hanya boleh mengganti kata sandinya sendiri,
        // bukan kata sandi user lain lewat URL.
        $user = $request->user();

        $request->validate(
            [
                'old_pass' => 'required',
                'password' => 'required',
                'confirmation' => 'required|same:password',
            ],
            [
                'required' => ':attribute harus diisi.',
                'same' => 'Konfirmasi password harus sesuai.',
            ],
            [
                'old_pass' => 'Password lama',
                'password' => 'Password baru',
                'confirmation' => 'Konfirmasi password baru',
            ]
        );

        if (! \Hash::check($request->old_pass, $user->password)) {
            return back()->withError('Password lama tidak cocok.');
        }

        if (\Hash::check($request->password, $user->password)) {
            return back()->withError('Password baru tidak boleh sama dengan password lama.');
        }

        try {
            $user->password = \Hash::make($request->password);
            $user->save();
        } catch (QueryException $e) {
            return back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            return back()->withError('Terjadi kesalahan.');
        }

        return redirect()->route('dashboard')->withStatus('Password berhasil diperbarui.');
    }
}
