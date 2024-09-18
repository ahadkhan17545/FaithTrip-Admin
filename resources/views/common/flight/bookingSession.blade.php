<div class="card shadow border-0 mb-3 d-none d-xl-block">
    <div class="card-body">
        <h3 class="fs-17 mb-0">Booking Session</h3>
        <hr>
        <h3 class="text-center" style="font-size: 22px; font-weight: 600; color: #b40000;" id="countdown">00:00:00</h3>
        <hr>
        <script>
             // Target date and time (Year, Month (0-based), Day, Hour, Minute, Second)
            const targetDate = new Date('{{ $revalidatedData['session_expired_at']}}').getTime();

            // Update the countdown every 1 second
            const countdownInterval = setInterval(function() {
                // Get the current date and time
                const now = new Date().getTime();

                // Calculate the difference in time
                const timeDiff = targetDate - now;

                // Time calculations for days, hours, minutes, and seconds
                const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

                // Display the result in the element with id="countdown"
                // document.getElementById("countdown").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                document.getElementById("countdown").innerHTML = minutes + "m " + seconds + "s ";

                // If the countdown is over, stop the timer and redirect
                if (timeDiff < 0) {
                    clearInterval(countdownInterval);
                    document.getElementById("countdown").innerHTML = "Countdown Over";

                    // Redirect to homepage after countdown finishes
                    window.location.href = '/home'; // Redirects to the home page of the website
                }
            }, 1000);
        </script>
    </div>
</div>
