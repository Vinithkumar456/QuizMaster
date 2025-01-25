<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$subject = $_POST['subject'];
$_SESSION['subject'] = $subject;

$sql = "SELECT * FROM questions WHERE subject='$subject'";
$result = $conn->query($sql);

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .btn:hover {
            background-color: #0056b3;
        }

        .btn.selected {
            background-color: #0056b3; /* Change color when selected */
        }

        .btn.option {
            /* Option button specific styles */
        }

        .btn-next {
            background-color: #28a745; /* Green color */
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        .btn-next:hover {
            background-color: #218838; /* Darker green on hover */
        }

    </style>
    <script>
        let questions = <?php echo json_encode($questions); ?>;
        let currentQuestionIndex = 0;
        let score = 0;
        let correctCount = 0;
        let wrongCount = 0;
        let unattemptedCount = 0;
        let selectedAnswer = null;

        function loadQuestion() {
            if (currentQuestionIndex < questions.length) {
                document.getElementById('question').innerText = questions[currentQuestionIndex]['question'];
                document.getElementById('option1').innerText = questions[currentQuestionIndex]['option1'];
                document.getElementById('option2').innerText = questions[currentQuestionIndex]['option2'];
                document.getElementById('option3').innerText = questions[currentQuestionIndex]['option3'];
                document.getElementById('option4').innerText = questions[currentQuestionIndex]['option4'];
                selectedAnswer = null;

                // Reset button styles
                document.querySelectorAll('.btn.option').forEach(btn => btn.classList.remove('selected'));
            } else {
                unattemptedCount += (questions.length - currentQuestionIndex);

                document.getElementById('quiz').innerHTML = `<h1>Your Score: ${score}</h1>`;
                document.getElementById('quiz').innerHTML += `<h1>Correct Answers: ${correctCount}</h1>`;
                document.getElementById('quiz').innerHTML += `<h1>Wrong Answers: ${wrongCount}</h1>`;
                document.getElementById('quiz').innerHTML += `<h1>Unattempted Questions: ${unattemptedCount}</h1>`;
                document.getElementById('quiz').innerHTML += `<form action='leaderboard.php?subject=<?php echo $subject; ?>' method="post"><button class="btn">View Leaderboard</button></form>`;
                
                // Update user's score in the database
                fetch('update_score.php', {
                    method: 'POST',
                    body: JSON.stringify({ score: score, subject: '<?php echo $subject; ?>' }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        console.log('Score updated successfully');
                    } else {
                        console.error('Failed to update score:', data.message);
                    }
                }).catch(error => console.error('Error:', error));

                // Reset the score and counts for the next attempt
                score = 0;
                correctCount = 0;
                wrongCount = 0;
                unattemptedCount = 0;
            }
        }

        function selectAnswer(option) {
            selectedAnswer = option;

            // Update button styles
            document.querySelectorAll('.btn.option').forEach(btn => btn.classList.remove('selected'));
            document.getElementById(option).classList.add('selected');
        }

        function nextQuestion() {
            if (selectedAnswer) {
                if (questions[currentQuestionIndex]['correct'] === selectedAnswer) {
                    score++;
                    correctCount++;
                } else {
                    wrongCount++;
                }
            } else {
                unattemptedCount++;
            }

            currentQuestionIndex++;
            loadQuestion();
        }

        window.onload = loadQuestion;
    </script>
</head>
<body>
    <div class="container" id="quiz">
        <h1 id="question"></h1>
        <button onclick="selectAnswer('option1')" id="option1" class="btn option"></button>
        <button onclick="selectAnswer('option2')" id="option2" class="btn option"></button>
        <button onclick="selectAnswer('option3')" id="option3" class="btn option"></button>
        <button onclick="selectAnswer('option4')" id="option4" class="btn option"></button>
        <button onclick="nextQuestion()" class="btn btn-next">Next Question</button>
    </div>
</body>
</html>
