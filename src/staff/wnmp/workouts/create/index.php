<?php
session_start();

require_once "../../../../auth-guards.php";
auth_required_guard("wnmp", "/staff/login");

require_once "../../../../alerts/functions.php";
require_once "../../../../db/models/Workout.php";
require_once "../../../../db/models/Exercise.php";

$workout = new Workout();
if (isset($_SESSION['workout'])) {
    $workout = unserialize($_SESSION['workout']);
} else {
    $workout->fill([]);
    $_SESSION['workout'] = serialize($workout);
}

if (!isset($_SESSION['exerciseTitles'])) {
    $exerciseModel = new Exercise();
    $exerciseTitles = $exerciseModel->get_all_titles();
    $_SESSION['exerciseTitles'] = $exerciseTitles;
} else {
    $exerciseTitles = $_SESSION['exerciseTitles'];
}


$sidebarActive = 3;
$menuBarConfig = [
    "title" => "Create Workout",
    "showBack" => true,
    "goBackTo" => "/staff/wnmp/workouts/index.php",
    "useButton" => true,
    "options" => [
        [
            "title" => "Save Changes",
            "buttonType" => "submit",
            "buttonName" => "action",
            "buttonValue" => "create",
            "type" => "secondary"
        ],
        ["title" => "Revert Changes", "buttonType" => "submit", "formAction" => "revert_workout.php", "type" => "destructive"]
    ]
];


require_once "../../pageconfig.php";

$pageConfig['styles'][] = "../workouts.css";

require_once "../../../includes/header.php";
require_once "../../../includes/sidebar.php";
?>

<main>
    <div class="base-container">
        <div class="form">
            <form action="create_workout.php" method="POST">
                <?php require_once "../../../includes/menubar.php"; ?>
                <div style="padding: 5px 10px;">
                    <input type="hidden" name="workout_id" value="<?= $workout->id ?>">
                    <h2><label for="edit-title">Title</label></h2>
                    <input type="text" id="edit-title" name="workout_name"
                        class="staff-input-primary staff-input-long" value="<?= $workout->name ?>"
                        placeholder="Enter workout title">
                    <h2 style="padding-top: 5px;"><label for="edit-description">Description</label></h2>
                    <textarea id="edit-description" name="workout_description"
                        class="staff-textarea-primary staff-textarea-large"
                        placeholder="Enter a workout description"><?= $workout->description ?></textarea>
                    <div>
                        <h2><label for="edit-duration">Duration (Days)</label></h2>
                        <input type="text" id="edit-duration" name="workout_duration" pattern="\d+"
                            class="staff-input-primary staff-input-long" value="<?= $workout->duration ?>">
                    </div>
                </div>
                <div style="padding: 5px 10px;">
                    <h2>Exercise</h2>
                    <?php foreach ($workout->exercises as $exercise): ?>
                        <div class="edit-workout-row">
                            <?php if (!$exercise['isDeleted']): ?>
                                <select name="exercise_title_<?= $exercise['edit_id'] ?>" class="staff-input-primary staff-input-long">
                                    <?php foreach ($exerciseTitles as $title): ?>
                                        <option value="<?= $title ?>" <?= $title == $exercise['title'] ? 'selected' : '' ?>>
                                            <?= $title ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="edit-workout-input-reps-sets">
                                    <label for="exercise_day_<?= $exercise['edit_id'] ?>">Day</label>
                                    <input type="text" name="exercise_day_<?= $exercise['edit_id'] ?>" pattern="[1-7]" min="1" max="7"
                                        value="<?= $exercise['day'] ?>" class="staff-input-primary staff-input-short">
                                </div>
                                <div class="edit-workout-input-reps-sets">
                                    <label for="exercise_sets_<?= $exercise['edit_id'] ?>">Sets</label>
                                    <input type="text" name="exercise_sets_<?= $exercise['edit_id'] ?>" pattern="\d+"
                                        value="<?= $exercise['sets'] ?>" class="staff-input-primary staff-input-short">
                                </div>
                                <div class="edit-workout-input-reps-sets">
                                    <label for="exercise_reps_<?= $exercise['edit_id'] ?>">Reps</label>
                                    <input type="text" name="exercise_reps_<?= $exercise['edit_id'] ?>" pattern="\d+"
                                        value="<?= $exercise['reps'] ?>" class="staff-input-primary staff-input-short">
                                </div>
                                <button type="submit" class="staff-btn-outline edit-workout-input-delete"
                                    name="delete_exercise" value="<?= $exercise['edit_id'] ?>">
                                    <!-- Delete -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f05050" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2-icon lucide-trash-2">
                                        <path d="M3 6h18" />
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                        <line x1="10" x2="10" y1="11" y2="17" />
                                        <line x1="14" x2="14" y1="11" y2="17" />
                                    </svg>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" name="action" value="add" class="staff-btn-secondary-black edit-workout-add-exercise">
                        + Add Exercise
                    </button>
            </form>
        </div>
    </div>
    </div>
</main>


<?php require_once "../../../includes/footer.php"; ?>