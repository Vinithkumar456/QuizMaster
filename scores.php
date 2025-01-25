<?php
include 'db.php';

session_start();

$adminSubjects = ['admin_math' => 'math', 'admin_science' => 'science', 'admin_history' => 'history'];
$currentAdmin = $_SESSION['username'];

if (!isset($adminSubjects[$currentAdmin])) {
    header("Location: index.html");
    exit();
}

$subject = $adminSubjects[$currentAdmin];
$subjectScoreColumn = 'score_' . $subject;

// Exclude admin usernames from the scores query
$adminUsernames = implode("','", array_keys($adminSubjects));
$sql = "SELECT username, $subjectScoreColumn as score FROM users WHERE $subjectScoreColumn IS NOT NULL AND username NOT IN ('$adminUsernames')";
$scoresResult = $conn->query($sql);

// Check if there are results
if ($scoresResult) {
    $scores = $scoresResult->fetch_all(MYSQLI_ASSOC);
} else {
    $scores = []; // If no results, initialize as empty array
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scores</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background: url('image.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: white;
        }
        .logo {
            width: 100px;
            margin-bottom: 20px;
            margin-top: 30px;
        }
        .container {
            background: linear-gradient(to bottom right, #657786, #194079);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 800px;
            text-align: center;
            margin-top: 50px;
            position: relative; /* Ensure positioning context */
        }
        h1, h2 {
            margin-bottom: 20px;
        }
        .scores-table {
            margin-top: 20px;
            max-width: 800px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 20px;
            overflow-x: auto; /* Add this to allow horizontal scrolling if needed */
        }
        .scores-table table {
            width: 100%;
            border-collapse: collapse;
            
        }
        .scores-table th, .scores-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .scores-table th {
            background-color: #007BFF;
            color: white;
        }
        .top-buttons {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        .logout-button, .admin-panel-button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
            font-size: 0.8em;
        }
        .logout-button:hover, .admin-panel-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
<div class="top-buttons">
            <form action="admin.php" method="get">
                <button type="submit" class="admin-panel-button">Admin Panel</button>
            </form>
            <form action="logout.php" method="post">
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </div>
    <div class="container">
        
        <h1>Scores for <?php echo ucfirst($subject); ?></h1>
        <div class="scores-table">
            <?php if (!empty($scores)): ?>
                <table>
                    <tr>
                        <th>Username</th>
                        <th>Score</th>
                    </tr>
                    <?php foreach ($scores as $score): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($score['username']); ?></td>
                            <td><?php echo htmlspecialchars($score['score']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No scores available.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
