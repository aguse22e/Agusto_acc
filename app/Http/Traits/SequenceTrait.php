<?php

namespace App\Http\Traits;

use App\Models\Sequence;

trait SequenceTrait
{

    public function generateNomorTransaksi($kode, $tahun, $bulan, $kodeAkun = null)
    {
        if ($kodeAkun == null) {
            $sequence = Sequence::where('kode', $kode)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->first();
            // return 'a';
        } else {
            $sequence = Sequence::where('kode', $kode)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->where('kode_akun', $kodeAkun)
                ->first();
            // return 'b';
        }
        
        if ($sequence) {
            $new_seq_no = ($sequence->seq_no + 1);

            //Update m_sequences
            $object = Sequence::findOrFail($sequence->id);
            $object->update(['seq_no' => $new_seq_no]);

            $gen_seq_no = '';
            $max_lenght_seq_no = $sequence->seq_length - strlen((string)$new_seq_no);
            for ($i = 0; $i < $max_lenght_seq_no; $i++) {
                $gen_seq_no .= '0';
            }
            $gen_seq_no .= (string)$new_seq_no;
            $tahun = substr($tahun, 2, 2);

            $bulan = strlen((string)$bulan) < 2 ? ('0' . (string)$bulan) : $bulan;
            $transaction_number = $kodeAkun != null ? $kode . '-' . $tahun . '-' . $bulan . '-' . $kodeAkun . '-' . $gen_seq_no : $kode . '-' . $tahun . '-' . $bulan . '-' . $gen_seq_no ;
        } else {
            $new_seq_no = 1;

            //Insert m_sequences
            $newSequence = [
                'kode' => $kode,
                'tahun' => $tahun,
                'bulan' => $bulan,
                'seq_length' => 3,
                'seq_no' => $new_seq_no,
                'kode_akun' => $kodeAkun == null ? null : $kodeAkun
            ];
            $object = Sequence::create($newSequence);

            $gen_seq_no = '';
            $max_lenght_seq_no = $newSequence['seq_length'] - strlen((string)$new_seq_no);
            for ($i = 0; $i < $max_lenght_seq_no; $i++) {
                $gen_seq_no .= '0';
            }
            $gen_seq_no .= (string)$new_seq_no;
            $tahun = substr($tahun, 2, 2);

            $bulan = strlen((string)$bulan) < 2 ? ('0' . (string)$bulan) : $bulan;
            $transaction_number = $kodeAkun != null ? $kode . '-' . $tahun . '-' . $bulan . '-' . $kodeAkun . '-' . $gen_seq_no : $kode . '-' . $tahun . '-' . $bulan . '-' . $gen_seq_no ;
        }

        return $transaction_number;
        
    }
}
