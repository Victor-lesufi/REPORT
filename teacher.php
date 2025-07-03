<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Student Marks</title>
    <link rel="stylesheet" href="CSS/teacher.css">  
    
    
</head>
<body>
<a href="dashboard.php">dashboard</a>
<a href="details.php">view details</a>
    <h2>Enter Student Marks for term1</h2>
<form id="marksForm" method="POST" action="submit_marks.php">
    <div class="success"></div>
    <label for="term">Select Term:</label>
    <select id="term" name="term" required>
        <option value="" disabled selected>-- Select a Term --</option>
        <option value="term1">Term 1</option>
        <option value="term2">Term 2</option>
        <option value="term3">Term 3</option>
    </select>

    <!-- Input for Student Name -->
    <label for="student_name">Student Name:</label>
    <input type="text" id="student_name" name="student_name" placeholder="Enter student's name" required>

    <!-- Dropdown for Subjects -->
    <label for="subject">Select Subject:</label>
    <select id="subject" name="subject" required>
        <option value="" disabled selected>-- Select a Subject --</option>
        <option value="english">English</option>
        <option value="sepedi">Sepedi</option>
        <option value="mathematics">Mathematics</option>
        <option value="life_science">Life Science</option>
        <option value="physical_science">Physical Science</option>
        <option value="geography">Geography</option>
        <option value="life_orientation">Life Orientation</option>
    </select>
    <div id="subjectError" class="error"></div>

    <!-- Input for Marks -->
    <label for="marks">Enter Marks (0-100):</label>
    <input type="number" id="marks" name="marks" min="0" max="100" step="0.01" required>
    <div id="marksError" class="error"></div>

    <!-- Dropdown for Term -->
    
    <div id="termError" class="error"></div>

    <!-- Submit Button -->
    <button type="submit">Submit Marks</button>
</form>


    <script>
        // Client-side validation
        document.getElementById('marksForm').addEventListener('submit', function(event) {
            let isValid = true;

            // Clear previous error messages
            document.getElementById('subjectError').textContent = '';
            document.getElementById('marksError').textContent = '';

            // Validate Subject
            const subject = document.getElementById('subject').value;
            if (!subject) {
                document.getElementById('subjectError').textContent = 'Please select a subject.';
                isValid = false;
            }

            // Validate Marks
            const marks = document.getElementById('marks').value;
            if (marks === '' || marks < 0 || marks > 100) {
                document.getElementById('marksError').textContent = 'Marks must be between 0 and 100.';
                isValid = false;
            }

            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>