<?php
declare(strict_types=1);

if (is_file(__DIR__ . '/vendor/autoload.php')) {
  $loader = require __DIR__ . '/vendor/autoload.php';
} else {
  die('The main autoloader not found! Did you forget to run "composer install"?');
}

$uri = $_SERVER['REQUEST_URI'] ?? '';

?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>UniSurf</title>
  <!-- Font Awesome icons (free version)-->
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link href="/assets/css/main.css" rel="stylesheet">
</head>
<body id="page-top">
<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
  <div class="container">
    <a class="navbar-brand" href="/"><img src="assets/img/unisurf-logo.png" alt="UniSurf Logo"/></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">Menu <i class="fas fa-bars ms-1"></i></button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav text-uppercase ms-auto py-4 py-lg-0">
        <li class="nav-item"><a class="nav-link" href="/services">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="/entwicklung">Entwicklung</a></li>
        <li class="nav-item"><a class="nav-link" href="/hosting">Hosting</a></li>
      </ul>
    </div>
  </div>
</nav>
<!-- Masthead-->
<?php if (empty($uri)) : ?>
<?php endif; ?>
<header class="masthead">
  <div class="container">
    <div class="masthead-subheading">Wir bieten den Service für einzigartige Ideen</div>
    <div class="masthead-heading text-uppercase">Unique Surfing!</div>
    <a class="btn btn-primary btn-xl text-uppercase" href="#services">Unsere Services</a>
  </div>
</header>
<section class="page-section" id="services">
  <div class="container">
    <?php

    $parsedown = new Parsedown();

    if (file_exists(__DIR__ . "/sites/content/{$uri}.md")) {
      echo $parsedown->text(file_get_contents(__DIR__ . "/sites/content/{$uri}.md"));
    } else {
      echo $parsedown->text(file_get_contents(__DIR__ . '/sites/content/index.md'));
    }

    ?>
  </div>
</section>
<!-- Footer-->
<footer class="footer py-4">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 text-lg-start">Copyright &copy; <strong>UniSurf</strong> <?= date('Y') ?></div>
      <div class="col-lg-6 text-lg-end">
        <a class="link-dark text-decoration-none me-3" href="/impressum">Impressum</a>
        <a class="link-dark text-decoration-none" href="/datenschutz">Datenschutz</a>
      </div>
    </div>
  </div>
</footer>
<script src="/assets/js/main.js"></script>
</body>
</html>
