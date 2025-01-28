<?php
// create_file.php
header('Content-Type: text/plain');

// 获取数据
$data = json_decode(file_get_contents('php://input'), true);
$filename = $data['filename'] ?? null;

if ($filename) {
    // 安全地清理文件名以避免任何可能的路径遍历攻击
    $safeFilename = preg_replace('/[^a-zA-Z0-9\.]/', '_', $filename) . '.txt';
    $filePath = __DIR__ . '/IPS/' . $safeFilename; // 确保文件保存在当前目录

    try {
        $file = fopen($filePath, 'w');
        if ($file) {
            fclose($file);
            echo "File $safeFilename created successfully.";
        } else {
            echo "Failed to create file: Could not open file for writing.";
        }
    } catch (Exception $e) {
        echo "Failed to create file: " . $e->getMessage();
    }
} else {
    echo "No filename provided.";
}
?>



