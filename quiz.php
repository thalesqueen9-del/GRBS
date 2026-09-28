<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (!isset($_SESSION['apprenant'])) {
    header('Location: index.php');
    exit;
}

$apprenant = $_SESSION['apprenant'];

$quiz_id = $_GET['id'] ?? 0;
$quiz = get_quiz($quiz_id);
if (!$quiz || $quiz['statut'] !== 'publie') {
    header('Location: accueil.php');
    exit;
}

$questions = get_questions_quiz($quiz_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($questions as $question) {
        $question_id = $question['id'];
        if ($question['type_question'] === 'qcm') {
            $option_id = $_POST['q_' . $question_id] ?? null;
            if ($option_id) {
                enregistrer_reponse($apprenant['id'], $quiz_id, $question_id, null, $option_id);
            }
        }
    }

    $score = calculer_score($apprenant['id'], $quiz_id);
    header('Location: voir_resultats.php?quiz=' . $quiz_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quiz['titre']); ?> - GRBS</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo htmlspecialchars($quiz['titre']); ?></h1>
            <div class="user-info">
                <strong><?php echo htmlspecialchars($apprenant['nom']); ?></strong>
                <br>
                <a href="accueil.php" style="color: #667eea; text-decoration: none; font-size: 12px;">← Retour</a>
            </div>
        </header>

        <div class="card">
            <p><?php echo htmlspecialchars($quiz['description'] ?? ''); ?></p>

            <form method="POST">
                <?php foreach ($questions as $index => $question): ?>
                    <?php $options = get_options_question($question['id']); ?>
                    <div class="question-container">
                        <h4><?php echo ($index + 1) . '. ' . htmlspecialchars($question['enonce']); ?></h4>
                        <div class="options">
                            <?php foreach ($options as $option): ?>
                                <label class="option">
                                    <input type="radio" name="q_<?php echo $question['id']; ?>" value="<?php echo $option['id']; ?>">
                                    <?php echo htmlspecialchars($option['texte']); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <button type="submit">Soumettre le quiz</button>
            </form>
        </div>
    </div>
</body>
</html>
