<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SentiTweet</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-home">
  <div class="bg">
    <div class="bubbles">
      <span style="--i: 11"></span>
      <span style="--i: 12"></span>
      <span style="--i: 24"></span>
      <span style="--i: 10"></span>
      <span style="--i: 14"></span>
      <span style="--i: 23"></span>
      <span style="--i: 18"></span>
      <span style="--i: 16"></span>
      <span style="--i: 19"></span>
      <span style="--i: 20"></span>
      <span style="--i: 22"></span>
      <span style="--i: 25"></span>
      <span style="--i: 18"></span>
      <span style="--i: 21"></span>
      <span style="--i: 13"></span>
      <span style="--i: 15"></span>
      <span style="--i: 26"></span>
      <span style="--i: 17"></span>
      <span style="--i: 13"></span>
      <span style="--i: 28"></span>
      <span style="--i: 11"></span>
      <span style="--i: 12"></span>
      <span style="--i: 24"></span>
      <span style="--i: 10"></span>
      <span style="--i: 14"></span>
      <span style="--i: 23"></span>
      <span style="--i: 18"></span>
      <span style="--i: 16"></span>
      <span style="--i: 19"></span>
      <span style="--i: 20"></span>
      <span style="--i: 22"></span>
      <span style="--i: 25"></span>
      <span style="--i: 18"></span>
      <span style="--i: 21"></span>
      <span style="--i: 13"></span>
      <span style="--i: 15"></span>
      <span style="--i: 26"></span>
      <span style="--i: 17"></span>
      <span style="--i: 13"></span>
      <span style="--i: 28"></span>
    </div>
    <div class="header text-center">
      <img src="img/logo_sentitweet.png" alt="logo_sentitweet.png" />
    </div>
    <div class="content mx-auto mb-2 text-light">
      <div class="content-header text-center">
        <h4 class="text-center">Beri Ulasan Kami</h4>
        <img src="img/star.png">
      </div>
      <form id="commentForm" method="POST" action="controller.php">
        <div class="mb-4">
          <label for="textFieldTweet" class="form-label">Komentar</label>
          <textarea name="komentar" type="text" class="form-control" id="textFieldTweet" rows="3"
            placeholder="Ceritakan pengalaman Anda . . ."></textarea>
        </div>
        <div class="mb-3 text-center">
          <button type="submit" id="submitButton" class="btn btn-secondary" disabled>Kirim</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Alert untuk error -->
  <div class="alert alert-danger alert-dismissible fade show" id="alertBox" role="alert"
    style="display:none; position:fixed; top:20px; right:20px; z-index:10000;">
    <i class="bi bi-x-circle-fill me-"></i>
    <span id="alertMessage">Kalimat minimal 3 kata!</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>

  <!-- Alert untuk sukses -->
  <div class="alert alert-success alert-dismissible fade show" id="successBox" role="alert"
    style="display:none; position:fixed; top:20px; right:20px; z-index:10000;">
    <i class="bi bi-check-circle-fill me-"></i>
    <span id="successMessage">Komentar berhasil dikirim!</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="sentimentModal" tabindex="-1" aria-labelledby="sentimentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="sentimentModalLabel">Hasil Sentimen</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php
          if (isset($_GET['sentiment'])) {
            $sentiment = htmlspecialchars($_GET['sentiment']);
            echo '<p>Sentimen Komentar: ' . $sentiment . '</p>';

            // Tampilkan pesan tambahan berdasarkan hasil sentimen
            if ($sentiment === 'positive') {
              echo '<p>Terimakasih atas feedbacknya! Senang mendengarnya!</p>';
            } elseif ($sentiment === 'neutral') {
              echo '<p>Terimakasih atas feedbacknya. Kami akan terus berusaha meningkatkan layanan kami.</p>';
            } elseif ($sentiment === 'negative') {
              echo '<p>Terimakasih atas feedbacknya. Kami mohon maaf atas ketidaknyamanan yang Anda alami.</p>';
            }
          } else {
            echo '<p>Menunggu hasil...</p>';
          }
          ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>


  <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLR6fl2R8/c7I4f0qsEOePqXvs2gqIHuM6sVFk3A07"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
    crossorigin="anonymous"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const urlParams = new URLSearchParams(window.location.search);

      // Menampilkan modal jika ada parameter 'sentiment' di URL
      if (urlParams.has('sentiment')) {
        var myModal = new bootstrap.Modal(document.getElementById('sentimentModal'));
        myModal.show();
      }

      // Menampilkan alert jika ada parameter 'success' di URL
      if (urlParams.has('success')) {
        showAlert(urlParams.get('success'), true);
      }

      // Update button state berdasarkan input textarea
      const textFieldTweet = document.getElementById('textFieldTweet');
      const submitButton = document.getElementById('submitButton');

      function updateButtonState() {
        const text = textFieldTweet.value.trim();
        const words = text.split(/\s+/);

        if (words.length < 3) {
          submitButton.disabled = true;
          submitButton.classList.add('btn-secondary');
          submitButton.classList.remove('btn-primary');
          showAlert("Kalimat minimal 3 kata!", false);
        } else {
          submitButton.disabled = false;
          submitButton.classList.add('btn-primary');
          submitButton.classList.remove('btn-secondary');
          hideAlert();
        }
      }

      function showAlert(message, isSuccess) {
        const alertBox = document.getElementById(isSuccess ? 'successBox' : 'alertBox');
        const alertMessage = alertBox.querySelector('span');
        alertMessage.textContent = message;
        alertBox.style.display = 'block';
      }

      function hideAlert() {
        const alertBoxes = document.querySelectorAll('.alert');
        alertBoxes.forEach(box => {
          box.style.display = 'none';
        });
      }

      textFieldTweet.addEventListener('input', updateButtonState);
    });
  </script>
</body>

</html>