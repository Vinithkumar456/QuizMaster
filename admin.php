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

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['question'])) {
    $question = $_POST['question'];
    $option1 = $_POST['option1'];
    $option2 = $_POST['option2'];
    $option3 = $_POST['option3'];
    $option4 = $_POST['option4'];
    $correct = $_POST['correct'];

    $sql = "INSERT INTO questions (subject, question, option1, option2, option3, option4, correct) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $subject, $question, $option1, $option2, $option3, $option4, $correct);

    if ($stmt->execute()) {
        $message = 'New question added successfully';
    } else {
        $message = 'Error: ' . $stmt->error;
    }

    $stmt->close();
}

$sql = "SELECT * FROM questions WHERE subject='$subject'";
$result = $conn->query($sql);
$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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
        }
        .logout-button, .view-scores-button {
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
        .view-scores-button {
            right: 100px;
        }
        .logout-button:hover, .view-scores-button:hover {
            background-color: #c82333;
        }
        h1, h2 {
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        form input, form button {
            margin: 10px 0;
            padding: 10px;
            width: 100%;
            max-width: 500px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
        }
        form button {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            font-size: 1em;
        }
        form button:hover {
            background-color: #0056b3;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        ul li {
            background: rgba(255, 255, 255, 0.1);
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        ul li form {
            margin: 0;
        }
        ul li form button {
            background-color: #dc3545;
        }
        ul li form button:hover {
            background-color: #c82333;
        }
        a {
            color: #007BFF;
            text-decoration: none;
            transition: color 0.3s ease-in-out;
        }
        a:hover {
            color: #0056b3;
        }
    </style>
    <script>
        window.onload = function() {
            var message = document.getElementById('message').value;
            if (message) {
                alert(message);
            }
        }
    </script>
</head>
<body>
    <input type="hidden" id="message" value="<?php echo htmlspecialchars($message); ?>">
    <form action="logout.php" method="post">
        <button type="submit" class="logout-button">Logout</button>
    </form>
    <form action="scores.php" method="get">
        <button type="submit" class="view-scores-button">View Scores</button>
    </form>
    <div class="container">
        <h1>Admin Panel for <?php echo ucfirst($subject); ?></h1>
        <form action="admin.php" method="post">
            <input type="text" name="question" placeholder="Question" required>
            <input type="text" name="option1" placeholder="Option 1" required>
            <input type="text" name="option2" placeholder="Option 2" required>
            <input type="text" name="option3" placeholder="Option 3" required>
            <input type="text" name="option4" placeholder="Option 4" required>
            <input type="text" name="correct" placeholder="Correct Option" required>
            <button type="submit">Add Question</button>
        </form>
        <h2>All Questions</h2>
        <ul>
            <?php foreach ($questions as $question): ?>
                <li>
                    <?php echo htmlspecialchars($question['question']); ?>
                    <form action="delete_question.php" method="post" style="display:inline;">
                        <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
