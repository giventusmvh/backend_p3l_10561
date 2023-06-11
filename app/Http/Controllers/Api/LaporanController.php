<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\BookingGym;
use App\Models\Instruktur;
use App\Models\JadwalUmum;
use App\Models\BookingKelas;
use App\Models\JadwalHarian;
use App\Models\TransaksiAktivasi;
use App\Models\TransaksiUang;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\IzinInstruktur;
use App\Models\PresensiInstruktur;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LaporanController extends Controller
{
    public function hitungKehadiranDanKetidakhadiran()
    { $bulanIni = date('Y-m');

        $kehadiran = Instruktur::select(
            'instrukturs.id','instrukturs.nama_instruktur', 'instrukturs.akumulasi_terlambat',
            DB::raw('(SELECT COUNT(id) FROM presensi_instrukturs WHERE presensi_instrukturs.id_instruktur = instrukturs.id AND DATE_FORMAT(created_at, "%Y-%m") = "'.$bulanIni.'") as total_kehadiran'),
            DB::raw('(SELECT COUNT(id) FROM izin_instrukturs WHERE izin_instrukturs.id_instruktur = instrukturs.id AND DATE_FORMAT(tgl_izin, "%Y-%m") = "'.$bulanIni.'") as total_ketidakhadiran')
        )
        ->orderBy('instrukturs.akumulasi_terlambat', 'asc')
        ->get();
    
        return response()->json([
            'success' => true,
            'message' => 'list laporan',
            'data' => $kehadiran,
        ], 200);
    }

    
    public function laporanGym(){
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $daysInMonth = CarbonPeriod::create("{$currentYear}-{$currentMonth}-01", "1 day", "{$currentYear}-{$currentMonth}-31");

        $dataByDate = BookingGym::whereMonth('tgl_booking', $currentMonth)
            ->whereYear('tgl_booking', $currentYear)
            ->select('tgl_booking', DB::raw('count(*) as jumlah_data'))
            ->groupBy('tgl_booking')
            ->get()
            ->keyBy('tgl_booking');

        $result = [];
        foreach ($daysInMonth as $date) {
            $formattedDate = $date->format('Y-m-d');
            $jumlahData = $dataByDate[$formattedDate]->jumlah_data ?? 0;
            $result[] = [
                'tgl_booking' => $formattedDate,
                'jumlah_data' => $jumlahData,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'list laporan',
            'data' => $result,
        ], 200);
    }

    public function laporanKelas(){
    $tanggalAwal = now()->startOfMonth();
    $tanggalAkhir = now()->endOfMonth();
    // Mengambil semua data kelas dari tabel kelas
    $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();

    // Membuat array kosong untuk menyimpan data kelas beserta jumlah peserta dan jumlah libur
    $dataKelas = [];

    foreach ($kelas as $kelas) {
        // Menghitung jumlah peserta dengan menghitung id_member pada tabel booking_kelas yang memiliki id_kelas yang sama
        $jumlahPeserta = BookingKelas::where('id_kelas', $kelas->id)
            ->whereBetween('waktu_presensi_kelas', [$tanggalAwal, $tanggalAkhir])
            ->where('cancel', 0)
            ->whereNotNull('waktu_presensi_kelas')
            ->count();

        // Menghitung jumlah libur dengan menghitung jadwal_harian yang memiliki status_jadwalHarian = 1 dan id_kelas yang sama
        $jumlahLibur = JadwalHarian::join('jadwal_umums', 'jadwal_harians.id_jadwalUmum', '=', 'jadwal_umums.id')
            ->where('status_jadwalHarian', 0)
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->where('jadwal_umums.id_kelas', $kelas->id)
            ->count();

        // Mengambil nama instruktur yang mengajar kelas ini
        $namaInstruktur = JadwalHarian::join('jadwal_umums', 'jadwal_harians.id_jadwalUmum', '=', 'jadwal_umums.id')
        ->join('instrukturs', 'jadwal_umums.id_instruktur', '=', 'instrukturs.id')
        ->where('jadwal_umums.id_kelas', $kelas->id)
        ->value('instrukturs.nama_instruktur');

        // Menyimpan data kelas beserta jumlah peserta dan jumlah libur ke dalam array
        $dataKelas[] = [
            'nama_kelas' => $kelas->nama_kelas,
            'nama_instruktur' => $namaInstruktur,
            'jumlah_peserta' => $jumlahPeserta,
            'jumlah_libur' => $jumlahLibur
        ];
    }

    // Mengembalikan data kelas beserta jumlah peserta dan jumlah libur dalam bentuk JSON
    return response()->json([
        'success' => true,
        'message' => 'list laporan',
        'data' => $dataKelas,
    ], 200);
}

public function penghitunganTotal()
{
    // Menghitung total jumlah_bayar_aktivasi per bulan
    $aktivasiPerBulan = DB::table('transaksi_aktivasis')
        ->select(DB::raw('MONTH(tgl_TransaksiAktivasi) as bulan'), DB::raw('SUM(jumlah_bayar_aktivasi) as total_aktivasi'))
        ->groupBy(DB::raw('MONTH(tgl_TransaksiAktivasi)'))
        ->get();

    // Menghitung total jumlah_bayar_uang per bulan
    $uangPerBulan = DB::table('transaksi_uangs')
        ->select(DB::raw('MONTH(tgl_TransaksiUang) as bulan'), DB::raw('SUM(jumlah_bayar_uang) as total_uang'))
        ->groupBy(DB::raw('MONTH(tgl_TransaksiUang)'))
        ->get();

    // Menghitung total jumlah_bayar_uang per bulan
    $kelasPerBulan = DB::table('transaksi_kelass')
        ->select(DB::raw('MONTH(tgl_TransaksiKelas) as bulan'), DB::raw('SUM(total_pembayaran_kelas) as total_kelas'))
        ->groupBy(DB::raw('MONTH(tgl_TransaksiKelas)'))
        ->get();

    // Menyiapkan array dengan semua bulan dalam setahun
    $bulanSetahun = [
        'January', 'February', 'March', 'April', 'May', 'June', 'July',
        'August', 'September', 'October', 'November', 'December'
    ];

    // Menggabungkan hasil per bulan
    $dataPerBulan = [];
    foreach ($bulanSetahun as $index => $bulan) {
        $dataPerBulan[$bulan] = [
            'total_aktivasi' => 0,
            'total_uang' => 0,
            'total_kelas' => 0
        ];

        foreach ($aktivasiPerBulan as $aktivasi) {
            if (($aktivasi->bulan - 1) == $index) {
                $dataPerBulan[$bulan]['total_aktivasi'] = $aktivasi->total_aktivasi;
                break;
            }
        }

        foreach ($uangPerBulan as $uang) {
            if (($uang->bulan - 1) == $index) {
                $dataPerBulan[$bulan]['total_uang'] = $uang->total_uang;
                break;
            }
        }

        foreach ($kelasPerBulan as $kelas) {
            if (($kelas->bulan - 1) == $index) {
                $dataPerBulan[$bulan]['total_kelas'] = $kelas->total_kelas;
                break;
            }
        }
    }

    // Menghitung total_transaksi per bulan
    $totalPerBulan = [];
    foreach ($dataPerBulan as $bulan => $data) {
        $kls = $data['total_kelas'];
        $totalTransaksi = $data['total_aktivasi'] + $data['total_uang'] + $kls;
        $totalPerBulan[] = [
            'bulan' => $bulan,
            'total_kelas' => $kls,
            'total_uang' => $data['total_uang'],
            'total_aktivasi' => $data['total_aktivasi'],
            'total_transaksi' => $totalTransaksi
        ];
    }

    // Return data per bulan
    return response()->json([
        'success' => true,
        'message' => 'list laporan',
        'data' => $totalPerBulan,
    ], 200);
}



public function totalPendapatan()
{
    $laporanPendapatan = $this->penghitunganTotal()->original['data'];

    $Aktivasis = 0;
    $DepositKelass = 0;
    $DepositUangs = 0;

    foreach ($laporanPendapatan as $laporan) {
        $Aktivasis = $Aktivasis  + $laporan['total_aktivasi'];
        $DepositKelass = $DepositKelass + $laporan['total_kelas'];
        $DepositUangs = $DepositUangs + $laporan['total_uang'];
    }

    $totalKeseluruhan = $Aktivasis + $DepositKelass + $DepositUangs;

    $totalSemua[] = [
        'totalAktivasis' => $Aktivasis,
        'totalDepositKelass' => $DepositKelass,
        'totalDepositUangs' => $DepositUangs,
        'totalKeseluruhan' => $totalKeseluruhan,
    ];

    return response()->json([
        'success' => true,
        'message' => 'list laporan',
        'data' => $totalSemua,
    ], 200);
}
}
