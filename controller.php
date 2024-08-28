<?php
class SentimentController {
    public function analyzeAndSave() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $komentar = $_POST['komentar'];

            try {
                $url = 'http://127.0.0.1:8000/sentiment';
                $data = json_encode(['text' => $komentar]);

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data)
                ]);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200) {
                    $result = json_decode($response, true);
                    $label = $result['sentiment'];
                    
                    header("Location: index.php?sentiment=" . urlencode($label));
                } else {
                    echo "Gagal mengirim ulasan untuk dianalisis.";
                }
            } catch (Exception $e) {
                echo "Terjadi kesalahan: " . $e->getMessage();
            }
        } else {
            echo "Permintaan tidak valid.";
        }
    }
}

$controller = new SentimentController();
$controller->analyzeAndSave();
?>
