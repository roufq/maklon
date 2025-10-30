# TODO: Alur Kerja CS → Finance/Admin → Produksi → BPOM → Box

Ringkasan
- CS mulai dengan membuat Ticket. Project wajib terhubung ke Ticket yang antri sesuai urutan (queue).
- Setelah Project dibuat dan dipilihkan Ticket, CS mengajukan persetujuan ke Finance/Admin.
- Jika disetujui, Project masuk ke tahap Produksi, termasuk pengajuan sampel dan QC approval.
- Setelah produksi selesai, CS mengajukan/menyelesaikan BPOM, lalu memilih Box. Selesai.

Peran & Izin (role → permissions)
- CS: tickets.view/create/reply, projects.view/create (submit), bpom.view (request), boxes.view
- Finance: projects.view/edit, tickets.view/reply/close, approvals.approve, qc.view, bpom.view/edit
- Admin: semua
- Produksi: projects.view, tickets.view/reply, qc.view/create/edit, boxes.*

Pisahkan Menu: Tickets vs Messages (baru)
- [ ] Tambah menu terpisah “Messages” di sidebar (di bawah Collaboration) → guard `messages.view`.
- [ ] Messages hanya aktif jika konteks dipilih: Ticket atau Project.
- [ ] Composer pesan disabled hingga user memilih Ticket/Project (via dropdown/filter atau dari halaman Ticket/Project).
- [ ] Pesan dikirim antar user (pengirim → penerima dalam konteks Ticket/Project). Tidak ada DM di luar konteks.
- [ ] Permissions baru: `messages.view`, `messages.create`.
- [ ] Seed permission: Admin (all), Finance (view), Produksi (view/create), CS (view/create).
- [ ] Routes:
  - GET `messages` (index, butuh query `?ticket_id=...` atau `?project_id=...`)
  - POST `messages` (store, validasi wajib salah satu konteks)
- [ ] Model & Migrasi (opsi A – generik, disarankan):
  - `messages` table: `id, tenant_id, context_type (Ticket|Project), context_id, user_id, body, attachment_path, timestamps` + index.
  - Model `Message` + relasi morphTo `context()`.
- [ ] Integrasi UI:
  - Halaman Ticket/Project: tombol “Open Messages” membuka `messages?ticket_id=...` atau `messages?project_id=...`.
  - Di Messages index: filter konteks (Ticket/Project) dan composer.
  - Visibilitas: hanya peserta terkait (requester/assignee/tim project/Admin) yang bisa melihat/mengirim.

Tahapan Alur
1) Ticket oleh CS
   - CS membuat Ticket (tanpa Project terlebih dahulu) → status `open` jika tidak ada yang aktif, jika ada maka `queued`.
   - Notifikasi ke Admin/Finance untuk diketahui (opsional).

2) CS membuat Project dan memilih Ticket (sesuai antrian)
   - Form Project: wajib pilih Ticket yang berstatus `open` (atau `queued` paling depan)
   - Setelah submit, Ticket dikaitkan ke Project dan statusnya tetap `open`.
   - Validasi: satu Project hanya boleh punya satu Ticket aktif (open/in_progress) pada saat yang sama.

3) Pengajuan persetujuan ke Finance/Admin
   - Aksi: `projects.submitForApproval` → set status Project: `pending_approval`
   - Finance/Admin dapat `approve` atau `reject`.
   - Jika approve → set `production_status = scheduled` + notifikasi ke Produksi; jika reject → kembali ke CS dengan catatan.

4) Produksi + Sampel + QC
   - Produksi memproses sesuai Ticket (boleh set ticket → `in_progress`).
   - Pengajuan sampel dari Produksi → QC approval oleh Finance/Admin (gunakan modul QC yang ada).
   - QC gating: tidak bisa lanjut ke selesai produksi sebelum QC `passed`.

5) Notifikasi selesai produksi → Pengajuan BPOM oleh CS
   - Setelah produksi `completed`, kirim notifikasi ke CS untuk mengajukan/menyelesaikan BPOM.
   - Gating: tidak bisa lanjut ke pemilihan Box sebelum BPOM berstatus `active`.

6) Pemilihan Box
   - Setelah BPOM `active`, CS/Produksi memilih Box (Project Box) → selesai.

7) Komunikasi (Messages) dalam konteks
   - CS/Produksi/Finance berkomunikasi lewat Messages yang terhubung ke Ticket/Project.
   - Composer tersedia hanya saat konteks dipilih; attachment disimpan ke `storage/app/public/messages`.

Perubahan Teknis (yang harus dibuat)
- Tickets
  - [ ] Ubah `tickets.project_id` menjadi nullable agar Ticket bisa dibuat sebelum Project.
  - [ ] Tambah validasi: saat membuat Project, wajib pilih Ticket `open/queued` dan kaitkan ke Project (update `ticket.project_id`).
  - [ ] Enforce 1 active ticket per project (open/in_progress) via validation.

- Projects
  - [ ] Tambah aksi `submitForApproval` (route + controller) → set status `pending_approval` dan kirim notifikasi.
  - [ ] Tambah aksi `approve`/`reject` oleh Finance/Admin.
  - [ ] Setelah approve, set `production_status = scheduled` dan notifikasi ke Produksi.

- QC & Produksi
  - [ ] Tambah flow “pengajuan sampel” (flag/field di Project atau Ticket) dan hubungkan dengan QC Result.
  - [ ] Pastikan QC gating diterapkan sebelum menandai produksi selesai.

- BPOM
  - [ ] Tambah tombol/flow pada Project untuk membuat/menautkan BPOM Registration.
  - [ ] Gating: pemilihan Box hanya aktif jika BPOM `active`.

- Box
  - [ ] Tampilkan call-to-action pilih Box hanya setelah BPOM `active`.

- Notifikasi
  - [ ] Ticket baru → notifikasi ke Admin/Finance.
  - [ ] Submit Project → notifikasi ke Finance/Admin.
  - [ ] Approve/Reject → notifikasi ke CS.
  - [ ] Sampel siap → notifikasi ke Finance/Admin untuk QC.
  - [ ] Produksi selesai → notifikasi ke CS untuk BPOM.
  - [ ] BPOM aktif → notifikasi ke CS/Produksi untuk pilih Box.

Update Skema & Kode (detail implementasi)
- Migrations
  - [ ] `tickets.project_id` nullable + index composite (`tenant_id`, `project_id`, `status`).
  - [ ] Optional: tabel `project_approvals` (project_id, approved_by, status, notes).

- Controllers
  - [ ] ProjectController: form create/edit → tambahkan dropdown Ticket `open/queued` (miliki tenant yang sama).
  - [ ] ProjectController: actions submit/approve/reject.
  - [ ] TicketController: izinkan pembuatan ticket tanpa `project_id`, update linking saat project dibuat.
  - [ ] MessagesController: `index` (require context), `store` (require context + permission), validasi peserta.

- Routes
  - [ ] Tambah routes: `projects.submit`, `projects.approve`, `projects.reject` (guard: approvals.approve untuk Finance/Admin).
  - [ ] Tambah routes: `messages.index`, `messages.store` (guard: messages.*) dengan query context.

- Policies/Permissions
  - [ ] Permission baru: `approvals.approve` (Finance/Admin).
  - [ ] Gate Project submit hanya untuk CS/Admin; approve/reject hanya Finance/Admin.
  - [ ] Permissions baru untuk pesan: `messages.view`, `messages.create`.

- Views
  - [ ] Project create/edit: pilih Ticket dari antrian.
  - [ ] Project show: tampilkan status persetujuan, tombol submit/approve/reject sesuai izin.
  - [ ] CTA Box muncul setelah BPOM aktif.
  - [ ] Messages index: filter Ticket/Project, daftar pesan, composer (disabled tanpa konteks), unggah lampiran.

- Tests
  - [ ] Test alur E2E: CS buat Ticket → buat Project pilih Ticket → submit → Finance approve → Produksi proses + QC → notifikasi CS → BPOM aktif → pilih Box.
  - [ ] Test gating (tanpa approve tidak bisa produksi; tanpa BPOM aktif tidak bisa pilih Box).

Catatan
- Antrian tiket tetap per project; di tahap awal (pra-project), gunakan Ticket tanpa `project_id`, Project pertama yang dibuat akan mengaitkan Ticket.
- Gunakan tenant scope di semua query.

Estimasi Waktu (Timeline Perkiraan)
- Tickets: buat tanpa project_id, linking saat buat Project, validasi 1 aktif per project
  - Backend (migrasi + model + validasi controller): 0.5 hari
  - UI Project (dropdown pilih ticket antrian): 0.5 hari
- Project submit/approve/reject + notifikasi
  - Routes + controller actions + guards + notifikasi: 1 hari
  - UI tombol submit/approve/reject di Project show: 0.5 hari
- Produksi: alur pengajuan sampel + QC gating penegasan
  - Field/flag + integrasi QC result + guard selesai produksi: 1 hari
- BPOM gating menuju Box
  - Tombol/flow attach/create BPOM di Project + guard Box: 1 hari
- Messages (menu terpisah) dengan konteks Ticket/Project
  - Migrations + model (polymorphic), permissions, controller (index/store): 1 hari
  - Views (index + composer + filter konteks) + sidebar + policies: 1 hari
- Notifikasi tahap-tahap (ticket baru, submit, approve/reject, sampel siap, produksi selesai, BPOM aktif)
  - Hook + pembuatan Notification: 0.5 hari
- Testing
  - E2E alur utama + gating: 1 – 1.5 hari
  - Penyesuaian unit/feature tests terkait permissions: 0.5 hari
- Stabilization & UAT
  - Perbaikan minor, regresi cepat, dokumentasi singkat: 0.5 hari

Total estimasi: 7 – 9 hari kerja (1.5 – 2 minggu kalender), tergantung kompleksitas UI yang diinginkan dan revisi saat UAT.
