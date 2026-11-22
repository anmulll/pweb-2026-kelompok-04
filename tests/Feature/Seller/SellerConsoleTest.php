<?php

namespace Tests\Feature\Seller;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerConsoleTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private User $buyer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->seller()->create();
        $this->buyer = User::factory()->buyer()->create();
    }

    public function test_guest_is_redirected_to_login_for_seller_console(): void
    {
        $this->get(route('seller.dashboard'))->assertRedirect(route('login'));
        $this->get(route('seller.categories.index'))->assertRedirect(route('login'));
        $this->get(route('seller.products.index'))->assertRedirect(route('login'));
        $this->get(route('seller.promotions.index'))->assertRedirect(route('login'));
        $this->get(route('seller.orders.index'))->assertRedirect(route('login'));
        $this->get(route('seller.payments.index'))->assertRedirect(route('login'));
        $this->get(route('seller.reports.index'))->assertRedirect(route('login'));
    }

    public function test_buyer_is_forbidden_from_seller_console(): void
    {
        $this->actingAs($this->buyer);

        $this->get(route('seller.dashboard'))->assertForbidden();
        $this->get(route('seller.categories.index'))->assertForbidden();
        $this->get(route('seller.products.index'))->assertForbidden();
        $this->get(route('seller.promotions.index'))->assertForbidden();
        $this->get(route('seller.orders.index'))->assertForbidden();
        $this->get(route('seller.payments.index'))->assertForbidden();
        $this->get(route('seller.reports.index'))->assertForbidden();
    }

    public function test_seller_can_access_dashboard_with_kpis_and_chart_container(): void
    {
        $this->actingAs($this->seller);

        $response = $this->get(route('seller.dashboard'));
        $response->assertOk();
        $response->assertSee('Ringkasan Penjual');
        $response->assertSee('Omzet Terverifikasi (Paid)');
        $response->assertSee('Jumlah Pesanan');
        $response->assertSee('Jumlah Produk Aktif');
        $response->assertSee('Stok Rendah');
        $response->assertSee('iStore merupakan peritel independen dan tidak berafiliasi secara resmi dengan Apple Inc.', false);
    }

    public function test_seller_can_view_category_management_pages(): void
    {
        $this->actingAs($this->seller);

        $this->get(route('seller.categories.index'))
            ->assertOk()
            ->assertSee('Kategori Produk')
            ->assertSee('Tambah Kategori');

        $this->get(route('seller.categories.create'))
            ->assertOk()
            ->assertSee('Tambah Kategori')
            ->assertSee('Nama Kategori')
            ->assertSee('Slug URL');

        $this->get(route('seller.categories.edit', 1))
            ->assertOk()
            ->assertSee('Ubah Kategori')
            ->assertSee('Simpan Perubahan');
    }

    public function test_seller_can_view_product_management_pages_with_inspection_panel(): void
    {
        $this->actingAs($this->seller);

        $this->get(route('seller.products.index'))
            ->assertOk()
            ->assertSee('Katalog Produk')
            ->assertSee('Tambah Produk');

        $createResponse = $this->get(route('seller.products.create'));
        $createResponse->assertOk();
        $createResponse->assertSee('Tambah Produk');
        $createResponse->assertSee('Panel Inspeksi Unit Second');
        $createResponse->assertSee('Grade Fisik');
        $createResponse->assertSee('Kesehatan Baterai (BH %)');
        $createResponse->assertSee('Status Fungsional');
        $createResponse->assertSee('Kelengkapan Aksesori');
        $createResponse->assertSee('Informasi Garansi');
        $createResponse->assertSee('Catatan Kekurangan Fisik &amp; Fungsi', false);

        $editResponse = $this->get(route('seller.products.edit', 1));
        $editResponse->assertOk();
        $editResponse->assertSee('Ubah Produk');
        $editResponse->assertSee('Simpan Perubahan');
    }

    public function test_seller_can_view_promotions_management_pages(): void
    {
        $this->actingAs($this->seller);

        $this->get(route('seller.promotions.index'))
            ->assertOk()
            ->assertSee('Promosi &amp; Diskon', false)
            ->assertSee('Tambah Promosi');

        $this->get(route('seller.promotions.create'))
            ->assertOk()
            ->assertSee('Tambah Promosi')
            ->assertSee('Banner Promosi')
            ->assertSee('Tanggal Mulai')
            ->assertSee('Tanggal Berakhir');

        $this->get(route('seller.promotions.edit', 1))
            ->assertOk()
            ->assertSee('Ubah Promosi')
            ->assertSee('Simpan Perubahan');
    }

    public function test_seller_can_view_orders_index_and_detail_with_fulfillment_actions(): void
    {
        $this->actingAs($this->seller);

        $this->get(route('seller.orders.index'))
            ->assertOk()
            ->assertSee('Daftar Pesanan')
            ->assertSee('Filter Status Pesanan');

        $detailResponse = $this->get(route('seller.orders.show', 1));
        $detailResponse->assertOk();
        $detailResponse->assertSee('Detail Pesanan ORD-1');
        $detailResponse->assertSee('Item Pesanan (Snapshot Permanen)');
        $detailResponse->assertSee('Tindakan Pemenuhan Pesanan');
        $detailResponse->assertSee('Tandai Diproses');
        $detailResponse->assertSee('Nama Kurir Pengiriman');
        $detailResponse->assertSee('Nomor Resi Pengiriman');
        $detailResponse->assertSee('Batalkan Pesanan');
        $detailResponse->assertSee('Rincian Pembayaran (DOKU Sandbox)');
    }

    public function test_seller_can_poll_payment_status_endpoint(): void
    {
        $this->actingAs($this->seller);

        $response = $this->getJson(route('seller.orders.payment.status', 1));
        $response->assertOk();
        $response->assertJsonStructure([
            'order_id',
            'order_status',
            'payment_status',
            'updated_at',
        ]);
    }

    public function test_seller_can_view_payments_monitoring_page(): void
    {
        $this->actingAs($this->seller);

        $this->get(route('seller.payments.index'))
            ->assertOk()
            ->assertSee('Pemantauan Pembayaran');
    }

    public function test_seller_can_view_sales_report_and_pdf_template(): void
    {
        $this->actingAs($this->seller);

        $reportResponse = $this->get(route('seller.reports.index'));
        $reportResponse->assertOk();
        $reportResponse->assertSee('Laporan Penjualan');
        $reportResponse->assertSee('Omzet Terverifikasi (Paid)');
        $reportResponse->assertSee('Unduh Laporan PDF');

        $pdfResponse = $this->get(route('seller.reports.pdf'));
        $pdfResponse->assertOk();
        $pdfResponse->assertSee('Laporan Penjualan iStore');
        $pdfResponse->assertSee('Omzet Terverifikasi (Paid)');
        $pdfResponse->assertSee('iStore merupakan peritel independen dan tidak berafiliasi secara resmi dengan Apple Inc.', false);
    }
}
