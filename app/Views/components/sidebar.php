<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link <?php echo (uri_string() == '') ? "" : "collapsed" ?>" href="/">
        <i class="bi bi-grid"></i>
        <span>Home</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?php echo (uri_string() == 'transaction') ? "" : "collapsed" ?>" href="/transaction">
        <i class="bi bi-cart-check"></i>
        <span>Keranjang</span>
      </a>
    </li>

    <?php if (session()->get('role') == 'admin') : ?>
      <li class="nav-item">
        <a class="nav-link <?php echo (uri_string() == 'product') ? "" : "collapsed" ?>" href="/product">
          <i class="bi bi-receipt"></i>
          <span>Produk</span>
        </a>
      </li>
    <?php endif; ?>

  </ul>
</aside>