<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Excel File</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #000000, #000066, #333333);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 2rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            width: 100%;
            padding: 1rem 0;
        }

        .header h1 {
            font-size: 2.5rem;
            margin: 0;
            padding: 0;
        }

        .upload-form {
            max-width: 500px;
            width: 90%;
            padding: 2rem;
            background-color: rgba(25, 25, 25, 0.95);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            margin: 0 auto;
        }

        h2 {
            color: #ffffff;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input[type="file"] {
            margin-bottom: 1rem;
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 4px;
            width: 100%;
            max-width: 300px;
        }

        .submit-btn {
            background-color: #0033cc;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        .submit-btn:hover {
            background-color: #0066ff;
        }

        .progress-container {
            width: 100%;
            max-width: 300px;
            margin: 1rem auto;
            display: none;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress {
            width: 0%;
            height: 100%;
            background-color: #0033cc;
            transition: width 0.3s ease;
        }

        /* Style for success message */
        p[style*="color: green"] {
            color: #00ff00 !important;
        }

        /* Style for error message */
        p[style*="color: red"] {
            color: #ff3333 !important;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Upload Assessment Center Monitoring System</h1>
    </div>
    <div class="upload-form">
        <h2>Upload Excel File</h2>
        <form method="POST" enctype="multipart/form-data" id="uploadForm">
            <input type="file" name="excel_file" accept=".xlsx,.xls" required>
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress"></div>
                </div>
            </div>
            <input type="submit" value="Upload" class="submit-btn">
        </form>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            require '../vendor/autoload.php'; // Make sure you have PhpSpreadsheet installed
            
            try {
                // Database connection
                $conn = new PDO("mysql:host=localhost;dbname=excel_data", "root", "");
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                if (isset($_FILES["excel_file"])) {
                    $file = $_FILES["excel_file"]["tmp_name"];
                    
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();
                    
                    // Skip header row if exists
                    array_shift($rows);
                    
                    foreach ($rows as $row) {
                        // Convert Excel date numbers to formatted dates
                        $date_accredited = null;
                        $valid_until = null;
                        
                        if (!empty($row[11])) {
                            if (is_numeric($row[11])) {
                                // Handle Excel numeric date
                                $unix_date = ($row[11] - 25569) * 86400;
                                $date_accredited = date('Y-m-d', $unix_date);
                            } else {
                                // Try to parse string date
                                $parsed_date = date_create_from_format('m/d/Y', $row[11]);
                                if ($parsed_date) {
                                    $date_accredited = $parsed_date->format('Y-m-d');
                                }
                            }
                        }
                        
                        if (!empty($row[12])) {
                            if (is_numeric($row[12])) {
                                // Handle Excel numeric date
                                $unix_date = ($row[12] - 25569) * 86400;
                                $valid_until = date('Y-m-d', $unix_date);
                            } else {
                                // Try to parse string date
                                $parsed_date = date_create_from_format('m/d/Y', $row[12]);
                                if ($parsed_date) {
                                    $valid_until = $parsed_date->format('Y-m-d');
                                }
                            }
                        }
                        
                        $sql = "INSERT INTO ac_data (region, province, assessment_center, address, longitude, 
                                latitude, center_manager, tel_no, sector, qualification_title, 
                                accreditation_number, date_accredited, valid_until) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([
                            $row[0], $row[1], $row[2], $row[3], $row[4], 
                            $row[5], $row[6], $row[7], $row[8], $row[9], 
                            $row[10], $date_accredited, $valid_until
                        ]);
                    }
                    
                    echo "<p style='color: green; text-align: center;'>Data uploaded successfully!</p>";
                    echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 2000);</script>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red; text-align: center;'>Error: " . $e->getMessage() . "</p>";
            }
        }
        ?>
    </div>

    <script>
    document.getElementById('uploadForm').addEventListener('submit', function() {
        const progressContainer = document.querySelector('.progress-container');
        const progress = document.querySelector('.progress');
        let width = 0;

        progressContainer.style.display = 'block';
        
        const interval = setInterval(function() {
            if (width >= 100) {
                clearInterval(interval);
            } else {
                width++;
                progress.style.width = width + '%';
            }
        }, 50);
    });
    </script>
</body>
</html>