<?php

namespace App\Http\Controllers;
use App\Models\Alinacakurunler;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class AlinacakUrunlerController extends Controller
{

    public function ekle(){
        return view('alinacakurunler.ekle');
    }
    public function ekleme(Request $request)
    {
        //controllerda form bilgilerinin uygun şartları sağladığından emin olduğumuz validation kodları
        $request->validate([
            'urun_adi' => 'required|string|max:150|unique :alinacakurunler',
            'urun_kodu' => 'required|numeric|unique :alinacakurunler',
            'urun_tipi' => 'required',
            'maks_stok' => 'required|numeric',
            'depo_miktar' => 'required|numeric',
            'min_stok' => 'required|numeric',
            'birim_fiyat' => 'required|numeric',

        ],
[
    'urun_adi.required' => 'Lütfen Ürün adı giriniz.',
    'urun_adi.string' => 'Lütfen sadece harflerden oluşan bir isim giriniz.',
    'urun_adi.max' => 'İsim için maksimum 150 harf kullanabilirsiniz.',
    'urun_adi.unique' => 'Ürün ismi benzersiz olmak zorundadır',

    'urun_kodu.required' => 'Ürün kodu girmek zorundasınız.',
    'urun_kodu.numeric' => 'Ürün kodu yalnızca sayılardan oluşmalıdır.',
    'urun_kodu.unique' => 'Bu ürün koduna sahip bir ürün zaten var.',

    'urun_tipi.required' => 'Ürün tipi seçilmek zorundadır.',

    'maks_stok.required' => 'Maksimum stok değerini girmek zorundasınız.',
    'maks_stok.numeric' => 'Maksimum stok değeri yalnızca sayılardan oluşabilir.',

    'depo_miktar.required' => 'Depo miktarını girmek zorundasınız.',
    'depo_miktar.numeric' => 'Depo miktar değeri yalnızca sayılardan oluşabilir',

    'min_stok.required' => 'Minimum stok değerini girmek zorundasınız.',
    'min_stok.numeric' => 'Minimum stok değeri yalnızca sayılardan oluşabilir.',

    'birim_fiyat.required' => 'Birim fiyatı girmek zorundasınız.',
    'birim_fiyat.numeric' => 'Birim fiyat değeri yalnızca sayılardan oluşabilir.',
]);
        $getir = Alinacakurunler::where('urun_kodu', $request->urun_kodu)->first();

        if ($getir) {
            return redirect()->back()->with([
                'mesaj' => 'Bu Ürün Zaten Listede Bulunuyor.',
                'durum' => '0',
            ]);
        } else {
            $yeni_urun = new Alinacakurunler();
            $yeni_urun->urun_adi = $request->urun_adi;
            $yeni_urun->urun_kodu = $request->urun_kodu;
            $yeni_urun->urun_tipi = $request->urun_tipi;
            $yeni_urun->maks_stok = $request->maks_stok;
            $yeni_urun->depo_miktar = $request->depo_miktar;
            $yeni_urun->min_stok = $request->min_stok;
            $yeni_urun->birim_fiyat = $request->birim_fiyat;
            $yeni_urun->alinacak_miktar = $request->maks_stok - $request->depo_miktar;


            $sonuc = $yeni_urun->save();

            if ($sonuc) {
                return redirect('/alinacakurunler/liste')->with([
                    'digerIslem' => true, // Bu değişkeni kullanarak Swal modalını tetikleyeceğiz
                    'mesaj' => 'Kayıt eklendi.',
                    'durum' => '1',
                ]);
            } else {
                return redirect()->back()->with([
                    'mesaj' => 'Kayıt eklenemedi.',
                    'durum' => '0',
                ]);
            }
        }
    }

    public function dataliste()
    {
        $data['alinacakurunler'] = Alinacakurunler::orderBy('id', 'desc')->get();
        $data['title'] = 'DataTable Eksik Ürünler Listesi';
        return view('alinacakurunler.liste', $data);
    }

    public function listeyigetir(Request $request)
    {
        $tablo = Alinacakurunler::query();
        $data = DataTables::of($tablo)
            ->addColumn('butonlar', function ($tablo) {
                return
                    '<a onclick=" satinal(\'' . url('alinacakurunler/duzenle', ['id' => $tablo->id]) . '\')"class="btn btn-sm btn-success">Satın Al</a>';
            })
            ->rawColumns(['butonlar'])
            ->make(true);
        return $data;
    }

    public function duzenle($id = '0')
    {
        if ($id == '0') {
            return redirect()->back()->with([
                'mesaj' => 'Buraya Giriş Yetkiniz Yok.',
                'durum' => '0',
            ]);
        } else {
            $urun = Alinacakurunler::find($id);

            if ($urun) {
                $data['urun'] = $urun;
                return view('alinacakurunler.guncelle', $data);
            } else {
                return redirect()->back()->with([
                    'mesaj' => 'Kayıt bulunamadı.',
                    'durum' => '0',
                ]);
            }
        }
    }





}
