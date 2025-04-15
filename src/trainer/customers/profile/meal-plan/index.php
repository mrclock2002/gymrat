<?php
// File path: src/trainer/customers/profile/meal-plan/index.php

$pageConfig = [
    "title" => "Customer Meal Plan",
    "styles" => [
        "./meal-plan.css" // CSS file
    ],
    "navbar_active" => 1,
    "titlebar" => [
        "back_url" => "../?id=" . (isset($_GET['id']) ? $_GET['id'] : ''),
        "title" => "MEAL PLAN"
    ],
    "need_auth" => true
];

require_once "../../../includes/header.php";
require_once "../../../includes/titlebar.php";
require_once "../../../../db/models/Customer.php";
require_once "../../../../db/models/MealPlan.php";
require_once "../../../../db/models/Meal.php";
require_once "../../../../alerts/functions.php";

// Get customer ID from URL
$customerId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// If customer ID is missing, redirect back to customers list
if (!$customerId) {
    redirect_with_error_alert("Invalid customer ID", "../../");
    exit;
}

// Get database connection for direct queries
$conn = Database::get_conn();

// Get customer details with their meal plan ID
$sql = "SELECT id, fname, lname, email, meal_plan FROM customers WHERE id = :customer_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':customer_id', $customerId);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    redirect_with_error_alert("Customer not found", "../../");
    exit;
}

// Get the meal plan ID from the customer record
$mealPlanId = $customer['meal_plan'];

// Default values
$mealPlanName = "No meal plan assigned";
$mealPlanDescription = "This customer does not have a meal plan assigned yet.";
$mealPlanDuration = 0;
$meals = [];

// If we have a meal plan ID, load the meal plan details
if ($mealPlanId) {
    try {
        // Query the meal plan details
        $sql = "SELECT * FROM mealplans WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $mealPlanId);
        $stmt->execute();
        $mealPlan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($mealPlan) {
            $mealPlanName = $mealPlan['name'];
            $mealPlanDescription = $mealPlan['description'];
            $mealPlanDuration = $mealPlan['duration'];

            // Query to get all meals in this meal plan
            $sql = "SELECT m.*, mm.day, mm.time, mm.amount 
                    FROM meals m 
                    JOIN mealplan_meals mm ON m.id = mm.meal_id 
                    WHERE mm.mealplan_id = :mealplan_id
                    ORDER BY 
                        CASE 
                            WHEN mm.day = 'Monday' THEN 1
                            WHEN mm.day = 'Tuesday' THEN 2
                            WHEN mm.day = 'Wednesday' THEN 3
                            WHEN mm.day = 'Thursday' THEN 4
                            WHEN mm.day = 'Friday' THEN 5
                            WHEN mm.day = 'Saturday' THEN 6
                            WHEN mm.day = 'Sunday' THEN 7
                            ELSE 8
                        END,
                        CASE 
                            WHEN mm.time = 'Breakfast' THEN 1
                            WHEN mm.time = 'Lunch' THEN 2
                            WHEN mm.time = 'Dinner' THEN 3
                            ELSE 4
                        END";

            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':mealplan_id', $mealPlanId);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $meals[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'calories' => $row['calories'],
                    'proteins' => $row['proteins'],
                    'fats' => $row['fats'],
                    'day' => $row['day'] ?? 'Any day',
                    'time' => $row['time'] ?? 'Any time',
                    'amount' => $row['amount'] ?? 1
                ];
            }
        }
    } catch (Exception $e) {
        // If there's an error, we'll show the default "no meal plan" message
    }
}

// Debug info
$debugInfo = [
    'Customer ID' => $customerId,
    'Customer Name' => $customer['fname'] . ' ' . $customer['lname'],
    'Customer Email' => $customer['email'],
    'Meal Plan ID' => $mealPlanId,
    'Meal Plan Name' => $mealPlanName,
    'Number of Meals' => count($meals)
];
?>

<main class="meal-plan-page">
    <!-- Meal Plan Header Section -->
    <div class="meal-plan-header">
        <h2><?= htmlspecialchars($mealPlanName) ?></h2>
        <p class="meal-plan-description"><?= htmlspecialchars($mealPlanDescription) ?></p>
        <?php if ($mealPlanDuration > 0): ?>
            <div class="meal-plan-duration">
                <span class="label">Duration:</span>
                <span class="value"><?= $mealPlanDuration ?> days</span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Meals Section -->
    <?php if (empty($meals)): ?>
        <div class="no-meals-message">
            <p>No meals found in this meal plan.</p>
        </div>
    <?php else: ?>
        <!-- Group meals by day -->
        <?php
        $mealsByDay = [];
        foreach ($meals as $meal) {
            $day = $meal['day'];
            if (!isset($mealsByDay[$day])) {
                $mealsByDay[$day] = [];
            }
            $mealsByDay[$day][] = $meal;
        }
        ?>

        <div class="meal-days">
            <?php foreach ($mealsByDay as $day => $dayMeals): ?>
                <div class="meal-day">
                    <h3 class="day-heading"><?= htmlspecialchars($day) ?></h3>

                    <div class="meals-list">
                        <?php foreach ($dayMeals as $meal): ?>
                            <div class="meal-card">
                                <div class="meal-header">
                                    <h4 class="meal-name"><?= htmlspecialchars($meal['name']) ?></h4>
                                    <span class="meal-time"><?= htmlspecialchars($meal['time']) ?></span>
                                </div>

                                <p class="meal-description"><?= htmlspecialchars($meal['description']) ?></p>

                                <div class="meal-nutrition">
                                    <div class="nutrition-item">
                                        <span class="label">Calories:</span>
                                        <span class="value"><?= $meal['calories'] ?></span>
                                    </div>
                                    <div class="nutrition-item">
                                        <span class="label">Protein:</span>
                                        <span class="value"><?= $meal['proteins'] ?>g</span>
                                    </div>
                                    <div class="nutrition-item">
                                        <span class="label">Fats:</span>
                                        <span class="value"><?= $meal['fats'] ?>g</span>
                                    </div>
                                    <?php if ($meal['amount'] > 1): ?>
                                        <div class="nutrition-item quantity">
                                            <span class="label">Quantity:</span>
                                            <span class="value"><?= $meal['amount'] ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Action Button -->
    <div class="action-buttons">
        <a href="#" class="request-edit-btn">Request To Edit</a>
    </div>

    <?php if (isset($_GET['debug']) && $_GET['debug'] === '1'): ?>
        <div class="debug-section">
            <h3>Debug Information</h3>
            <pre><?php print_r($debugInfo); ?></pre>
        </div>
    <?php endif; ?>
</main>

<?php require_once "../../../includes/navbar.php" ?>
<?php require_once "../../../includes/footer.php" ?>