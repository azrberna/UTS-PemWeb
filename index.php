<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuminaRead — Perpustakaan Digital & Sewa E-Book</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="navbar">
        <div class="container nav-content">
            <a href="#" class="brand">
                <span class="brand-icon" aria-hidden="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 5.5C2 4.67 2.67 4 3.5 4H8.5C10.43 4 12 5.57 12 7.5V19.5C12 18.12 10.88 17 9.5 17H3.5C2.67 17 2 16.33 2 15.5V5.5Z" fill="rgba(255,255,255,0.22)" stroke="#FFFFFF" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M22 5.5C22 4.67 21.33 4 20.5 4H15.5C13.57 4 12 5.57 12 7.5V19.5C12 18.12 13.12 17 14.5 17H20.5C21.33 17 22 16.33 22 15.5V5.5Z" fill="rgba(255,255,255,0.12)" stroke="#FFFFFF" stroke-width="2" stroke-linejoin="round"/>
                        <line x1="5.2" y1="8.5" x2="8.8" y2="8.5" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round"/>
                        <line x1="5.2" y1="12" x2="8.8" y2="12" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round"/>
                        <line x1="15.2" y1="8.5" x2="18.8" y2="8.5" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round"/>
                        <line x1="15.2" y1="12" x2="18.8" y2="12" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
                <div>
                    <h1>Lumina<span>Read</span></h1>
                    <p>Perpustakaan Digital & Sewa E-Book</p>
                </div>
            </a>

            <nav aria-label="Navigasi Utama">
                <ul class="nav-links">
                    <li><a href="#katalog">Eksplorasi Katalog</a></li>
                    <li><a href="#riwayat">Riwayat Sewa</a></li>
                    <li><a href="#panel-aksi" class="nav-cta">Sewa Sekarang</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container page-wrapper">
        <section class="stats-bar" aria-label="Ringkasan Perpustakaan">
            <div class="stat-item">
                <span class="stat-label">Total Koleksi</span>
                <strong class="stat-value" id="statBooks">0 Judul</strong>
            </div>
            <div class="stat-item">
                <span class="stat-label">Lisensi Tersedia</span>
                <strong class="stat-value" id="statStock">0 Lisensi</strong>
            </div>
            <div class="stat-item">
                <span class="stat-label">Member Terdaftar</span>
                <strong class="stat-value" id="statUsers">0 Akun</strong>
            </div>
            <div class="stat-item">
                <span class="stat-label">Sedang Dipinjam</span>
                <strong class="stat-value" id="statActive">0 Aktif</strong>
            </div>
        </section>

        <div class="main-grid">
            <div class="primary-column">
                <section id="katalog" class="card">
                    <div class="section-header">
                        <div>
                            <h2>Katalog E-Book & AudioBook</h2>
                            <p class="section-sub">Pilih judul koleksi untuk langsung disewa secara digital</p>
                        </div>
                        <div class="filter-controls">
                            <input type="search" id="searchInput" placeholder="Cari judul buku atau penulis..." aria-label="Cari katalog">
                            <select id="typeFilter" aria-label="Filter format media">
                                <option value="all">Semua Format</option>
                                <option value="ebook">E-Book</option>
                                <option value="audiobook">AudioBook</option>
                            </select>
                        </div>
                    </div>
                    <div id="catalogGrid" class="catalog-grid" aria-live="polite"></div>
                </section>

                <section id="riwayat" class="card">
                    <div class="section-header">
                        <div>
                            <h2>Daftar Peminjaman & Pengembalian</h2>
                            <p class="section-sub">Pantau masa aktif akses digital member secara real-time</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>No. Invoice</th>
                                    <th>Nama Member</th>
                                    <th>Koleksi</th>
                                    <th>Durasi</th>
                                    <th>Total Biaya</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="transactionBody"></tbody>
                        </table>
                    </div>
                </section>
            </div>

            <aside id="panel-aksi" class="sidebar-column">
                <div id="toastAlert" class="toast hidden" role="alert"></div>

                <section class="card action-panel">
                    <div class="tab-bar" role="tablist">
                        <button type="button" class="tab-btn active" data-tab="tabRent" role="tab" aria-selected="true">Sewa Koleksi</button>
                        <button type="button" class="tab-btn" data-tab="tabUser" role="tab" aria-selected="false">+ Member</button>
                        <button type="button" class="tab-btn" data-tab="tabMedia" role="tab" aria-selected="false">+ Koleksi</button>
                    </div>

                    <div id="tabRent" class="tab-pane active">
                        <form id="rentForm" novalidate>
                            <div class="form-group">
                                <label for="rentUser">Akun Member</label>
                                <select id="rentUser" required></select>
                                <small class="error-msg" id="errRentUser"></small>
                            </div>

                            <div class="form-group">
                                <label for="rentMedia">Judul E-Book / AudioBook</label>
                                <select id="rentMedia" required></select>
                                <small class="error-msg" id="errRentMedia"></small>
                            </div>

                            <div class="form-group">
                                <label for="rentDays">Durasi Akses (1–30 Hari)</label>
                                <input type="number" id="rentDays" min="1" max="30" value="7" required>
                                <small class="error-msg" id="errRentDays"></small>
                            </div>

                            <div class="price-box" id="pricePreview">
                                <div class="price-row">
                                    <span>Rincian Tarif</span>
                                    <span id="priceDetail">-</span>
                                </div>
                                <div class="price-total">
                                    <span>Total Pembayaran</span>
                                    <strong id="priceTotal">Rp 0</strong>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Konfirmasi Sewa</button>
                        </form>
                    </div>

                    <div id="tabUser" class="tab-pane">
                        <form id="userForm" novalidate>
                            <div class="form-group">
                                <label for="userName">Nama Lengkap</label>
                                <input type="text" id="userName" placeholder="Contoh: Dimas Saputra" required>
                                <small class="error-msg" id="errUserName"></small>
                            </div>
                            <div class="form-group">
                                <label for="userEmail">Alamat Email</label>
                                <input type="email" id="userEmail" placeholder="dimas@email.com" required>
                                <small class="error-msg" id="errUserEmail"></small>
                            </div>
                            <div class="form-group">
                                <label for="userTier">Paket Keanggotaan</label>
                                <select id="userTier">
                                    <option value="Regular">Regular — Maks. 2 pinjam aktif</option>
                                    <option value="Premium">Premium — Maks. 5 pinjam + Diskon 20%</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Daftarkan Member</button>
                        </form>
                    </div>

                    <div id="tabMedia" class="tab-pane">
                        <form id="mediaForm" novalidate>
                            <div class="form-group">
                                <label for="mediaType">Format Digital</label>
                                <select id="mediaType">
                                    <option value="ebook">E-Book (PDF / EPUB)</option>
                                    <option value="audiobook">AudioBook (+Rp 2.000 lisensi audio)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="mediaTitle">Judul Buku</label>
                                <input type="text" id="mediaTitle" placeholder="Contoh: Head First Design Patterns" required>
                                <small class="error-msg" id="errMediaTitle"></small>
                            </div>
                            <div class="form-group">
                                <label for="mediaAuthor">Nama Penulis</label>
                                <input type="text" id="mediaAuthor" placeholder="Contoh: Eric Freeman" required>
                                <small class="error-msg" id="errMediaAuthor"></small>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="mediaCategory">Kategori</label>
                                    <input type="text" id="mediaCategory" value="Pemrograman" required>
                                </div>
                                <div class="form-group">
                                    <label for="mediaExtra" id="extraLabel">Jml Halaman</label>
                                    <input type="number" id="mediaExtra" value="300" min="1" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="mediaRate">Tarif/Hari (Rp)</label>
                                    <input type="number" id="mediaRate" value="6000" min="1000" step="500" required>
                                </div>
                                <div class="form-group">
                                    <label for="mediaStock">Stok Lisensi</label>
                                    <input type="number" id="mediaStock" value="4" min="1" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Tambahkan ke Katalog</button>
                        </form>
                    </div>
                </section>
            </aside>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 LuminaRead Digital Library. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script src="assets/js/app.js"></script>
</body>
</html>