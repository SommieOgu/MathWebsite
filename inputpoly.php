<?php
// Initialize variables
$showAlert = false;
$showError = false;
$polynomialsArray = [];

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the polynomial input from the user
    $polynomialInput = $_POST["polynomials"];

    // Remove unnecessary whitespace from the input
    $polynomialInput = trim($polynomialInput);

    // Check if parentheses are balanced
    if (substr_count($polynomialInput, '(') !== substr_count($polynomialInput, ')')) {
        $showError = "The polynomial expression contains unbalanced parentheses.";
    } else {
        // Split the input by parentheses to handle multiple expressions
        preg_match_all('/\((.*?)\)/', $polynomialInput, $matches);

        // If there are expressions found inside parentheses, store them
        if (count($matches[1]) > 0) {
            // For each expression, split it into terms
            $processedPolynomials = [];
            for ($i = 0; $i < count($matches[1]); $i++) {
                $expression = $matches[1][$i];
                // Split by + to get individual terms, but be careful with negative terms
                $terms = preg_split('/(?<!\-)\+/', $expression);

                // Clean up each term
                $cleanedTerms = [];
                foreach ($terms as $term) {
                    $term = trim($term);
                    // Replace multiple spaces with a single space
                    $term = preg_replace('/\s+/', ' ', $term);
                    if (!empty($term)) {
                        $cleanedTerms[] = $term;
                    }
                }

                // Add the array of terms for this expression
                if (count($cleanedTerms) > 0) {
                    $processedPolynomials[] = $cleanedTerms;
                }
            }

            // If we have processed polynomials, store them
            if (count($processedPolynomials) > 0) {
                $polynomialsArray = $processedPolynomials;
                $showAlert = true;  // Success message
            } else {
                $showError = "Could not process the polynomial expressions properly.";
            }
        } else {
            $showError = "Please enter valid polynomial expressions within parentheses.";
        }
    }

    // Return JSON response for AJAX POST requests
    header('Content-Type: application/json');
    echo json_encode([
        'showAlert' => $showAlert,
        'showError' => $showError ? $showError : false,
        'polynomials' => $polynomialsArray
    ]);
    exit;
}

// For GET requests, return the form with all styling
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math.co</title>
    <style>
        /* Custom Styles */
        body {
            background: white;
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            background: #fff;
            padding: 20px;
            width: 50%;
            margin: 50px auto;
            border-radius: 10px;
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.1);
        }

        .container input {
            padding: 10px;
            font-size: 18px;
            margin: 10px 0;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .container button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 18px;
        }

        .container button:hover {
            background-color: #45a049;
        }

        .alert {
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
        }

        .error {
            background-color: #f44336;
        }

        button {
            background-color: #1f1f1f;
            color: #ffffff;
            border: 1px solid #444;
            border-radius: 6px;
            padding: 10px 16px;
            margin: 5px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        button:hover {
            background-color: #292929;
            transform: scale(1.05);
            box-shadow: 0 0 8px rgba(0, 255, 170, 0.3);
        }

    </style>
   
</head>
<body>
    <div class="container" style="margin: 0; width: auto; box-shadow: none;">
        <h2>Factor Polynomials</h2>
        <form id="polynomialForm" method="POST">
            <label for="polynomials">Enter polynomials (e.g., (x^4 + 3)(x^2 + x^4 + x^7)(x^2)):</label>
            <input type="text" id="polynomials" name="polynomials" required><br><br>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>

