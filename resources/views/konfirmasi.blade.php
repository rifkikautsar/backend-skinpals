<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/css/style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
      integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <title>Skinpals!</title>
  </head>
  <body>
    <header>
      <nav class="navbar navbar-expand-lg navbar-light fixed-top scrolling-navbar" style="background-color: #fafafa">
        <div class="container">
          <a class="navbar-brand" href="https://skinpals.id">
            <img src="https://api.skinpals.id/images/256px.png" alt="Logo" width="40" class="d-inline-block align-text-bottom" />
            <span style="color: #293942">Skinpals</span>
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </nav>
    </header>

    <main style="background-color: #fafafa; height: max-content">
      <section>
        <div class="container text-center">
          <div class="row"><h1 class="row" style="padding-top: 150px;" id="founderMiddle">{{ $title }}</h1>
          </div>
        </div>
      </section>

      <!-- Section Features -->
    </main>

    <footer id="contacts" class="text-center text-muted" style="background-color: #fafafa">
      <!-- Grid container -->
      <div class="container p-4">
        <!-- Section: Social media -->
        <section class="mb-4 border-bottom">
          <!-- Instagram -->
          <a class="btn text-dark btn-floating btn-link btn-lg m-1" href="https://instagram.com/skinpals.id" role="button" data-mdb-ripple-color="dark">
            <i class="fab fa-instagram"></i>
          </a>
          <!-- Linkedin -->
          <a class="btn text-dark btn-floating btn-link btn-lg m-1" href="https://www.linkedin.com/company/skinpals-indonesia/" role="button" data-mdb-ripple-color="dark">
            <i class="fab fa-linkedin-in"></i>
          </a>
        </section>
        <!-- Section: Social media -->
        <!-- Section Skinpals Contacts about -->
        <section class="mb-4 border-bottom">
          <div class="container text-center text-md-start mt-5">
            <div class="row mt-3">
              <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold mb-4"><i class="fa-solid fa-building me-3"></i>Skinpals</h6>
                <p>Aplikasi karya anak bangsa yang mampu mendeteksi kondisi kulit wajah dan memberikan treatment terbaik untuk kesehatan kulit wajah Anda.</p>
              </div>
              <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold mb-4">Contact Us</h6>
                <p>
                  <i class="fas fa-home me-3"></i>
                  Bandung, Jawa Barat, Indonesia
                </p>
                <p>
                  <i class="fas fa-envelope me-3"></i>
                  skinpals@skinpals.id
                </p>
                <p>
                  <i class="fas fa-phone me-3"></i>
                  +62 812 3456 7890
                </p>
              </div>
            </div>
          </div>
        </section>

        <div class="text-center p-2">
          Copyright © 2022 |
          <a href="https://skinpals.id" style="text-decoration: none" class="text-reset fw-bold">Skinpals</a>
          | All rights reserved
        </div>
      </div>
      <!-- Grid container -->
    </footer>

    <!-- Bootstrap -->
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js" integrity="sha384-IDwe1+LCz02ROU9k972gdyvl+AESN10+x7tBKgc9I5HFtuNz0wWnPclzo6p9vxnk" crossorigin="anonymous"></script>
  </body>
</html>
