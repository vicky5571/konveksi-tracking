# Konveksi Tracking

Sistem Tracking Produksi Konveksi berbasis Web untuk memantau progres pesanan (PO) dari proses awal hingga selesai secara real-time.

## 📋 Deskripsi

Konveksi Tracking adalah aplikasi yang digunakan untuk memonitor alur produksi konveksi mulai dari penerimaan PO, proses penjahitan, QC (Quality Control), hingga pesanan selesai. Sistem ini membantu admin, owner, dan tim produksi dalam mengetahui status setiap pesanan secara cepat dan akurat.

## ✨ Fitur Utama

* Dashboard Statistik Produksi
* Manajemen Data PO (Purchase Order)
* Tracking Status Produksi
* Monitoring Proses Jahit
* Quality Control (QC)
* Status Pending (Perbaikan / Permak)
* Riwayat Perubahan Status
* Pencarian Data PO
* Filter Berdasarkan Status
* Tampilan Responsive

## 🔄 Alur Tracking

```text
PO Masuk
    ↓
Penjahitan
    ↓
QC / Trimming
    ↓
Pending (Jika ada perbaikan)
    ↓
Selesai
```

## 📊 Status Produksi

| Status     | Keterangan                  |
| ---------- | --------------------------- |
| PO Masuk   | Pesanan baru diterima       |
| Penjahitan | Sedang dalam proses jahit   |
| QC         | Pemeriksaan kualitas produk |
| Pending    | Menunggu perbaikan / revisi |
| Selesai    | Produk siap dikirim         |

## 🛠️ Teknologi yang Digunakan

### Frontend

* HTML5
* CSS3
* JavaScript

### Backend (Opsional Pengembangan)

* Node.js
* Express.js

### Database

* MySQL

## 📂 Struktur Project

```bash
konveksi-tracking/
│
├── index.html
├── dashboard.html
├── tracking.html
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
├── database/
│   └── konveksi_tracking.sql
│
└── README.md
```

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/Topan03/konveksi-tracking.git
```

### 2. Masuk ke Folder Project

```bash
cd konveksi-tracking
```

### 3. Jalankan Project

Jika masih menggunakan HTML murni:

```bash
buka index.html
```

Atau menggunakan VS Code:

```bash
Right Click → Open with Live Server
```

## 📸 Tampilan Sistem

### Dashboard

* Total PO
* Proses Jahit
* Pending
* Selesai

### Tracking Produksi

* Nomor PO
* Nama Customer
* Tanggal Masuk
* Status Saat Ini
* Progress Produksi

## 🎯 Tujuan Sistem

* Mempermudah monitoring produksi konveksi.
* Mengurangi kesalahan komunikasi antar bagian.
* Mempercepat pencarian status pesanan.
* Meningkatkan efisiensi proses produksi.

## 🔮 Pengembangan Selanjutnya

* Login Multi User
* Hak Akses Admin & Owner
* Notifikasi WhatsApp
* Export PDF
* Grafik Produksi
* Scan QR Code Tracking
* Upload Foto Progress Produksi
* Tracking Real-Time

## 👨‍💻 Developer

**Fitra Mustofa**

* GitHub: https://github.com/Topan03

## 📄 License

Project ini dibuat untuk kebutuhan pembelajaran, tugas akhir, dan pengembangan sistem manajemen produksi konveksi.

---

⭐ Jika project ini bermanfaat, jangan lupa berikan Star pada repository ini.
