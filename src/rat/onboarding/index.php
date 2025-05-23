<?php
require_once "../../auth-guards.php";
if (auth_required_guard("rat", "/rat/login")) exit;

$pageConfig = [
    "title" => "Onboarding",
    "styles" => ["/rat/styles/auth.css", "./onboarding.css"],
    "scripts" => ["/rat/scripts/forms.js"]
];

require_once "../includes/header.php";

$fname = $_SESSION['auth']['fname'] ?? "Rat";
?>

<main class="onboarding">
    <img width="100" src="./animation1.gif" alt="Man lifting a weight">
    <h1>Hello <?= $fname ?></h1>
    <p class="paragraph">Let us gather some details about you to personalize your fitness journey at GYMRAT. This will help us tailor the best experience for you at our gym.</p>
    <form action="onboarding_process.php" method="post">
        <div class="question">
            <span class="title">What is your gender?</span>
            <div class="gender">
                <label class="input line radio">
                    <input type="radio" name="gender" value="male" checked required>
                    <div class="tick">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                    <span class="option">Male</span>
                </label>
                <label class="input line radio">
                    <input type="radio" name="gender" value="female" required>
                    <div class="tick">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>

                    </div>
                    <span class="option">Female</span>
                </label>
            </div>
        </div>
        <div class="question">
            <span class="title">How old are you?</span>
            <div class="line">
                <input class="input" min="10" max="100" type="number" name="age" placeholder="16" required>
                <span class="">YRS</span>
            </div>
        </div>
        <div class="question">
            <span class="title">What is your weight?</span>
            <div class="line">
                <input class="input" min="30" max="250" type="number" name="weight" required>
                <span class="">KG</span>
            </div>
        </div>
        <div class="question">
            <span class="title">What is your height?</span>
            <div class="line">
                <input class="input" min="120" max="250" type="number" name="height" required>
                <span class="">CM</span>
            </div>
        </div>
        <div class="question">
            <span class="title">What is your goal?</span>
            <select name="goal" class="input">
                <option disabled value="">Select option</option>
                <option value="weight_loss">Weight Loss</option>
                <option value="weight_gain">Weight Gain</option>
                <option value="muscle_mass_gain">Muscle Mass Gain</option>
                <option value="shape_body">Shape Body</option>
                <option value="other">Other</option>
            </select>
            <textarea name="other_goal" class="input" placeholder="Describe your goal briefly"></textarea>
        </div>
        <div class="question">
            <span class="title">Your physical activity level?</span>
            <select name="physical_activity_level" class="input">
                <option disabled value="">Select option</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
        </div>
        <div class="question">
            <span class="title">Your dietary preferences?</span>
            <select name="dietary_preference" class="input">
                <option value="vegitarian">Vegitarian</option>
                <option value="non_vegitarian">Non-vegitarian</option>
                <option value="gluten_free">Gluten - Free</option>
                <option value="keto">Keto</option>
                <option value="paleo">Paleo</option>
                <option value="" selected>No Preferences</option>
            </select>
        </div>
        <div class="question">
            <span class="title">Do you have any allergies?</span>
            <textarea name="allergies" class="input" placeholder="Describe your allergies briefly(if there's any)"></textarea>
        </div>
        <p class="paragraph small">*Please note that the weight and height fields cannot be changed later as they are collected as initial data to personalize your fitness journey.</p>
        <button class="btn">Let's get started</button>
    </form>
</main>
<script>
    const goal = document.querySelector('select[name="goal"]');
    const otherGoal = document.querySelector('textarea[name="other_goal"]');

    otherGoal.style.display = 'none';
    otherGoal.removeAttribute('required');

    goal.addEventListener('change', (e) => {
        if (e.target.value === 'other') {
            otherGoal.style.display = 'block';
            otherGoal.setAttribute('required', 'true');
        } else {
            otherGoal.style.display = 'none';
            otherGoal.removeAttribute('required');
        }
    });
</script>
<?php require_once "../includes/footer.php" ?>