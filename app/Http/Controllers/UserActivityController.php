<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use Exception;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| User Activity (Jejak Audit)
|--------------------------------------------------------------------------
| Modul ini bersifat READ-ONLY secara sengaja: catatan aktivitas adalah
| jejak audit, jadi tidak boleh dibuat, diubah, atau dihapus manual dari
| antarmuka — nilainya sebagai bukti hilang bila bisa diedit.
| Catatan hanya ditulis otomatis oleh controller transaksi (Kas, Bank,
| Memorial, User, dll) dan hanya bisa DILIHAT di sini (index & show).
| Karena itu route-nya dibatasi ->only(['index', 'show']) di routes/web.php.
*/
class UserActivityController extends Controller
{
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'User Activity';
        $this->param['pageIcon'] = 'fa fa-history';
        $this->param['parentMenu'] = 'User Activity';
        $this->param['current'] = 'User Activity';
    }

    public function index(Request $request)
    {
        try {
            $start = $request->start;
            $end = $request->end;
            $idUser = $request->id_user;

            if ($start > $end) {
                return back()->withError('Tanggal tidak valid.');
            }

            // PERBAIKAN: select user_activity.* agar $item->id adalah id LOG
            // (sebelumnya users.id ikut terselect dan menimpa id log).
            $getUserActivity = UserActivity::select('user_activity.*', 'users.name')
                                            ->join('users','users.id','user_activity.id_user')
                                            ->orderBy('user_activity.id', 'DESC');
            if ($idUser) {
                $getUserActivity->where('user_activity.id_user', $idUser);
            }
            if ($start && $end) {
                $getUserActivity->whereBetween('user_activity.created_at', ["$start 00:00:00", "$end 23:59:59"]);
            }

            $this->param['users'] = User::get();

            $this->param['logActivity'] = $getUserActivity->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('pages.user-activity.index', $this->param);
    }

    /**
     * Detail satu catatan aktivitas (read-only).
     */
    public function show($id)
    {
        $this->param['btnText'] = 'Lihat User Activity';
        $this->param['btnLink'] = route('user-activity.index');
        $this->param['current'] = 'Detail User Activity';

        try {
            $this->param['activity'] = UserActivity::select('user_activity.*', 'users.name', 'users.email', 'users.level')
                                            ->join('users','users.id','user_activity.id_user')
                                            ->where('user_activity.id', $id)
                                            ->firstOrFail();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
        catch (Exception $e) {
            return back()->withError('Catatan aktivitas tidak ditemukan.');
        }

        return view('pages.user-activity.show', $this->param);
    }
}
