<?php

    // Aircraft Helpers
    if (!function_exists('getAircraftMap')) {
        function getAircraftMap()
        {
            return [
                "20J" => "Embraer ERJ-170",
                "208" => "Beechcraft 1900D",
                "208" => "Beechcraft 1900D",
                "319" => "Airbus A319",
                "320" => "Airbus A320",
                "321" => "Airbus A321",
                "322" => "Airbus A320-200",
                "323" => "Airbus A320-200",
                "332" => "Airbus A330-200",
                "333" => "Airbus A330-300",
                "343" => "Airbus A340-300",
                "346" => "Airbus A340-600",
                "388" => "Airbus A380-800",
                "73H" => "Boeing 737-800 (High Capacity)",
                "73G" => "Boeing 737-700",
                "73H" => "Boeing 737-800",
                "73W" => "Boeing 737-800",
                "738" => "Boeing 737-800",
                "739" => "Boeing 737-900ER",
                "744" => "Boeing 747-400",
                "748" => "Boeing 747-8",
                "752" => "Boeing 757-200",
                "753" => "Boeing 757-300",
                "763" => "Boeing 767-300",
                "764" => "Boeing 767-400",
                "772" => "Boeing 777-200",
                "773" => "Boeing 777-300",
                "77L" => "Boeing 777-200LR",
                "77W" => "Boeing 777-300ER",
                "787" => "Boeing 787-8",
                "788" => "Boeing 787-8",
                "789" => "Boeing 787-9",
                "CR7" => "Bombardier CRJ-700",
                "CR9" => "Bombardier CRJ-900",
                "E70" => "Embraer E170",
                "E75" => "Embraer E175",
                "E90" => "Embraer E190",
                "E95" => "Embraer E195",
                "SF3" => "Fairchild Dornier 328JET",
                "AT7" => "ATR 72-600",
                "AT4" => "ATR 42-400",
                "DH8" => "Bombardier Dash 8",
                "DH8A" => "Bombardier Dash 8-100",
                "DH8B" => "Bombardier Dash 8-200",
                "DH8C" => "Bombardier Dash 8-300",
                "DH8D" => "Bombardier Dash 8-Q400",
                "JS3" => "Sukhoi Superjet 100",
                "GLF" => "Gulfstream",
                "C25" => "Cessna 525 CitationJet",
                // Add more as needed
            ];
        }
    }

    if (!function_exists('getAircraftName')) {
        function getAircraftName(string $code): string
        {
            $map = getAircraftMap();
            return $map[$code] ?? 'Unknown Aircraft';
        }
    }

    // Booking Helpers
    if (!function_exists('getCabinClass')) {
        function getCabinClass(?string $code): string
        {
            $map = [
                'F' => 'First',
                'A' => 'First',
                'J' => 'Business',
                'C' => 'Business',
                'D' => 'Business',
                'I' => 'Business',
                'Z' => 'Business',
                'W' => 'Premium Economy',
                'Y' => 'Economy',
                'B' => 'Economy',
                'M' => 'Economy',
                'H' => 'Economy',
                'K' => 'Economy',
                'L' => 'Economy',
                'N' => 'Economy',
                'Q' => 'Economy',
                'T' => 'Economy',
                'V' => 'Economy',
                'X' => 'Economy',
                'S' => 'Economy',
                'E' => 'Basic Economy'
            ];

            // Normalize the input to uppercase string or default to empty
            $code = strtoupper((string) $code);
            return $map[$code] ?? 'Unknown';
        }
    }

    if (! function_exists('aircraft_name')) {
        function aircraft_name(?string $code): string
        {
            if (!$code) return 'Aircraft TBA';
            $map = config('aircraft');
            return $map[$code] ?? $code; // fallback to code if unknown
        }
    }

?>
