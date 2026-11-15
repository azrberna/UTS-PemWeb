document.addEventListener('DOMContentLoaded', () => {
    let appState = { media: [], users: [], transactions: [] };

    const formatRupiah = (num) => 'Rp ' + Number(num).toLocaleString('id-ID');

    const showToast = (message, isError = false) => {
        const toast = document.getElementById('toastAlert');
        if (!toast) return;
        toast.textContent = message;
        toast.className = `toast ${isError ? 'error' : 'success'}`;
        setTimeout(() => toast.classList.add('hidden'), 4500);
    };

    const switchTab = (tabId) => {
        document.querySelectorAll('.tab-btn').forEach((btn) => {
            const isTarget = btn.dataset.tab === tabId;
            btn.classList.toggle('active', isTarget);
            btn.setAttribute('aria-selected', String(isTarget));
        });
        document.querySelectorAll('.tab-pane').forEach((pane) => {
            pane.classList.toggle('active', pane.id === tabId);
        });
    };

    document.querySelectorAll('.tab-btn').forEach((btn) => {
        btn.addEventListener('click', () => switchTab(btn.dataset.tab));
    });

    const fetchState = async () => {
        const res = await fetch('api.php');
        const json = await res.json();
        if (json.success) {
            appState = json.data;
            renderAll();
        }
    };

    const postAction = async (payload) => {
        const res = await fetch('api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const json = await res.json();
        if (!json.success) {
            throw new Error(json.message || 'Terjadi kendala saat memproses permintaan.');
        }
        appState = json.data;
        renderAll();
        showToast(json.message, false);
    };

    const renderStats = () => {
        const totalBooks = appState.media.length;
        const totalStock = appState.media.reduce((acc, item) => acc + item.stock, 0);
        const totalUsers = appState.users.length;
        const activeRentals = appState.transactions.filter((tx) => tx.status === 'active').length;

        const elBooks = document.getElementById('statBooks');
        const elStock = document.getElementById('statStock');
        const elUsers = document.getElementById('statUsers');
        const elActive = document.getElementById('statActive');

        if (elBooks) elBooks.textContent = `${totalBooks} Judul`;
        if (elStock) elStock.textContent = `${totalStock} Lisensi`;
        if (elUsers) elUsers.textContent = `${totalUsers} Akun`;
        if (elActive) elActive.textContent = `${activeRentals} Aktif`;
    };

    const renderCatalog = () => {
        const grid = document.getElementById('catalogGrid');
        const keyword = document.getElementById('searchInput').value.toLowerCase();
        const typeFilter = document.getElementById('typeFilter').value;

        const filtered = appState.media.filter((item) => {
            const matchText =
                item.title.toLowerCase().includes(keyword) ||
                item.author.toLowerCase().includes(keyword) ||
                item.category.toLowerCase().includes(keyword);
            const matchType = typeFilter === 'all' || item.type === typeFilter;
            return matchText && matchType;
        });

        if (filtered.length === 0) {
            grid.innerHTML = '<p style="color:#6B7280;">Tidak ada koleksi yang cocok dengan pencarian Anda.</p>';
            return;
        }

        grid.innerHTML = filtered.map((m, idx) => `
            <article class="media-card">
                <div class="book-cover cover-theme-${idx % 6}">
                    <div class="cover-top">
                        <span class="cover-badge">${m.mediaType}</span>
                        <span class="cover-stock">Stok: ${m.stock}</span>
                    </div>
                    <div class="cover-title">${m.title}</div>
                </div>
                <div class="media-body">
                    <div>
                        <div class="media-category ${m.type === 'audiobook' ? 'is-audio' : ''}">
                            ${m.category}
                        </div>
                        <h3>${m.title}</h3>
                        <p class="media-meta">${m.author} • ${m.extraInfo}</p>
                    </div>
                    <div class="media-footer">
                        <div class="price-tag">
                            <strong>${formatRupiah(m.dailyRate)}</strong>
                            <span>per hari</span>
                        </div>
                        <div style="display:flex; gap:0.35rem;">
                            <button
                                type="button"
                                class="btn btn-sm"
                                data-restock-media="${m.id}"
                                data-media-title="${m.title.replace(/"/g, '&quot;')}"
                                title="Tambah stok lisensi"
                            >
                                + Stok
                            </button>
                            <button
                                type="button"
                                class="btn-rent-quick"
                                data-select-media="${m.id}"
                                ${m.stock === 0 ? 'disabled' : ''}
                            >
                                ${m.stock === 0 ? 'Habis' : 'Sewa'}
                            </button>
                        </div>
                    </div>
                </div>
            </article>
        `).join('');
    };

    const renderSelectOptions = () => {
        const userSelect = document.getElementById('rentUser');
        const mediaSelect = document.getElementById('rentMedia');
        const prevUser = userSelect.value;
        const prevMedia = mediaSelect.value;

        userSelect.innerHTML = appState.users.map((u) => `
            <option value="${u.id}">${u.name} (${u.tier})</option>
        `).join('');

        mediaSelect.innerHTML = appState.media.map((m) => `
            <option value="${m.id}" ${m.stock === 0 ? 'disabled' : ''}>
                ${m.title} (${m.mediaType}) — Stok ${m.stock}
            </option>
        `).join('');

        if (prevUser && appState.users.some((u) => u.id === prevUser)) {
            userSelect.value = prevUser;
        }
        if (prevMedia && appState.media.some((m) => m.id === prevMedia && m.stock > 0)) {
            mediaSelect.value = prevMedia;
        }

        updatePricePreview();
    };

    const updatePricePreview = () => {
        const userId = document.getElementById('rentUser').value;
        const mediaId = document.getElementById('rentMedia').value;
        const days = parseInt(document.getElementById('rentDays').value, 10) || 1;
        const detailEl = document.getElementById('priceDetail');
        const totalEl = document.getElementById('priceTotal');

        const user = appState.users.find((u) => u.id === userId);
        const media = appState.media.find((m) => m.id === mediaId);

        if (!user || !media) {
            detailEl.textContent = '-';
            totalEl.textContent = 'Rp 0';
            return;
        }

        let base = media.dailyRate * days;
        let notes = [`${days} hari`];
        if (media.type === 'audiobook') {
            base += 2000;
            notes.push('+Rp 2.000 audio');
        }
        const finalPrice = user.tier === 'Premium' ? base * 0.8 : base;
        if (user.tier === 'Premium') {
            notes.push('Diskon 20%');
        }

        detailEl.textContent = notes.join(' • ');
        totalEl.textContent = formatRupiah(finalPrice);
    };

    const renderTransactions = () => {
        const tbody = document.getElementById('transactionBody');
        if (appState.transactions.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#6B7280;">Belum ada riwayat peminjaman aktif.</td></tr>';
            return;
        }

        tbody.innerHTML = appState.transactions.map((tx) => `
            <tr>
                <td><code>${tx.id}</code></td>
                <td>${tx.userName}</td>
                <td>${tx.mediaTitle}</td>
                <td>${tx.days} hari</td>
                <td>${formatRupiah(tx.totalPrice)}</td>
                <td>
                    <span class="badge ${tx.status === 'active' ? 'badge-active' : 'badge-returned'}">
                        ${tx.status === 'active' ? 'Sedang Dipinjam' : 'Selesai'}
                    </span>
                </td>
                <td>
                    ${tx.status === 'active'
                        ? `<button class="btn btn-sm" data-return="${tx.id}">Kembalikan</button>`
                        : '-'}
                </td>
            </tr>
        `).join('');
    };

    const renderAll = () => {
        renderStats();
        renderCatalog();
        renderSelectOptions();
        renderTransactions();
    };

    document.getElementById('catalogGrid').addEventListener('click', async (e) => {
        const restockId = e.target.getAttribute('data-restock-media');
        if (restockId) {
            const title = e.target.getAttribute('data-media-title') || 'koleksi ini';
            const input = prompt(`Tambah berapa lisensi stok untuk "${title}"?`, '3');
            if (input === null) return;

            const amount = parseInt(input, 10);
            if (isNaN(amount) || amount < 1) {
                showToast('Masukkan angka jumlah stok yang valid (minimal 1).', true);
                return;
            }

            try {
                await postAction({ action: 'restock', mediaId: restockId, amount });
            } catch (err) {
                showToast(err.message, true);
            }
            return;
        }

        const mediaId = e.target.getAttribute('data-select-media');
        if (!mediaId) return;

        switchTab('tabRent');
        const mediaSelect = document.getElementById('rentMedia');
        mediaSelect.value = mediaId;
        updatePricePreview();
        document.getElementById('panel-aksi').scrollIntoView({ behavior: 'smooth' });
    });

    document.getElementById('searchInput').addEventListener('input', renderCatalog);
    document.getElementById('typeFilter').addEventListener('change', renderCatalog);
    document.getElementById('rentUser').addEventListener('change', updatePricePreview);
    document.getElementById('rentMedia').addEventListener('change', updatePricePreview);
    document.getElementById('rentDays').addEventListener('input', updatePricePreview);

    document.getElementById('mediaType').addEventListener('change', (e) => {
        document.getElementById('extraLabel').textContent =
            e.target.value === 'audiobook' ? 'Durasi (Menit)' : 'Jml Halaman';
    });

    document.getElementById('rentForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const errDays = document.getElementById('errRentDays');
        errDays.textContent = '';

        const days = parseInt(document.getElementById('rentDays').value, 10);
        if (isNaN(days) || days < 1 || days > 30) {
            errDays.textContent = 'Durasi sewa harus antara 1 hingga 30 hari.';
            return;
        }

        try {
            await postAction({
                action: 'rent',
                userId: document.getElementById('rentUser').value,
                mediaId: document.getElementById('rentMedia').value,
                days,
            });
        } catch (err) {
            showToast(err.message, true);
        }
    });

    document.getElementById('userForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = document.getElementById('userName').value.trim();
        const email = document.getElementById('userEmail').value.trim();
        document.getElementById('errUserName').textContent = '';
        document.getElementById('errUserEmail').textContent = '';

        let valid = true;
        if (name.length < 3) {
            document.getElementById('errUserName').textContent = 'Nama minimal 3 karakter.';
            valid = false;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            document.getElementById('errUserEmail').textContent = 'Format email tidak valid.';
            valid = false;
        }
        if (!valid) return;

        try {
            await postAction({
                action: 'register_user',
                name,
                email,
                tier: document.getElementById('userTier').value,
            });
            e.target.reset();
            switchTab('tabRent');
        } catch (err) {
            showToast(err.message, true);
        }
    });

    document.getElementById('mediaForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const title = document.getElementById('mediaTitle').value.trim();
        const author = document.getElementById('mediaAuthor').value.trim();
        document.getElementById('errMediaTitle').textContent = '';
        document.getElementById('errMediaAuthor').textContent = '';

        let valid = true;
        if (!title) {
            document.getElementById('errMediaTitle').textContent = 'Judul buku wajib diisi.';
            valid = false;
        }
        if (!author) {
            document.getElementById('errMediaAuthor').textContent = 'Nama penulis wajib diisi.';
            valid = false;
        }
        if (!valid) return;

        try {
            await postAction({
                action: 'add_media',
                type: document.getElementById('mediaType').value,
                title,
                author,
                category: document.getElementById('mediaCategory').value.trim() || 'Pemrograman',
                extraValue: parseInt(document.getElementById('mediaExtra').value, 10) || 200,
                dailyRate: parseFloat(document.getElementById('mediaRate').value) || 6000,
                stock: parseInt(document.getElementById('mediaStock').value, 10) || 1,
            });
            e.target.reset();
            switchTab('tabRent');
        } catch (err) {
            showToast(err.message, true);
        }
    });

    document.getElementById('transactionBody').addEventListener('click', async (e) => {
        const txId = e.target.getAttribute('data-return');
        if (!txId) return;

        try {
            await postAction({ action: 'return', transactionId: txId });
        } catch (err) {
            showToast(err.message, true);
        }
    });

    fetchState();
});