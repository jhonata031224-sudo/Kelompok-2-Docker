<?php
$host = 'localhost';
$db   = 'jasuke_db';
$user = 'root';
$pass = 'jasuke123';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama     = $conn->real_escape_string($_POST['nama']);
    $whatsapp = $conn->real_escape_string($_POST['whatsapp']);
    $menu     = $conn->real_escape_string($_POST['menu']);
    $jumlah   = intval($_POST['jumlah']);
    $alamat   = $conn->real_escape_string($_POST['alamat']);
    $catatan  = $conn->real_escape_string($_POST['catatan']);

    $sql = "INSERT INTO pesanan (nama, whatsapp, menu, jumlah, alamat, catatan)
            VALUES ('$nama', '$whatsapp', '$menu', '$jumlah', '$alamat', '$catatan')";

    if ($conn->query($sql) === TRUE) {
        $pesan = "SUCCESS";
    } else {
        $pesan = "ERROR: " . $conn->error;
    }
}

$result = $conn->query("SELECT * FROM pesanan ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pesanan - Jasuke Maz D</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
  <style>
    :root { --orange:#ff6f00; --orange-light:#ff9800; --orange-dark:#e65100; --cream:#fff8e1; }
    * { margin:0; padding:0; box-sizing:border-box; }
    html,body { font-family:'Poppins',sans-serif; background:var(--cream); overflow-x:hidden; }
    .navbar { position:fixed; top:0; width:100%; background:rgba(255,255,255,0.92); backdrop-filter:blur(16px); display:flex; justify-content:space-between; align-items:center; padding:12px 50px; z-index:999; box-shadow:0 4px 20px rgba(230,81,0,0.12); border-bottom:2px solid rgba(255,152,0,0.2); }
    .navbar-logo { height:60px; filter:drop-shadow(0 2px 6px rgba(255,160,0,0.4)); transition:all 0.4s; }
    .navbar-logo:hover { filter:drop-shadow(0 4px 14px rgba(255,111,0,0.65)); transform:translateY(-2px); }
    .navbar ul { list-style:none; display:flex; gap:10px; }
    .navbar ul li a { text-decoration:none; color:#444; font-weight:600; font-size:0.95rem; padding:8px 18px; border-radius:30px; transition:all 0.3s; }
    .navbar ul li a:hover,.navbar ul li a.active { background:var(--orange); color:white; }
    .page-header { background:linear-gradient(135deg,#e65100,#ff9800); padding:130px 50px 70px; text-align:center; color:white; }
    .page-header h1 { font-family:'Playfair Display',serif; font-size:3rem; margin-bottom:12px; }
    .page-header p { font-size:1.1rem; opacity:0.9; max-width:500px; margin:0 auto; }
    .order-section { padding:70px 50px; max-width:1200px; margin:0 auto; }
    .order-grid { display:grid; grid-template-columns:1.2fr 1fr; gap:40px; }
    .order-form-box { background:white; border-radius:24px; padding:40px; box-shadow:0 6px 30px rgba(0,0,0,0.08); }
    .order-form-box h2 { font-family:'Playfair Display',serif; font-size:1.8rem; color:#222; margin-bottom:24px; }
    .form-group { margin-bottom:18px; }
    .form-group label { display:block; font-weight:600; color:#444; margin-bottom:7px; font-size:0.9rem; }
    .form-group input,.form-group select,.form-group textarea { width:100%; padding:12px 16px; border:2px solid #eee; border-radius:12px; font-family:'Poppins',sans-serif; font-size:0.92rem; transition:border-color 0.3s; outline:none; background:#fafafa; }
    .form-group input:focus,.form-group select:focus,.form-group textarea:focus { border-color:var(--orange); background:white; }
    .form-group textarea { height:90px; resize:vertical; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .btn-submit { width:100%; padding:15px; background:var(--orange); color:white; border:none; border-radius:14px; font-family:'Poppins',sans-serif; font-weight:700; font-size:1rem; cursor:pointer; transition:all 0.3s; margin-top:10px; }
    .btn-submit:hover { background:var(--orange-dark); transform:translateY(-2px); box-shadow:0 8px 25px rgba(255,111,0,0.4); }
    .cart-box { background:white; border-radius:24px; padding:36px; box-shadow:0 6px 30px rgba(0,0,0,0.08); height:fit-content; position:sticky; top:100px; }
    .cart-box h2 { font-family:'Playfair Display',serif; font-size:1.8rem; color:#222; margin-bottom:20px; }
    .menu-item { display:flex; align-items:center; gap:14px; padding:14px 0; border-bottom:1px solid #f0f0f0; }
    .menu-item img { width:65px; height:65px; object-fit:cover; border-radius:12px; }
    .menu-item-info h4 { font-size:0.95rem; font-weight:700; color:#222; }
    .menu-item-info p { color:var(--orange); font-weight:600; font-size:0.9rem; }
    .cart-total { background:var(--cream); border-radius:14px; padding:18px; margin-top:16px; }
    .cart-total-row { display:flex; justify-content:space-between; margin-bottom:10px; }
    .cart-total-row span { color:#666; font-size:0.9rem; }
    .cart-total-row strong { color:#222; font-weight:700; }
    .cart-total-final { display:flex; justify-content:space-between; padding-top:12px; border-top:2px solid #ffe0b2; }
    .cart-total-final span { font-weight:700; color:#222; }
    .cart-total-final strong { color:var(--orange); font-size:1.3rem; font-weight:800; }
    .orders-section { padding:0 50px 70px; max-width:1200px; margin:0 auto; }
    .table-box { background:white; border-radius:24px; padding:40px; box-shadow:0 6px 30px rgba(0,0,0,0.08); }
    .table-box h2 { font-family:'Playfair Display',serif; font-size:1.8rem; color:#222; margin-bottom:24px; }
    table { width:100%; border-collapse:collapse; font-size:0.88rem; }
    th { background:var(--orange); color:white; padding:12px 14px; text-align:left; }
    td { padding:12px 14px; border-bottom:1px solid #ffe0b2; color:#444; }
    tr:hover td { background:#fff8e1; }
    .badge-success { background:#e8f5e9; color:#2e7d32; padding:4px 10px; border-radius:20px; font-size:0.8rem; font-weight:600; }
    .alert-success { background:#e8f5e9; color:#2e7d32; padding:15px 20px; border-radius:12px; margin-bottom:20px; font-weight:600; text-align:center; }
    .alert-error { background:#ffebee; color:#c62828; padding:15px 20px; border-radius:12px; margin-bottom:20px; font-weight:600; text-align:center; }
    .platform-section { background:white; padding:80px 50px; }
    .platform-section h2 { font-family:'Playfair Display',serif; text-align:center; font-size:2.2rem; color:#222; margin-bottom:10px; }
    .platform-section > p { text-align:center; color:#888; margin-bottom:50px; }
    .platform-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:28px; max-width:900px; margin:0 auto; }
    .platform-card { border-radius:22px; padding:35px 25px; text-align:center; box-shadow:0 4px 20px rgba(0,0,0,0.07); transition:all 0.35s; text-decoration:none; border:2px solid transparent; }
    .platform-card:hover { transform:translateY(-8px); border-color:var(--orange); }
    .platform-card.ig { background:linear-gradient(135deg,#ffecd2,#fcb69f); }
    .platform-card.wa { background:linear-gradient(135deg,#d4f1c0,#b3e8a0); }
    .platform-card.grab { background:linear-gradient(135deg,#c8f7c5,#a8e6a3); }
    .platform-card img { width:75px; height:75px; object-fit:contain; margin-bottom:16px; }
    .platform-card h3 { font-size:1.1rem; font-weight:700; color:#222; margin-bottom:7px; }
    .platform-card p { color:#666; font-size:0.88rem; margin-bottom:18px; }
    .platform-btn { display:inline-block; padding:10px 26px; background:var(--orange); color:white; border-radius:30px; font-weight:700; font-size:0.85rem; text-decoration:none; }
    footer { background:#1a1a1a; color:white; padding:50px; display:flex; justify-content:space-between; flex-wrap:wrap; gap:30px; }
    .footer-brand h3 { font-family:'Playfair Display',serif; font-size:1.6rem; color:var(--orange-light); }
    .footer-brand p { color:#aaa; font-size:0.9rem; margin-top:8px; max-width:260px; }
    .footer-links h4 { color:white; font-weight:600; margin-bottom:14px; }
    .footer-links a { display:block; color:#aaa; text-decoration:none; font-size:0.9rem; margin-bottom:8px; }
    .footer-links a:hover { color:var(--orange-light); }
    .footer-bottom { text-align:center; padding:18px; background:#111; color:#666; font-size:0.85rem; }
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center; }
    .modal-overlay.show { display:flex; }
    .modal-box { background:white; border-radius:24px; padding:50px 40px; text-align:center; max-width:400px; width:90%; animation:popIn 0.4s ease; }
    .modal-box .icon { font-size:4rem; margin-bottom:16px; }
    .modal-box h3 { font-family:'Playfair Display',serif; font-size:1.8rem; color:#222; margin-bottom:10px; }
    .modal-box p { color:#888; margin-bottom:24px; }
    .modal-btn { background:var(--orange); color:white; border:none; padding:12px 30px; border-radius:30px; font-family:'Poppins',sans-serif; font-weight:700; cursor:pointer; font-size:0.95rem; }
    @keyframes popIn { from{opacity:0;transform:scale(0.8)} to{opacity:1;transform:scale(1)} }
    @media(max-width:900px) { .order-grid{grid-template-columns:1fr} .navbar{padding:10px 20px} .order-section,.orders-section{padding:40px 20px} .platform-section{padding:60px 20px} footer{padding:40px 20px;flex-direction:column} }
  </style>
</head>
<body>
  <nav class="navbar">
    <a href="index.html"><img src="Jagung.png" alt="Logo" class="navbar-logo"></a>
    <ul>
      <li><a href="index.html">Home</a></li>
      <li><a href="produk.html">Produk</a></li>
      <li><a href="pesanan.php" class="active">Pesanan</a></li>
      <li><a href="kontak.html">Kontak</a></li>
    </ul>
  </nav>

  <div class="page-header">
    <h1>🛒 Form Pesanan</h1>
    <p>Isi form di bawah untuk memesan Jasuke Maz D favoritmu!</p>
  </div>

  <section class="order-section">
    <?php if ($pesan == "SUCCESS"): ?>
      <div class="alert-success">✅ Pesanan berhasil disimpan! Kami akan segera menghubungi kamu via WhatsApp.</div>
    <?php elseif ($pesan != ""): ?>
      <div class="alert-error">❌ <?= $pesan ?></div>
    <?php endif; ?>

    <div class="order-grid">
      <div class="order-form-box">
        <h2>📋 Detail Pesanan</h2>
        <form method="POST" action="pesanan.php">
          <div class="form-row">
            <div class="form-group">
              <label>Nama Lengkap *</label>
              <input type="text" name="nama" placeholder="Masukkan nama kamu" required>
            </div>
            <div class="form-group">
              <label>No. WhatsApp *</label>
              <input type="text" name="whatsapp" id="telp" placeholder="08xxxxxxxxx" required>
            </div>
          </div>
          <div class="form-group">
            <label>Pilih Menu *</label>
            <select name="menu" id="menu" required onchange="updateCart()">
              <option value="">-- Pilih Menu --</option>
              <option value="Jasuke Original|10000">Jasuke Original - Rp 10.000</option>
              <option value="Keju Coklat|12000">Keju Coklat - Rp 12.000</option>
              <option value="Coklat Milo|12000">Coklat Milo - Rp 12.000</option>
              <option value="Keju Milo|12000">Keju Milo - Rp 12.000</option>
              <option value="Coklat|12000">Coklat - Rp 12.000</option>
              <option value="Milo|12000">Milo - Rp 12.000</option>
            </select>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Jumlah *</label>
              <input type="number" name="jumlah" id="jumlah" min="1" value="1" required onchange="updateCart()">
            </div>
            <div class="form-group">
              <label>Metode Pengiriman</label>
              <select name="pengiriman">
                <option value="ambil">Ambil Sendiri</option>
                <option value="grab">GrabFood</option>
                <option value="gojek">GoFood</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Alamat Pengiriman</label>
            <input type="text" name="alamat" placeholder="(Isi jika diantar)">
          </div>
          <div class="form-group">
            <label>Catatan Tambahan</label>
            <textarea name="catatan" placeholder="Contoh: Gula sedikit, extra keju, dll..."></textarea>
          </div>
          <button type="submit" class="btn-submit">🌽 Kirim Pesanan</button>
        </form>
      </div>

      <div class="cart-box">
        <h2>🧾 Ringkasan</h2>
        <div class="menu-item">
          <img src="orig.png" alt="Menu" id="cartImg">
          <div class="menu-item-info">
            <h4 id="cartName">Pilih menu terlebih dahulu</h4>
            <p id="cartPrice">Rp 0</p>
          </div>
        </div>
        <div class="cart-total">
          <div class="cart-total-row"><span>Menu dipilih:</span><strong id="summMenu">-</strong></div>
          <div class="cart-total-row"><span>Jumlah:</span><strong id="summJumlah">-</strong></div>
          <div class="cart-total-row"><span>Harga satuan:</span><strong id="summHarga">-</strong></div>
          <div class="cart-total-final"><span>Total:</span><strong id="summTotal">Rp 0</strong></div>
        </div>
        <div style="margin-top:16px;padding:14px;background:#fff3e0;border-radius:12px;font-size:0.85rem;color:#e65100;">
          💡 <strong>Tips:</strong> Pesanan disimpan ke database & dikonfirmasi via WhatsApp dalam 5-10 menit.
        </div>
      </div>
    </div>
  </section>

  <section class="orders-section">
    <div class="table-box">
      <h2>📊 Daftar Pesanan Masuk</h2>
      <table>
        <tr><th>No</th><th>Nama</th><th>Menu</th><th>Jumlah</th><th>WhatsApp</th><th>Alamat</th><th>Waktu</th><th>Status</th></tr>
        <?php $no=1; while($row=$result->fetch_assoc()): $menuName=explode('|',$row['menu'])[0]; ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['nama']) ?></td>
          <td><?= htmlspecialchars($menuName) ?></td>
          <td><?= $row['jumlah'] ?> porsi</td>
          <td><?= htmlspecialchars($row['whatsapp']) ?></td>
          <td><?= htmlspecialchars($row['alamat']) ?: '-' ?></td>
          <td><?= $row['created_at'] ?></td>
          <td><span class="badge-success">✓ Masuk</span></td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </section>

  <section class="platform-section">
    <h2>Order Langsung</h2>
    <p>Atau pesan langsung melalui platform favoritmu</p>
    <div class="platform-grid">
      <a href="https://www.instagram.com/jasuke_mazde/" target="_blank" class="platform-card ig">
        <img src="https://cdn-icons-png.flaticon.com/512/174/174855.png" alt="Instagram">
        <h3>@jasuke_mazde</h3><p>Follow dan DM untuk order di Instagram</p>
        <span class="platform-btn">Buka Instagram</span>
      </a>
      <a href="https://wa.me/6283898101912?text=Halo%2C%20saya%20mau%20pesan%20Jasuke!" target="_blank" class="platform-card wa">
        <img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp">
        <h3>WhatsApp Kami</h3><p>Chat langsung untuk order dan info produk</p>
        <span class="platform-btn">Chat Sekarang</span>
      </a>
      <a href="https://r.grab.com/g/2-1-6-C7D3EUX2GKBAVN" target="_blank" class="platform-card grab">
        <img src="grab.png" alt="GrabFood">
        <h3>GrabFood</h3><p>Order lewat GrabFood, diantar ke pintu rumahmu</p>
        <span class="platform-btn">Order di Grab</span>
      </a>
    </div>
  </section>

  <footer>
    <div class="footer-brand">
      <h3>Jasuke Maz D</h3>
      <p>Camilan sehat berbahan jagung lokal pilihan dari Cimahi Selatan.</p>
    </div>
    <div class="footer-links">
      <h4>Navigasi</h4>
      <a href="index.html">Home</a><a href="produk.html">Produk</a>
      <a href="pesanan.php">Pesanan</a><a href="kontak.html">Kontak</a>
    </div>
    <div class="footer-links">
      <h4>Hubungi Kami</h4>
      <a href="https://wa.me/6283898101912">WhatsApp</a>
      <a href="https://www.instagram.com/jasuke_mazde/">Instagram</a>
    </div>
  </footer>
  <div class="footer-bottom">© 2025 Jasuke Maz D · All Rights Reserved</div>

  <div class="modal-overlay" id="successModal">
    <div class="modal-box">
      <div class="icon">✅</div>
      <h3>Pesanan Dikirim!</h3>
      <p>Pesananmu telah disimpan ke database. Cek WhatsApp untuk konfirmasi!</p>
      <button class="modal-btn" onclick="document.getElementById('successModal').classList.remove('show')">Tutup</button>
    </div>
  </div>

  <script>
    const menuImages = {'Jasuke Original':'orig.png','Keju Coklat':'cokju.png','Coklat Milo':'cokmil.png','Coklat':'coklat.png','Keju Milo':'kemil.png','Milo':'milo.png'};
    function formatRupiah(n){return 'Rp '+n.toLocaleString('id-ID');}
    function updateCart(){
      const v=document.getElementById('menu').value;
      const j=parseInt(document.getElementById('jumlah').value)||1;
      if(!v)return;
      const[name,p]=v.split('|'); const price=parseInt(p); const total=price*j;
      document.getElementById('cartImg').src=menuImages[name]||'orig.png';
      document.getElementById('cartName').textContent=name;
      document.getElementById('cartPrice').textContent=formatRupiah(price)+' / porsi';
      document.getElementById('summMenu').textContent=name;
      document.getElementById('summJumlah').textContent=j+' porsi';
      document.getElementById('summHarga').textContent=formatRupiah(price);
      document.getElementById('summTotal').textContent=formatRupiah(total);
    }
    <?php if($pesan=="SUCCESS"): ?>
    document.getElementById('successModal').classList.add('show');
    <?php endif; ?>
  </script>
</body>
</html>
