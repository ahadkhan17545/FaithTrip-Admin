<div class="card shadow border-0 mb-3">
    <div class="card-body">
        <h3 class="fs-17 mb-0 text-center">Session Timeout In</h3>
        <hr>
        <h3 class="text-center" style="font-size: 22px; font-weight: 600; color: #b40000;" id="countdown">00:00:00</h3>
        <hr style="margin-bottom: 0px">

        @php
            $timeoutTime = strtotime(session('session_timeout_at'));
            $currentTime = strtotime(date("Y-m-d H:i:s"));
            $remainingTime = $timeoutTime - $currentTime;
            if ($remainingTime < 0) {
                $remainingTime = 0;
            }
        @endphp

        <script>
            var remainingTime = <?php echo $remainingTime; ?>;
            function startCountdown(seconds) {
                const countdownElement = document.getElementById('countdown');

                function updateCountdown() {
                    if (seconds <= 0) {
                        countdownElement.textContent = "Session expired!";
                        clearInterval(timer);
                        window.location.href = '/home';
                    } else {
                        const minutes = Math.floor(seconds / 60);
                        const remainingSeconds = seconds % 60;
                        countdownElement.textContent = `${minutes}m ${remainingSeconds}s`;
                        seconds--;
                    }
                }

                updateCountdown();
                const timer = setInterval(updateCountdown, 1000);
            }
            startCountdown(remainingTime);
        </script>
    </div>
</div>
