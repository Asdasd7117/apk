<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['project_zip'])) {
    $uploadDir = __DIR__ . '/uploads/';
    $zipFile = $uploadDir . basename($_FILES['project_zip']['name']);
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    if (move_uploaded_file($_FILES['project_zip']['tmp_name'], $zipFile)) {
        $extractDir = $uploadDir . pathinfo($zipFile, PATHINFO_FILENAME);
        mkdir($extractDir, 0777, true);
        
        $zip = new ZipArchive;
        if ($zip->open($zipFile) === TRUE) {
            $zip->extractTo($extractDir);
            $zip->close();
            
            // تنفيذ Gradle لبناء APK
            $gradleCmd = "cd $extractDir && ./gradlew assembleDebug";
            exec($gradleCmd, $output, $returnVar);
            
            if ($returnVar === 0) {
                $apkPath = glob("$extractDir/app/build/outputs/apk/debug/*.apk")[0] ?? null;
                if ($apkPath && file_exists($apkPath)) {
                    echo json_encode(["status" => "success", "apk_url" => $apkPath]);
                } else {
                    echo json_encode(["status" => "error", "message" => "APK file not found"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Build failed", "details" => implode("\n", $output)]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to extract zip"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "File upload failed"]);
    }
} else {
    echo "<html>
            <head>
                <title>Upload Project</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
                    form { background: #f4f4f4; padding: 20px; border-radius: 10px; display: inline-block; }
                    input, button { margin-top: 10px; }
                </style>
            </head>
            <body>
                <h2>Upload Your Android Project (ZIP)</h2>
                <form method='post' enctype='multipart/form-data'>
                    <input type='file' name='project_zip' required>
                    <br>
                    <button type='submit'>Upload & Build</button>
                </form>
            </body>
          </html>";
}
