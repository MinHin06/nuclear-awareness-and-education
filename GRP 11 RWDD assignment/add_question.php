<?php
include("conn.php");

// Get competition ID from URL
$competitionID = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch competition type from DB
$compRes = mysqli_query($dbConn, "SELECT CompetitionType FROM competition WHERE CompetitionID='$competitionID'");
$compRow = mysqli_fetch_assoc($compRes);
$competitionType = $compRow['CompetitionType'] ?? '';

if (isset($_POST['save_questions'])) {
    $totalQuestions = intval($_POST['totalQuestions']);

    for ($i = 1; $i <= $totalQuestions; $i++) {
        $questionText = mysqli_real_escape_string($dbConn, $_POST["questionText$i"]);
        $questionType = $_POST["questionType$i"];
        $createdDate = date('Y-m-d H:i:s');

        // Default values (IMPORTANT)
        $option1 = $option2 = $option3 = $option4 = '';
        $correctAnswer = '';
        $answerKey = '';

        // MCQ data
        if ($questionType === 'MCQ' || $questionType === 'Both') {
            $option1 = mysqli_real_escape_string($dbConn, $_POST["q{$i}_option1"] ?? '');
            $option2 = mysqli_real_escape_string($dbConn, $_POST["q{$i}_option2"] ?? '');
            $option3 = mysqli_real_escape_string($dbConn, $_POST["q{$i}_option3"] ?? '');
            $option4 = mysqli_real_escape_string($dbConn, $_POST["q{$i}_option4"] ?? '');
            $correctAnswer = $_POST["q{$i}_correct"] ?? '';
        }

        // Structured data
        if ($questionType === 'Structured' || $questionType === 'Both') {
            $answerKey = mysqli_real_escape_string($dbConn, $_POST["q{$i}_structured"] ?? '');
        }

        // BOTH → insert TWO rows
        if ($questionType === "Both") {

            // MCQ row
            mysqli_query($dbConn, "
                INSERT INTO questions
                (CompetitionID, CompetitionType, QuestionText, QuestionType,
                 Option1, Option2, Option3, Option4, CorrectAnswer, AnswerKey, CreatedDate)
                VALUES
                ('$competitionID', '$competitionType', '$questionText', 'MCQ',
                 '$option1', '$option2', '$option3', '$option4', '$correctAnswer', '', '$createdDate')
            ");

            // Structured row
            mysqli_query($dbConn, "
                INSERT INTO questions
                (CompetitionID, CompetitionType, QuestionText, QuestionType,
                 Option1, Option2, Option3, Option4, CorrectAnswer, AnswerKey, CreatedDate)
                VALUES
                ('$competitionID', '$competitionType', '$questionText', 'Structured',
                 '', '', '', '', '', '$answerKey', '$createdDate')
            ");

        } else {
            // Single insert (MCQ or Structured)
            mysqli_query($dbConn, "
                INSERT INTO questions
                (CompetitionID, CompetitionType, QuestionText, QuestionType,
                 Option1, Option2, Option3, Option4, CorrectAnswer, AnswerKey, CreatedDate)
                VALUES
                ('$competitionID', '$competitionType', '$questionText', '$questionType',
                 '$option1', '$option2', '$option3', '$option4', '$correctAnswer', '$answerKey', '$createdDate')
            ");
        }
    }

    echo "<p style='color:green'>All questions saved successfully!</p>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Questions</title>
<link rel="stylesheet" href="create_competition.css">
<style>
.container { max-width: 800px; margin: auto; padding: 20px; }
.question-card { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 8px; }
.mcq-options, .structured-answer { margin-top: 10px; }
button { padding: 8px 15px; margin-top: 10px; cursor: pointer; }
</style>
</head>
<body>
<div class="container">
<h2>Add Questions</h2>

<form method="post" id="questionsForm">
    <div id="questionContainer"></div>

    <input type="hidden" name="totalQuestions" id="totalQuestions" value="0">

    <button type="button" id="addQuestionBtn">+ Add Question</button>
    <button type="submit" name="save_questions">Save All Questions</button>
</form>
</div>

<script>
let questionCount = 0;

function createQuestionCard() {
    questionCount++;
    document.getElementById('totalQuestions').value = questionCount;

    const container = document.getElementById('questionContainer');
    const card = document.createElement('div');
    card.classList.add('question-card');
    card.innerHTML = `
        <h4>Question ${questionCount}</h4>
        <label>Question Text:</label>
        <textarea name="questionText${questionCount}" placeholder="Enter question here..." required></textarea><br>

        <label>Question Type:</label>
        <select name="questionType${questionCount}" class="question-type" data-id="${questionCount}" required>
            <option value="">-- Select Type --</option>
            <option value="MCQ">MCQ</option>
            <option value="Structured">Structured</option>
            <option value="Both">Competition (Both)</option>
        </select>

        <div class="mcq-options" id="mcq-${questionCount}" style="display:none;">
            <label>Option 1: <input type="text" name="q${questionCount}_option1"></label>
            <label>Option 2: <input type="text" name="q${questionCount}_option2"></label>
            <label>Option 3: <input type="text" name="q${questionCount}_option3"></label>
            <label>Option 4: <input type="text" name="q${questionCount}_option4"></label>
            <label>Correct Answer:
                <select name="q${questionCount}_correct">
                    <option value="Option1">Option 1</option>
                    <option value="Option2">Option 2</option>
                    <option value="Option3">Option 3</option>
                    <option value="Option4">Option 4</option>
                </select>
            </label>
        </div>

        <div class="structured-answer" id="structured-${questionCount}" style="display:none;">
            <label>Answer / Description:</label>
            <textarea name="q${questionCount}_structured" placeholder="Enter answer here..."></textarea>
        </div>
    `;
    container.appendChild(card);

    // Show/hide fields based on type
    card.querySelector(".question-type").addEventListener("change", function() {
        const id = this.dataset.id;
        const mcq = document.getElementById(`mcq-${id}`);
        const structured = document.getElementById(`structured-${id}`);

        if(this.value === "MCQ") {
            mcq.style.display = "block";
            structured.style.display = "none";
        } else if(this.value === "Structured") {
            mcq.style.display = "none";
            structured.style.display = "block";
        } else if(this.value === "Both") {
            mcq.style.display = "block";
            structured.style.display = "block";
        } else {
            mcq.style.display = "none";
            structured.style.display = "none";
        }
    });
}

// Initial question
createQuestionCard();

// Add new question button
document.getElementById('addQuestionBtn').addEventListener('click', createQuestionCard);
</script>
</body>
</html>


