<?php
include "db.php";
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's a JSON or FormData request
    $json_input = json_decode(file_get_contents('php://input'), true);
    $job_text = isset($json_input['job_text']) ? trim($json_input['job_text']) : trim($_POST['job_text'] ?? '');
    $has_image = (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK);

    if (empty($job_text) && !$has_image) {
        echo json_encode(["status" => "error", "message" => "Please enter job text or upload an image"]);
        exit();
    }

    // ---------- VALIDATION ----------
    function isValidJobText($text) {
        $cleanText = preg_replace("/[^a-zA-Z\s]/", "", $text);
        $words = explode(" ", $cleanText);
        $wordCount = count(array_filter($words));

        if ($wordCount < 3) return false;
        if (!preg_match("/[aeiou]/i", $text)) return false;
        return true;
    }

    // Only validate text if NO image is provided
    if (!$has_image && !isValidJobText($job_text)) {
        echo json_encode(["status" => "error", "message" => "Please enter valid job offer text ❗"]);
        exit();
    }

    // ---------- API CALL ----------
    $api_url = "http://127.0.0.1:5000/predict";
    $prediction = "";
    $confidence = 0;
    $reason = "";
    $extracted_text = "";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $api_url = "http://127.0.0.1:5000/predict-image";
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed = ['jpg', 'jpeg', 'png'];
        if (!in_array($file_ext, $allowed)) {
            echo json_encode(["status" => "error", "message" => "Only JPG, JPEG & PNG are allowed"]);
            exit();
        }

        $upload_path = "uploads/" . time() . "_" . $file_name;
        if (move_uploaded_file($file_tmp, $upload_path)) {
            $ch = curl_init($api_url);
            $cfile = new CURLFile($upload_path, $_FILES['image']['type'], $_FILES['image']['name']);
            $data = ['image' => $cfile];
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            unlink($upload_path); // Delete temp file

            if ($httpCode === 0) {
                echo json_encode(["status" => "error", "message" => "API Connection Failed (Image): The detection service is not running. Please start the Python API."]);
                exit();
            }

            if ($httpCode !== 200 || !$response) {
                echo json_encode(["status" => "error", "message" => "API Connection Failed (Image): Server returned error code " . $httpCode]);
                exit();
            }

            $responseData = json_decode($response, true);
            if (isset($responseData['status']) && $responseData['status'] === 'success') {
                $prediction = $responseData['result'];
                $confidence = $responseData['confidence'];
                $reason = $responseData['reason'];
                $extracted_text = $responseData['extracted_text'];
                $job_text = $extracted_text; // For DB storage
            } else {
                echo json_encode(["status" => "error", "message" => $responseData['message'] ?? "Image analysis failed"]);
                exit();
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to upload image"]);
            exit();
        }
    } else {
        // Text-based analysis
        $data = json_encode(["text" => $job_text]);
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 0) {
            echo json_encode(["status" => "error", "message" => "API Connection Failed: The detection service is not running. Please start the Python API."]);
            exit();
        }

        if ($httpCode !== 200 || !$response) {
            echo json_encode(["status" => "error", "message" => "API Connection Failed: Server returned error code " . $httpCode]);
            exit();
        }

        $responseData = json_decode($response, true);
        if (is_array($responseData) && isset($responseData['result'])) {
            $prediction = $responseData['result'];
            $confidence = $responseData['confidence'];
            $reason = $responseData['reason'] ?? "AI analysis completed based on job structure and patterns.";
        } else {
            echo json_encode(["status" => "error", "message" => "Detection service returned invalid data"]);
            exit();
        }
    }

    // ---------- SAVE TO DB (Only for logged-in users) ----------
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $username = $_SESSION['user'];

        $stmt = $conn->prepare(
            "INSERT INTO job_history (user_id, username, job_text, result, reason, confidence) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("issssd", $user_id, $username, $job_text, $prediction, $reason, $confidence);
        $stmt->execute();
    }

    echo json_encode([
        "status" => "success",
        "result" => $prediction,
        "confidence" => $confidence,
        "reason" => $reason,
        "extracted_text" => $extracted_text
    ]);
}
?>
