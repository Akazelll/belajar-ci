<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use Dompdf\Dompdf;

class ProductController extends BaseController
{
    protected $productModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        return view('produk/index', [
            'products' => $this->productModel->findAll()
        ]);
    }

    public function create()
    {
        $dataFoto = $this->request->getFile('foto');

        $dataForm = [
            'nama'   => $this->request->getPost('nama'),
            'harga'  => $this->request->getPost('harga'),
            'jumlah' => $this->request->getPost('jumlah')
        ];

        if ($dataFoto && $dataFoto->isValid() && !$dataFoto->hasMoved()) {
            $fileName = $dataFoto->getRandomName();
            $dataFoto->move(FCPATH . 'img', $fileName);

            $dataForm['foto'] = $fileName;
        }

        $this->productModel->insert($dataForm);

        return redirect()->to(base_url('produk'))->with('success', 'Data Berhasil Ditambah');
    }

    public function edit($id)
    {
        $produkLama = $this->productModel->find($id);

        if (!$produkLama) {
            return redirect()->to(base_url('produk'))->with('failed', 'Data tidak ditemukan');
        }

        $dataFoto = $this->request->getFile('foto');

        $dataForm = [
            'nama'   => $this->request->getPost('nama'),
            'harga'  => $this->request->getPost('harga'),
            'jumlah' => $this->request->getPost('jumlah')
        ];

        if ($dataFoto && $dataFoto->isValid() && !$dataFoto->hasMoved()) {
            $fileName = $dataFoto->getRandomName();
            $dataFoto->move(FCPATH . 'img', $fileName);

            if (!empty($produkLama['foto']) && file_exists(FCPATH . 'img/' . $produkLama['foto'])) {
                unlink(FCPATH . 'img/' . $produkLama['foto']);
            }

            $dataForm['foto'] = $fileName;
        }

        $this->productModel->update($id, $dataForm);

        return redirect()->to(base_url('produk'))->with('success', 'Data Berhasil Diubah');
    }

    public function delete($id)
    {
        $produk = $this->productModel->find($id);

        if (!$produk) {
            return redirect()->to(base_url('produk'))->with('failed', 'Data tidak ditemukan');
        }

        if (!empty($produk['foto']) && file_exists(FCPATH . 'img/' . $produk['foto'])) {
            unlink(FCPATH . 'img/' . $produk['foto']);
        }

        $this->productModel->delete($id);

        return redirect()->to(base_url('produk'))->with('success', 'Data Berhasil Dihapus');
    }
    public function download()
    {
        // Ambil data produk dari database
        $products = $this->productModel->findAll();

        // Render view menjadi HTML
        $html = view('produk/download_pdf', [
            'products' => $products
        ]);

        // Nama file PDF
        $filename = date('Y-m-d-H-i-s') . '-produk.pdf';

        // Inisialisasi Dompdf
        $dompdf = new Dompdf();

        // Load HTML ke Dompdf
        $dompdf->loadHtml($html);

        // Setting ukuran kertas dan orientasi
        $dompdf->setPaper('A4', 'portrait');

        // Generate PDF
        $dompdf->render();

        // Download / tampilkan PDF
        $dompdf->stream($filename, [
            'Attachment' => true
        ]);
    }
}
