# 6. CSS Pendukung Fitur Baru & Dark Theme

Fitur CRUD penuh, pencarian, dan pagination membutuhkan penyesuaian styling agar serasi dengan skema warna **Dark Theme Gramedia**:
- Background Utama: `#0D0F0C`
- Kontainer & Card: `#122538`
- Header & Aksen Primer: `#03365B`
- Aksen Sekunder / Tautan: `#01669C`
- Warna Teks: `#9BBACD`

---

## 6.1 Tombol Edit & Form Hapus
```css
/* Tombol Edit bertindak seperti hyperlink namun bergaya tombol */
td a.btn-edit {
    display: inline-block;
    padding: 0.35rem 0.7rem;
    margin-right: 0.35rem;
    border-radius: 4px;
    font-size: 0.85rem;
    background-color: #d97706; /* Kuning-emas/amber */
    color: #ffffff;
    text-decoration: none;
    font-weight: 500;
}

td a.btn-edit:hover {
    background-color: #b45309;
    color: #ffffff;
}

/* Form hapus dibuat inline agar tombol hapus sejajar dengan tombol edit */
td form.form-hapus {
    display: inline;
}

td form.form-hapus button.btn-hapus {
    background-color: #dc3545; /* Merah bahaya */
    color: #ffffff;
    border: none;
    padding: 0.35rem 0.7rem;
    border-radius: 4px;
    font-size: 0.85rem;
    cursor: pointer;
}

td form.form-hapus button.btn-hapus:hover {
    background-color: #bb2d3b;
}
```

---

## 6.2 Navigasi Pagination
```css
.pagination {
    display: flex;
    gap: 0.5rem;
    margin-top: 1.25rem;
}

.pagination a {
    padding: 0.4rem 0.75rem;
    border: 1px solid #03365B;
    border-radius: 4px;
    color: #9BBACD;
    background-color: #0D0F0C;
    text-decoration: none;
    font-size: 0.9rem;
}

.pagination a:hover {
    background-color: #03365B;
    color: #ffffff;
}

.pagination a.active {
    background-color: #01669C;
    color: #ffffff;
    border-color: #01669C;
}
```
