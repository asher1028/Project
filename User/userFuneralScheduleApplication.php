<?php
include 'userSessionStart.php';
include '../config/connection.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and get the input values
    $deceasedName = htmlspecialchars($_POST['deceasedName']);
    $requestorName = htmlspecialchars($_POST['requestorName']);
    $dateOfMass = $_POST['datePick'];
    $timeOfMass = $_POST['timePick'];
    $contactInfo = htmlspecialchars($_POST['contact']);
    $servicePlace = htmlspecialchars($_POST['servicePlace']);
    $commentText = htmlspecialchars($_POST['commentText']);

    // Store values in session
    $_SESSION['deceasedName'] = $deceasedName;
    $_SESSION['requestorName'] = $requestorName;
    $_SESSION['datePick'] = $dateOfMass;
    $_SESSION['timePick'] = $timeOfMass;
    $_SESSION['contact'] = $contactInfo;
    $_SESSION['servicePlace'] = $servicePlace;
    $_SESSION['commentText'] = $commentText;

    // Redirect to confirmation page
    header("Location: userFuneralRequestConfirmation.php");
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Parish of San Juan</title>
        <link rel="stylesheet" href="userFuneral1.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>
    <script>
        function btnback(event) {
            event.preventDefault();
            window.location.href = "userLandingpage.php";
        }

        function validateForm() {
        // Get all form fields and trim extra spaces
        var deceasedName = document.querySelector('input[placeholder="Name of Deceased"]').value.trim().replace(/\s+/g, ' ');
        var requestorName = document.querySelector('input[placeholder="Name of Requestor"]').value.trim().replace(/\s+/g, ' ');
        var datePick = document.getElementById('datePick').value; // No trim needed for date input
        var timePick = document.getElementById('timePick').value; // No trim needed for date input
        var contact = document.querySelector('input[placeholder="Mobile number or email"]').value.trim().replace(/\s+/g, ' ');
        var servicePlace = document.getElementById('servicePlace').value;
        var commentText = document.getElementById('commentText').value.trim().replace(/\s+/g, ' ');

    // Check if any field is empty
    if (!deceasedName || !requestorName || !datePick || !timePick || !contact || !servicePlace || !commentText) {
        alert('All fields must be filled out before proceeding.');
        return false;
    }

    return true;
    
    }

    // Function to proceed to next step
    function btnnext(event) {
        event.preventDefault();
        if (validateForm()) {
            // Prepare form data for submission
            var formData = new FormData(document.querySelector('form'));
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url; // Redirect to confirmation page
                }
            });
        }
    }

   // Restrict past dates and times
        window.onload = function () {
            // Set today's date as the minimum for the date picker
            var today = new Date().toISOString().split('T')[0];
            var datePicker = document.getElementById('datePick');
            datePicker.setAttribute('min', today);

            // Validate the input to prevent typing past dates
            datePicker.addEventListener('input', function () {
                var selectedDate = this.value;
                if (selectedDate < today) {
                    alert('You cannot select a past date.');
                    this.value = ''; // Clear the invalid input
                }
            });
        };

        // Handle changes in the date picker
        document.getElementById('datePick').addEventListener('change', function () {
            var selectedDate = this.value;
            var today = new Date().toISOString().split('T')[0];

            if (selectedDate === today) {
                // If the selected date is today, restrict the time picker
                var currentTime = new Date();
                var hours = currentTime.getHours();
                var minutes = currentTime.getMinutes();

                // Format time to HH:MM (24-hour format)
                var formattedTime = (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes;

                // Set the minimum time to the current time
                document.getElementById('timePick').setAttribute('min', formattedTime);
            } else {
                // Remove the time restriction for future dates
                document.getElementById('timePick').removeAttribute('min');
            }
        });
    </script>
    <body>
        <?php include 'userHeader.php'; ?>
        <div id="formFillUp">
            <a href="#" id="funeralLabel" class="label">Funeral</a>
            <div id="notice">
                <form>
                    <p id="reqInfoLabel">Request Information</p>
                    <div class="form-container">
                        <!-- Left Side Inputs -->
                        <div class="left-side">
                            <p id="deceasedNameLabel">Name of Deceased</p>
                            <input type="text" name="deceasedName" placeholder="Name of Deceased" class="textFields" >
                            <!-- Date and Time pickers side by side -->
                            <div class="date-time-container">
                                <p id="dateTimeLabel">Date and Time</p>
                                <div class="date-time-inputs">
                                    <input type="date" id="datePick" name="datePick" class="textFields">
                                    <input type="time" id="timePick" name="timePick" class="textFields">
                                </div>
                            </div>
                        </div>
                        <!-- Right Side Inputs -->
                        <div class="right-side">
                            <p id="requestorNameLabel">Name of Requestor</p>
                            <input type="text" name="requestorName" placeholder="Name of Requestor" class="textFields">
                            <p id="contactLabel">Contact Info.</p>
                            <input type="text" id="contact" name="contact" placeholder="Mobile number or email" class="textFields">
                        </div>
                        </div>
                        <p id="placeLabel">Place of Service</p>
                            <select id="servicePlace" name="servicePlace">
                                <option value="house">House</option>
                                <option value="church" selected>Church</option>
                            </select>
                        <p id="commentLabel">Comments</p>
                        <textarea id="commentText" name="commentText" required></textarea>
                    <!-- Buttons -->
                    <button id="btnBack" type="button" class="btn btn-danger" onclick="btnback(event)">BACK</button>
                    <button id="btnNext" type="button" class="btn btn-success" onclick="btnnext(event)">NEXT</button>
                </form>
            </div>
        </div>
    </body>
</html>
