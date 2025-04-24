@extends('master')

@section('header_css')
    <style>
        .section {
            margin-bottom: 40px;
        }

        .section-title {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            gap: 12px;
        }

        .section-title h2 {
            color: #FF0832;
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }

        .section-title img {
            width: 28px;
            height: 28px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card-logo {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .card-logo img {
            max-height: 100%;
            max-width: 100%;
        }

        .card-title {
            font-size: 16px;
            color: #333;
            font-weight: bold;
            margin-bottom: 12px;
            text-align: center;
        }

        .card-details {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }

        .detail-row {
            margin-bottom: 5px;
        }

        .detail-label {
            font-weight: bold;
        }

        .branch-name {
            color: #FF0832;
        }

        .mobile-banking-card,
        .cash-office-card,
        .online-payment-card {
            text-align: center;
        }

        .mobile-banking-card .card-title,
        .online-payment-card .card-title {
            margin-top: 10px;
        }

        .cash-office-address {
            text-align: left;
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .card-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .card-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection


@section('content')
    <!-- Direct Banking Section -->
    <div class="section">
        <div class="section-title">
            <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M20.1012 3.4938L34.7262 9.34376C35.295 9.57126 35.75 10.2538 35.75 10.855V16.25C35.75 17.1438 35.0188 17.875 34.125 17.875H4.875C3.98125 17.875 3.25 17.1438 3.25 16.25V10.855C3.25 10.2538 3.70501 9.57126 4.27376 9.34376L18.8988 3.4938C19.2238 3.3638 19.7762 3.3638 20.1012 3.4938Z"
                    stroke="#FF0832" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round">
                </path>
                <path
                    d="M35.75 35.75H3.25V30.875C3.25 29.9812 3.98125 29.25 4.875 29.25H34.125C35.0188 29.25 35.75 29.9812 35.75 30.875V35.75Z"
                    stroke="#FF0832" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M6.5 29.25V17.875" stroke="#FF0832" stroke-width="1.5" stroke-miterlimit="10"
                    stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M13 29.25V17.875" stroke="#FF0832" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M19.5 29.25V17.875" stroke="#FF0832" stroke-width="1.5" stroke-miterlimit="10"
                    stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M26 29.25V17.875" stroke="#FF0832" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M32.5 29.25V17.875" stroke="#FF0832" stroke-width="1.5" stroke-miterlimit="10"
                    stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M1.625 35.75H37.375" stroke="#FF0832" stroke-width="1.5" stroke-miterlimit="10"
                    stroke-linecap="round" stroke-linejoin="round"></path>
                <path
                    d="M19.5 13.8125C20.8462 13.8125 21.9375 12.7212 21.9375 11.375C21.9375 10.0288 20.8462 8.9375 19.5 8.9375C18.1538 8.9375 17.0625 10.0288 17.0625 11.375C17.0625 12.7212 18.1538 13.8125 19.5 13.8125Z"
                    stroke="#FF0832" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                    stroke-linejoin="round"></path>
            </svg>
            <h2>Direct Banking</h2>
        </div>
        <div class="card-grid">

            @foreach ($bankAccounts as $bankAccount)
            <div class="card">
                <div class="card-logo">
                    @if(str_contains($bankAccount->bank_name, 'Islami'))
                        <img src="{{url('bank_logos')}}/islami_bank.jpg" alt="Islami Bank Bangladesh PLC" style="width: 85%;">
                    @endif
                    @if(str_contains($bankAccount->bank_name, 'United Commercial'))
                        <img src="{{url('bank_logos')}}/ucb_bank.jpg" alt="United Commercial Bank">
                    @endif
                    @if(str_contains($bankAccount->bank_name, 'City Bank'))
                        <img src="{{url('bank_logos')}}/city_bank.png" alt="City Bank Ltd">
                    @endif
                    @if(str_contains($bankAccount->bank_name, 'Dutch Bangla'))
                        <img src="{{url('bank_logos')}}/dutchbangla_bank.png" alt="Dutch Bangla Bank Limited" style="width: 85%;">
                    @endif
                    @if(str_contains($bankAccount->bank_name, 'Eastern'))
                        <img src="{{url('bank_logos')}}/eastern_bank.png" alt="Eastern Bank PLC" style="width: 85%;">
                    @endif
                    @if(str_contains($bankAccount->bank_name, 'Southeast'))
                        <img src="{{url('bank_logos')}}/southeast_bank.jpg" alt="Southeast Bank Limited" style="width: 85%;">
                    @endif
                    @if(str_contains($bankAccount->bank_name, 'Bengal Commercial'))
                        <img src="{{url('bank_logos')}}/bengal_bank.png" alt="Bengal Commercial Bank" style="width: 70%;">
                    @endif
                    @if(str_contains($bankAccount->bank_name, 'BRAC'))
                        <img src="{{url('bank_logos')}}/brac_bank.png" alt="BRAC Bank Limited">
                    @endif
                </div>
                <div class="card-title">{{$bankAccount->bank_name}}</div>
                <div class="card-details">
                    <div class="detail-row">
                        <span class="detail-label">Account Name:</span> {{$bankAccount->acc_holder_name}}
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">A/C No.:</span> {{$bankAccount->acc_no}}
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Routing No.:</span> {{$bankAccount->routing_no}}
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Branch:</span> <span class="branch-name">{{$bankAccount->branch_name}}</span>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>

    <!-- Mobile Banking Section -->
    <div class="section">
        <div class="section-title">
            <svg width="31" height="38" viewBox="0 0 31 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M30 10V28C30 35.2 28.1875 37 20.9375 37H10.0625C2.8125 37 1 35.2 1 28V10C1 2.8 2.8125 1 10.0625 1H20.9375C28.1875 1 30 2.8 30 10Z"
                    stroke="#FF0832" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M19.125 7.30005H11.875" stroke="#FF0832" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path
                    d="M15.5 31.78C17.0516 31.78 18.3094 30.5308 18.3094 28.99C18.3094 27.4491 17.0516 26.2 15.5 26.2C13.9484 26.2 12.6906 27.4491 12.6906 28.99C12.6906 30.5308 13.9484 31.78 15.5 31.78Z"
                    stroke="#FF0832" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            <h2>Mobile Banking</h2>
        </div>
        <div class="card-grid">

            @foreach ($mfsAccounts as $mfsAccount)
            <div class="card mobile-banking-card">
                <div class="card-logo">
                    @if($mfsAccount->account_type == 1)
                        <img src="{{url('bank_logos')}}/bkash.png" alt="bKash">
                    @endif
                    @if($mfsAccount->account_type == 2)
                        <img src="{{url('bank_logos')}}/nagad.png" alt="Nagad">
                    @endif
                    @if($mfsAccount->account_type == 3)
                        <img src="{{url('bank_logos')}}/rocket.png" alt="Rocket">
                    @endif
                    @if($mfsAccount->account_type == 4)
                        <img src="{{url('bank_logos')}}/upay.png" alt="Upay">
                    @endif
                    @if($mfsAccount->account_type == 5)
                        {{-- <img src="{{url('bank_logos')}}/sure_cash.png" alt="Sure Cash"> --}}
                    @endif
                </div>
                <div class="card-title">
                    @if($mfsAccount->account_type == 1)
                        bKash
                    @endif
                    @if($mfsAccount->account_type == 2)
                        Nagad
                    @endif
                    @if($mfsAccount->account_type == 3)
                        Rocket
                    @endif
                    @if($mfsAccount->account_type == 4)
                        Upay
                    @endif
                    @if($mfsAccount->account_type == 5)
                        Sure Cash
                    @endif
                    Merchant Account
                </div>
                <div class="card-details">{{$mfsAccount->acc_no}}</div>
            </div>
            @endforeach

        </div>
    </div>

    <!-- Cash at Office Section -->
    <div class="section">
        <div class="section-title">
            <svg width="38" height="32" viewBox="0 0 38 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M31.775 8.85997V17.8725C31.775 23.2625 28.695 25.5724 24.075 25.5724H8.69248C7.90498 25.5724 7.15248 25.5025 6.45248 25.345C6.01498 25.275 5.595 25.1525 5.21 25.0125C2.585 24.0325 0.992493 21.7575 0.992493 17.8725V8.85997C0.992493 3.46997 4.07248 1.16003 8.69248 1.16003H24.075C27.995 1.16003 30.8125 2.82253 31.565 6.62003C31.6875 7.32003 31.775 8.03747 31.775 8.85997Z"
                    stroke="#FF0832" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path
                    d="M37.0269 14.1102V23.1227C37.0269 28.5127 33.9469 30.8227 29.3269 30.8227H13.9444C12.6494 30.8227 11.4769 30.6478 10.4619 30.2628C8.37942 29.4928 6.96191 27.9003 6.45441 25.3453C7.15441 25.5028 7.9069 25.5727 8.6944 25.5727H24.0769C28.6969 25.5727 31.7769 23.2627 31.7769 17.8727V8.86018C31.7769 8.03768 31.7069 7.30274 31.5669 6.62024C34.8919 7.32024 37.0269 9.66518 37.0269 14.1102Z"
                    stroke="#FF0832" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path
                    d="M16.3723 17.9948C18.9238 17.9948 20.9923 15.9263 20.9923 13.3748C20.9923 10.8232 18.9238 8.75476 16.3723 8.75476C13.8207 8.75476 11.7523 10.8232 11.7523 13.3748C11.7523 15.9263 13.8207 17.9948 16.3723 17.9948Z"
                    stroke="#FF0832" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M6.36499 9.52502V17.2251" stroke="#FF0832" stroke-width="1.5" stroke-miterlimit="10"
                    stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M26.388 9.52551V17.2256" stroke="#FF0832" stroke-width="1.5" stroke-miterlimit="10"
                    stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            <h2>Cash at Office</h2>
        </div>
        <div class="card-grid">
            <!-- Motijheel, Dhaka Office -->
            <div class="card cash-office-card">
                <div class="card-logo">
                    <img src="https://portal.faithtrip.net/companyLogo/edAFN1717060920.svg" alt="Rights expert agent">
                </div>
                <div class="card-title">Gulshan, Dhaka Office</div>
                <div class="cash-office-address">
                    90/1 Gulshan, City Centre<br>
                    Level 2B-1, Lift 26, Dhaka<br>
                    Bangladesh
                </div>
            </div>

            <!-- Agrabad, Chattogram Office -->
            <div class="card cash-office-card">
                <div class="card-logo">
                    <img src="https://portal.faithtrip.net/companyLogo/edAFN1717060920.svg" alt="Rights expert agent">
                </div>
                <div class="card-title">Cox's Bazer, Chattogram Office</div>
                <div class="cash-office-address">
                    Level:8 4, Business Center<br>
                    1280/B, SK Zia Road<br>
                    Cox's Bazer, Chattogram
                </div>
            </div>
        </div>
    </div>

    <!-- Accepted Online Payment Methods -->
    <div class="section">
        <div class="section-title">
            <svg width="43" height="43" viewBox="0 0 43 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M22.6825 4.51158L22.6287 4.63699L17.4329 16.6949H12.3267C11.1083 16.6949 9.94374 16.9457 8.88666 17.3937L12.0221 9.90449L12.0937 9.72533L12.2192 9.43866C12.255 9.33116 12.2908 9.22366 12.3446 9.13408C14.6917 3.70533 17.3433 2.46908 22.6825 4.51158Z"
                    stroke="#FF0832" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path
                    d="M32.3396 17.0534C31.5333 16.8026 30.6733 16.6951 29.8133 16.6951H17.4329L22.6288 4.63714L22.6825 4.51172C22.9513 4.6013 23.2021 4.72672 23.4708 4.83422L27.4304 6.50047C29.6342 7.41422 31.175 8.3638 32.1067 9.51047C32.2858 9.72547 32.4292 9.92255 32.5546 10.1555C32.7158 10.4063 32.8413 10.6571 32.9129 10.9259C32.9846 11.0871 33.0383 11.2484 33.0742 11.3917C33.5579 12.8967 33.2713 14.7421 32.3396 17.0534Z"
                    stroke="#FF0832" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path
                    d="M38.5597 25.4386V28.9324C38.5597 29.2907 38.5418 29.6491 38.5239 30.0074C38.1835 36.2603 34.6897 39.4136 28.0606 39.4136H14.0856C13.6556 39.4136 13.2256 39.3778 12.8135 39.3241C7.11598 38.9478 4.07014 35.902 3.69389 30.2045C3.64014 29.7924 3.60431 29.3624 3.60431 28.9324V25.4386C3.60431 21.8374 5.79014 18.7378 8.90764 17.3941C9.98264 16.9461 11.1293 16.6953 12.3476 16.6953H29.8343C30.7122 16.6953 31.5722 16.8207 32.3606 17.0536C35.926 18.1466 38.5597 21.4791 38.5597 25.4386Z"
                    stroke="#FF0832" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path
                    d="M12.0221 9.90454L8.88665 17.3937C5.76915 18.7375 3.58331 21.837 3.58331 25.4383V20.1887C3.58331 15.1004 7.20248 10.8541 12.0221 9.90454Z"
                    stroke="#FF0832" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path
                    d="M38.5541 20.188V25.4375C38.5541 21.4959 35.9383 18.1455 32.355 17.0705C33.2866 14.7413 33.5554 12.9138 33.1075 11.3909C33.0716 11.2296 33.0179 11.0684 32.9462 10.925C36.2787 12.645 38.5541 16.1746 38.5541 20.188Z"
                    stroke="#FF0832" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            <h2>Accepted Online Payment Methods</h2>
        </div>
        <div class="card-grid">
            <!-- SSL Commerz -->
            <div class="card online-payment-card">
                <div class="card-logo">
                    <img src="https://b2b.flightexpertagent.com/assets/img/sslc.png" alt="SSL Commerz">
                </div>
                <div class="card-title">SSL Commerz</div>
            </div>

            <!-- Visa/Master Card -->
            <div class="card online-payment-card">
                <div class="card-logo">
                    <img src="https://b2b.flightexpertagent.com/assets/img/visa-card.png" alt="BRAC Bank">
                </div>
                <div class="card-title">Visa/Master Card</div>
            </div>

            <!-- bKash -->
            <div class="card online-payment-card">
                <div class="card-logo">
                    <img src="https://b2b.flightexpertagent.com/assets/img/bkash.png" alt="bKash Payment">
                </div>
                <div class="card-title">bKash</div>
            </div>

            <!-- Nagad -->
            <div class="card online-payment-card">
                <div class="card-logo">
                    <img src="https://b2b.flightexpertagent.com/assets/img/nagad.png" alt="Nagad">
                </div>
                <div class="card-title">Nagad</div>
            </div>
        </div>
    </div>
@endsection
