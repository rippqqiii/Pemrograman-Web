# 4. JavaScript: Konfirmasi Hapus via Event `submit`

## 4.1 Perubahan dari Event `click` ke `submit`
Pada Jobsheet sebelumnya, tombol hapus hanya berupa `<button>` dengan listener `click`. Namun karena sekarang tombol hapus berada di dalam `<form class="form-hapus" method="post">`, konfirmasi dialihkan ke event `submit` form.

Dengan menangani event `submit`, kita dapat memanggil `event.preventDefault()` apabila pengguna memilih **Batal** pada kotak dialog konfirmasi, sehingga form tidak terkirim ke server:

```javascript
function initHapusConfirm() {
    document.addEventListener("submit", function (e) {
        const form = e.target;
        if (!form.classList.contains("form-hapus")) return;

        const row = form.closest("tr");
        const nama = row ? row.querySelector("td")?.textContent : "data ini";
        const yakin = confirm("Yakin ingin menghapus \"" + nama.trim() + "\"?");
        if (!yakin) {
            e.preventDefault(); // Mencegah pengiriman form
        }
    });
}
```

## 4.2 Event Delegation
Listener dipasang pada objek `document` dengan teknik *event delegation*. Hal ini memastikan tombol hapus pada baris tabel apa pun tetap berfungsi dengan baik meskipun struktur DOM berubah.
