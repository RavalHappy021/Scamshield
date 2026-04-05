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
    $api_url = $api_base_url . "/predict";
    $prediction = "";
    $extracted_text = "";

    // ---------- IMAGE COMPRESSION FUNCTION ----------
    function compressImage($source, $destination, $quality, $max_width = 800) {
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source);
        elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source);
        elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source);
        else return false;

        // Resize if too large
        $width = imagesx($image);
        $height = imagesy($image);
        if ($width > $max_width) {
            $new_width = $max_width;
            $new_height = floor($height * ($max_width / $width));
            $tmp_img = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($tmp_img, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagedestroy($image);
            $image = $tmp_img;
        }

        imagejpeg($image, $destination, $quality);
        imagedestroy($image);
        return $destination;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $api_url = $api_base_url . "/predict-image";
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed = ['jpg', 'jpeg', 'png'];
        if (!in_array($file_ext, $allowed)) {
            echo json_encode(["status" => "error", "message" => "Only JPG, JPEG & PNG are allowed"]);
            exit();
        }

        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $upload_path = $upload_dir . time() . "_" . $file_name;
        if (move_uploaded_file($file_tmp, $upload_path)) {
            // COMPRESS IMAGE before sending to API (Crucial for Render Free Tier Stability)
            $compressed_path = $upload_dir . "comp_" . time() . ".jpg";
            if (compressImage($upload_path, $compressed_path, 75)) {
                $upload_path = $compressed_path; // Use the lighter version
            }

            // Send image to Python API
            $data = [
                'image' => new CURLFile($upload_path)
            ];

            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_USERAGENT, 'ScamShield-Client/1.0');

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 0) {
                $curl_error = curl_error($ch);
                echo json_encode(["status" => "error", "message" => "Image API Connection Failed: " . $curl_error]);
                exit();
            }

            if ($httpCode !== 200 || !$response) {
                // Log raw response for debugging if possible
                $short_response = substr(strip_tags($response), 0, 100);
                echo json_encode(["status" => "error", "message" => "Image API Error (Code $httpCode): " . $short_response]);
                exit();
            }

            $responseData = json_decode($response, true);
            if (is_array($responseData) && $responseData['status'] === 'success') {
                $prediction = $responseData['result'];
                $confidence = $responseData['confidence'];
                $reason = $responseData['reason'] ?? "AI image analysis completed successfully.";
                $extracted_text = $responseData['extracted_text'] ?? "";
            } else {
                $msg = $responseData['message'] ?? "The detection service returned an invalid response format.";
                echo json_encode(["status" => "error", "message" => $msg]);
                exit();
            }
        } else {
            $error_info = error_get_last();
            $msg = "Failed to upload image to server folder.";
            if ($error_info) {
                $msg .= " Details: " . $error_info['message'];
            }
            echo json_encode(["status" => "error", "message" => $msg]);
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
        curl_setopt($ch, CURLOPT_USERAGENT, 'ScamShield-Client/1.0'); // Help bypass some blocks

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 0) {
            $curl_error = curl_error($ch);
            echo json_encode(["status" => "error", "message" => "API Connection Failed: " . $curl_error]);
            exit();
        }

        if ($httpCode !== 200 || !$response) {
            // Log raw response for debugging if possible
            $short_response = substr(strip_tags($response), 0, 100);
            echo json_encode(["status" => "error", "message" => "API Error (Code $httpCode): " . $short_response]);
            exit();
        }

        $responseData = json_decode($response, true);
        if (is_array($responseData) && (($responseData['status'] ?? '') === 'success' || isset($responseData['result']))) {
            $prediction = $responseData['result'];
            $confidence = $responseData['confidence'];
            $reason = $responseData['reason'] ?? "AI analysis completed based on job structure and patterns.";
        } else {
            // Check if the response itself is "Unauthorized"
            if (trim($response) == "Unauthorized") {
                echo json_encode(["status" => "error", "message" => "The API server returned an Unauthorized response. Check Render logs."]);
            } else {
                echo json_encode(["status" => "error", "message" => "The detection service returned an invalid response format."]);
            }
            exit();
        }
    }

    // ---------- SAVE TO DB ----------
    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['user'] ?? 'Guest';

    $stmt = $conn->prepare(
        "INSERT INTO job_history_v2 (user_id, username, job_text, result, reason, confidence) VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("issssd", $user_id, $username, $job_text, $prediction, $reason, $confidence);
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "result" => $prediction,
        "confidence" => $confidence,
        "reason" => $reason,
        "extracted_text" => $extracted_text
    ]);
}
?>
