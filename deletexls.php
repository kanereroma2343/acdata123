<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "excel_data";

// Handle delete action
if (isset($_POST['delete_data'])) {
    try {
        // Create connection
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // SQL to delete all records
        $sql = "DELETE FROM ac_data";
        $conn->exec($sql);
        
        // Simulate a delay for the progress bar (remove this in production)
        sleep(2);

        // Redirect to index.php after successful deletion
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
    } finally {
        // Close connection
        $conn = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Database</title>
    <style>
        :root {
            --primary-color: #3498db;
            --danger-color: #e74c3c;
            --success-color: #2ecc71;
            --text-color: #333;
            --bg-color: #f5f5f5;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #000428, #004e92, #ffffff);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .delete-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 400px;
            width: 100%;
        }

        .warning-heading {
            color: var(--danger-color);
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .warning-text {
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .delete-btn {
            background-color: var(--danger-color);
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        .progress-bar-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .progress-bar-wrapper {
            background-color: #fff;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .progress {
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, #3498db, #2ecc71);
            background-size: 200% 100%;
            animation: gradientMove 2s linear infinite;
            border-radius: 10px;
            transition: width 0.3s ease-in-out;
            position: relative;
            overflow: hidden;
        }

        .progress::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.3) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            animation: shine 1.5s infinite;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 0%; }
            100% { background-position: 200% 0%; }
        }

        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .error-msg {
            color: var(--danger-color);
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="delete-container">
        <h2 class="warning-heading">⚠️ Database Deletion Warning</h2>
        <p class="warning-text">
            Please be aware that clicking the button below will permanently delete <strong>ALL DATA</strong> from the database. 
            This action cannot be undone and will result in the complete removal of all records. 
            Make sure you have a backup if needed before proceeding.
        </p>
        <form method="POST" onsubmit="return confirmDelete()">
            <button class="delete-btn" type="submit" name="delete_data">
                Delete All Data
            </button>
        </form>
        <?php if (isset($error_message)): ?>
            <p class="error-msg"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>

    <div class="progress-bar-container" id="progressBarContainer">
        <div class="progress-bar-wrapper">
            <h3>Deleting Data</h3>
            <div class="progress-bar">
                <div class="progress" id="progress"></div>
            </div>
            <p id="progressText">0%</p>
        </div>
    </div>

    <script>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete all data? This action cannot be undone.')) {
                document.getElementById('progressBarContainer').style.display = 'flex';
                simulateProgress();
                return true;
            }
            return false;
        }

        function simulateProgress() {
            let progress = 0;
            const progressBar = document.getElementById('progress');
            const progressText = document.getElementById('progressText');
            
            const interval = setInterval(() => {
                if (progress < 98) {
                    progress += Math.random() * 8 + 2; // Random increment between 2 and 10
                    progress = Math.min(progress, 98); // Don't exceed 98%
                    progressBar.style.width = `${progress}%`;
                    progressText.textContent = `${Math.round(progress)}%`;
                }
            }, 200);

            // Set to 100% when the page is about to redirect
            setTimeout(() => {
                progress = 100;
                progressBar.style.width = '100%';
                progressText.textContent = '100%';
                clearInterval(interval);
            }, 1900); // Set to slightly less than the PHP sleep duration
        }
    </script>
</body>
</html>