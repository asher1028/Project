<?php
include 'userSessionStart.php';
include '../config/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requesterName = htmlspecialchars($_POST['requesterName']);
    $address = htmlspecialchars($_POST['address']);
    $datePick = $_POST['datePick'];
    $timePick = $_POST['timePick'];
    $parishChoirAnswer = htmlspecialchars($_POST['parishChoirAnswer']);
    $contact = htmlspecialchars($_POST['contact']);
    $commentText = htmlspecialchars($_POST['commentText']);

    $_SESSION['requesterName'] = $requesterName;
    $_SESSION['address'] = $address;
    $_SESSION['datePick'] = $datePick;
    $_SESSION['timePick'] = $timePick;
    $_SESSION['parishChoirAnswer'] = $parishChoirAnswer;
    $_SESSION['contact'] = $contact;
    $_SESSION['commentText'] = $commentText;

    header("Location: userPrivateMassConfirmation.php");
    exit();
}


?>
<!DOCTYPE html>
<html>
    <head>
        <title>Parish of San Juan</title>
        <link rel="stylesheet" href="userPrivateMass3.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script>
            function btnback(event) {
                event.preventDefault();
                window.location.href = "userLandingpage.php";
            }

            function validateForm() {
                var requesterName = document.querySelector('input[placeholder="Name of the Requester"]').value.trim().replace(/\s+/g, ' ');
                var address = document.querySelector('input[placeholder="Address"]').value.trim().replace(/\s+/g, ' ');
                var datePick = document.getElementById('datePick').value;
                var timePick = document.getElementById('timePick').value;
                var parishChoirAnswer = document.querySelector('input[name="parishChoirAnswer"]:checked');
                var contact = document.querySelector('input[placeholder="Mobile number or email"]').value.trim().replace(/\s+/g, ' ');
                var commentText = document.getElementById('commentText').value.trim().replace(/\s+/g, ' ');

            if (!requesterName || !address || !datePick || !timePick || !parishChoirAnswer || !contact || !commentText) {
                alert('All fields must be filled out before proceeding.');
                return false;
        }

        return true;
        }

            function btnsend(event) {
                event.preventDefault();
                if (validateForm()) {
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
    </head>
    <body>
        <?php include 'userHeader.php'; ?>
        <div id="mainDiv">
            <div id="forLabel">
                <a href="#" id="weddingScheduleLabel" class="label">Private Mass</a>
            </div>
            <form>
            <div id="formsDiv" class="d-flex flex-column">
                <p id="reqInfoLabel">Application Information</p>
                <div id=forEachSide class="d-flex">
                    <div id="leftSide" class="w-50">
                        <p id="requesterName" class="textLabels">Name of the Requester</p>
                        <input type="text" name="requesterName" id="requesterNameField" class="textFields" placeholder="Name of the Requester">
                        <p id="requesterAddress" class="textLabels">Address</p>
                        <input type="text" name="address" id="requesterAddreField" class="textFields" placeholder="Address">
                    </div>
                    <div id="rightSide" class="w-50">
                    <p id="dateTimeLabel" class="dateTimePicker">Date and Time</p>
                        <div id="forDateAndTime" class="d-flex">
                            <input type="date" id="datePick" name="datePick" class="dateField">
                            <input type="time" id="timePick" name="timePick" class="timeField">
                        </div>
                        <div id="forChoirAndContact">
                            <div id="forParishChoir">
                                <p id="parishChoirLabel">Parish Choir</p>
                                <input type="radio" id="parishChoirYes" name="parishChoirAnswer" value="Yes" required> Yes</input>
                                <input type="radio" id="parishChoirNo" name="parishChoirAnswer" value="No" checked> No</input>
                            </div>
                            <div id="forContact">
                                <p id="contactLabel">Contact Info.</p>
                                <input type="text" id="contactInfo" name="contact" placeholder="Mobile number or email" class="textFields">
                            </div>
                        </div>
                    </div>
                </div>
                <p id="commentLabel">Comments</p>
                <textarea id="commentText" name="commentText" required></textarea>
                <!-- Buttons moved inside formsDiv -->
                <div id="buttonsDiv">
                    <button id="btnSend" type="button" class="btn btn-success" onclick="btnsend(event)">Next</button>
                    <button id="btnBack" type="button" class="btn btn-danger" onclick="btnback(event)">Back</button>
                </div>
            </div>
        </form>
        </div>
    </body>
</html>
