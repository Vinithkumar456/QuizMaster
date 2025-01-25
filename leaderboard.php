<?php
include 'db.php';

$subject = $_GET['subject'];
$score_column = "score_" . $subject;

$sql = "SELECT username, $score_column as score FROM users WHERE username NOT LIKE 'admin_%' ORDER BY $score_column DESC LIMIT 5";
$result = $conn->query($sql);

$leaderboard = [];
while ($row = $result->fetch_assoc()) {
    $leaderboard[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .logout-button {
        position: fixed;
        top: 20px;
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease-in-out;
        font-size: 0.8em;
        width: auto;
        }
        .logout-button {
            right: 20px;
        }
        .logout-button:hover {
            background-color: #c82333;
        }
        .container {
                    width: 50%;
                    margin: auto;
                    text-align: center;
                    color: white; /* Added to make text inside container white */
                }
                .score{
                    margin-top: 20px;
                    max-width: 800px;
                    background: rgba(0, 0, 0, 0.7);
                    border-radius: 10px;
                    padding: 20px;
                    overflow-x: auto;
                }
                .score table {
                    background-color: black;
                    width: 100%;
                    border-collapse: collapse;
                }
                .score th,.score td {
                    padding: 10px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                .score th {
                    background-color: #007BFF;
                    color: white;
                }
    </style>
</head>
<body>
<form action="logout.php" method="post">
    <button type="submit" class="logout-button">Logout</button>
</form>
    <div class="container">
        <h1>Leaderboard for <?php echo ucfirst($subject); ?></h1>
        <div class="score">
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Score</th>
                </tr>
            </thead>
            
            <tbody>
                <?php foreach ($leaderboard as $entry): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($entry['username']); ?></td>
                        <td><?php echo htmlspecialchars($entry['score']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <form action="subjects.html" method="post">
            <button>Back to subjects</button>
        </form>
    </div>
</body>
</html>