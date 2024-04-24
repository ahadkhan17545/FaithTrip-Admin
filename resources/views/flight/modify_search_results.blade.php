<div class="container search-box-container" id="mainCnt">
    <div class="search-box_row justify-content-center">
        <div class="search-box_col" style="padding-bottom: 32px;">
            <div class="search-box">
                <div class="tab-content position-relative" id="myTabContent">

                    <input type="hidden" id="get_airports_info" value>
                    <div class="tab-pane fade active show"
                        id="home" role="tabpanel"
                        aria-labelledby="home-tab">
                        <div class="search-tabs d-flex flex-wrap">
                            <input class="checkbox d-none"
                                id="tab1" type="radio"
                                name="tabsA">
                            <label
                                class="checkbox-label d-inline-block font-weight-500 me-2 border rounded fs-14 bg-white"
                                for="tab1">Round trip</label>
                            <input class="checkbox d-none"
                                id="tab2" type="radio"
                                name="tabsA" checked>
                            <label
                                class="checkbox-label d-inline-block font-weight-500 me-2 border rounded fs-14 bg-white"
                                for="tab2">One way</label>

                            <div class="search-content d-none w-100 pt-3"
                                id="search-content1">
                                <form class="modify-search"
                                    action="search.html" method="post"
                                    onsubmit="disableButtonRound()">
                                    <input type="hidden" name="_token"
                                        value="Dq5ZaIUBIH0dagMDNTHCoXXx9FLF2VHPBBrTWbzw">
                                    <input type="hidden" name="round"
                                        value="1">
                                    <div
                                        class="search-row row no-gutters position-relative mx-0 mb-4">
                                        <div class="col-lg-6 px-0">
                                            <div
                                                class="input-group rounded">
                                                <div
                                                    class="form-floating flight-form">
                                                    <label
                                                        for="floatingInput">From</label>
                                                    <select
                                                        class="round-trip-from-option form-control border-bottom-0 border-right select2"
                                                        id="round-trip-from-option"
                                                        name="from_airport_name"
                                                        required
                                                        tabindex="-1"
                                                        aria-hidden="true">
                                                        <option selected
                                                            value="DAC">
                                                            Dhaka,
                                                            Bangladesh
                                                            (DAC)
                                                        </option>
                                                        <option
                                                            value="NYC">
                                                            New York,
                                                            United
                                                            States (JFK)
                                                        </option>
                                                    </select>
                                                </div>
                                                <span
                                                    class="input-group-text">
                                                    <svg class="bi bi-arrow-left-right"
                                                        id="round-swap"
                                                        width="1.2em"
                                                        height="1.2em"
                                                        viewBox="0 0 16 16"
                                                        fill="currentColor"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M10.146 7.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L12.793 11l-2.647-2.646a.5.5 0 0 1 0-.708z">
                                                        </path>
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M2 11a.5.5 0 0 1 .5-.5H13a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 11zm3.854-9.354a.5.5 0 0 1 0 .708L3.207 5l2.647 2.646a.5.5 0 1 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0z">
                                                        </path>
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M2.5 5a.5.5 0 0 1 .5-.5h10.5a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z">
                                                        </path>
                                                    </svg>
                                                </span>
                                                <div
                                                    class="form-floating flight-to">
                                                    <label
                                                        for="floatingInput">To</label>
                                                    <select
                                                        class="round-trip-to-option form-control border-bottom-0 border-right select2"
                                                        id="round-trip-to-option"
                                                        name="to_airport_name"
                                                        required
                                                        tabindex="-1"
                                                        aria-hidden="true">
                                                        <option
                                                            value="DAC">
                                                            Dhaka,
                                                            Bangladesh
                                                            (DAC)
                                                        </option>

                                                        <option
                                                            value="NYC">
                                                            New York,
                                                            United
                                                            States (JFK)
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="col-lg-3 px-0 position-static">
                                            <div data-t-start="2024-03-11"
                                                data-t-end
                                                class="oneWay-datepicker t-datepicker t-datepicker-modal-round d-flex w-100 border-0"
                                                id="roundDatePicker">
                                                <div
                                                    class="t-check-in w-100">
                                                </div>
                                                <div
                                                    class="t-check-out w-100">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 px-0">
                                            <div class="dropdown travellers-dropdown"
                                                id="dropdown-roundTrip">
                                                <div class="form-floating"
                                                    id="dropdownMenuButton"
                                                    data-bs-toggle="dropdown"
                                                    aria-haspopup="true">
                                                    <input
                                                        type="text"
                                                        class="form-control dropdown-toggle"
                                                        id="passengers-roundTrip"
                                                        value="1 Travellers, Economy">
                                                    <label
                                                        for="passengers">Traveller(s)
                                                        cabin</label>
                                                </div>
                                                <div class="dropdown-menu dropdown-menu-right drop-down-round"
                                                    aria-labelledby="dropdownMenuButton">
                                                    <div
                                                        class="tab-container">
                                                        <div
                                                            class="triangle abs">
                                                        </div>
                                                        <ul
                                                            class="m-0 p-0">
                                                            <li
                                                                class="noOf d-flex justify-content-between">
                                                                <span>
                                                                    <input
                                                                        type="text"
                                                                        id="round-adult-input"
                                                                        class="all-input"
                                                                        readonly
                                                                        value="1">
                                                                    <span
                                                                        class="fs-16 font-weight-500">Adult<span>S</span></span>
                                                                </span>
                                                                <div
                                                                    class="spinner d-flex">
                                                                    <span
                                                                        id="round-adult-minus"
                                                                        class="minus">-</span>
                                                                    <span
                                                                        id="round-adult-plus"
                                                                        class="plus">+</span>
                                                                </div>
                                                                <input
                                                                    hidden
                                                                    name="adult_members"
                                                                    id="adult_input"
                                                                    value="1">
                                                            </li>
                                                            <li
                                                                class="noOf d-flex justify-content-between">
                                                                <span>
                                                                    <input
                                                                        type="text"
                                                                        id="round-child-input"
                                                                        class="all-input"
                                                                        readonly
                                                                        value="0">
                                                                    <span
                                                                        class="fs-16 font-weight-500">Child</span>
                                                                    <span
                                                                        class="cat-info fs-13">2
                                                                        11
                                                                        years</span>
                                                                </span>
                                                                <input
                                                                    hidden
                                                                    name="child_members"
                                                                    id="child_input"
                                                                    value="0">
                                                                <div
                                                                    class="spinner d-flex">
                                                                    <span
                                                                        id="round-child-minus"
                                                                        class="minus"
                                                                        onclick="roundChildDec()">-</span>
                                                                    <span
                                                                        id="round-child-plus"
                                                                        class="plus"
                                                                        onclick="roundChildInc()">+</span>
                                                                </div>
                                                            </li>
                                                            <li
                                                                class="noOf d-flex justify-content-between">
                                                                <div data-child-total="0"
                                                                    class="_child_age_"
                                                                    id="_child_age_">
                                                                </div>
                                                            </li>
                                                            <li
                                                                class="noOf d-flex justify-content-between">
                                                                <span>
                                                                    <input
                                                                        type="text"
                                                                        id="round-infant-input"
                                                                        class="all-input"
                                                                        readonly
                                                                        value="0">
                                                                    <span
                                                                        class="fs-16 font-weight-500">Infant</span>
                                                                    <span
                                                                        class="cat-info fs-13">Below
                                                                        2
                                                                        years</span>
                                                                </span>
                                                                <input
                                                                    hidden
                                                                    name="infant_members"
                                                                    id="infant_input"
                                                                    value="0">
                                                                <div
                                                                    class="spinner d-flex">
                                                                    <span
                                                                        id="round-infant-minus"
                                                                        class="minus">-</span>
                                                                    <span
                                                                        id="round-infant-plus"
                                                                        class="plus">+</span>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                        <div
                                                            class="class-type mt-2">
                                                            <div
                                                                class="custom-control custom-radio pl-0">
                                                                <input
                                                                    type="radio"
                                                                    id="economy2"
                                                                    name="classType1"
                                                                    class="custom-control-input economy1"
                                                                    checked>
                                                                <label
                                                                    class="custom-control-label fs-16 font-weight-500"
                                                                    for="economy1">Economy</label>
                                                            </div>
                                                            <div
                                                                class="custom-control custom-radio pl-0">
                                                                <input
                                                                    type="radio"
                                                                    id="premiumEconomy2"
                                                                    name="classType1"
                                                                    class="custom-control-input premiumEconomy1">
                                                                <label
                                                                    class="custom-control-label fs-16 font-weight-500"
                                                                    for="premiumEconomy1">Premium
                                                                    economy</label>
                                                            </div>
                                                            <div
                                                                class="custom-control custom-radio pl-0">
                                                                <input
                                                                    type="radio"
                                                                    id="first2"
                                                                    name="classType1"
                                                                    class="custom-control-input first1">
                                                                <label
                                                                    class="custom-control-label fs-16 font-weight-500"
                                                                    for="first1">First</label>
                                                            </div>
                                                            <div
                                                                class="custom-control custom-radio pl-0">
                                                                <input
                                                                    type="radio"
                                                                    id="business2"
                                                                    name="classType1"
                                                                    class="custom-control-input business1">
                                                                <label
                                                                    class="custom-control-label fs-16 font-weight-500"
                                                                    for="business1">Business</label>
                                                            </div>
                                                        </div>
                                                        <input hidden
                                                            name="classType"
                                                            id="class_type"
                                                            value="Y">
                                                        <div
                                                            class="cat-sel mt-3 text-right">
                                                            <input
                                                                type="button"
                                                                class="btn btn-danger w-100"
                                                                onclick="roundTripTotalPassenger()"
                                                                value="Confirm">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="btn-hub-round">
                                        <button type="submit"
                                            id="btn-search-round"
                                            class="btn btn-primary btn-search">Search
                                            flights <i
                                                class="fas fa-plane-departure"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="search-content d-none w-100 pt-3"
                                id="search-content2">
                                <form id="one_way_search_btn"
                                    class="modify-search"
                                    action="search.html"
                                    method="post"
                                    onsubmit="disableButtonOneWay()">
                                    <input type="hidden"
                                        name="_token"
                                        value="Dq5ZaIUBIH0dagMDNTHCoXXx9FLF2VHPBBrTWbzw">
                                    <input type="hidden"
                                        name="oneway" value="1">
                                    <div
                                        class="search-row row no-gutters position-relative mx-0 mb-4">
                                        <div class="col-lg-6 px-0">
                                            <div
                                                class="input-group rounded">
                                                <div
                                                    class="form-floating flight-form">
                                                    <label
                                                        for="floatingInput">From</label>
                                                    <select
                                                        class="one-way-from-option form-control border-bottom-0 border-right select2"
                                                        id="one-way-from-option"
                                                        name="from_airport_name"
                                                        tabindex="-1"
                                                        aria-hidden="true">
                                                        <option selected
                                                            value="DAC">
                                                            Dhaka,
                                                            Bangladesh
                                                            (DAC)
                                                        </option>
                                                        <option
                                                            value="YSO">
                                                            Postville,
                                                            Canada (YSO)
                                                        </option>
                                                        <option
                                                            value="NYC">
                                                            New York,
                                                            United
                                                            States (JFK)
                                                        </option>
                                                    </select>
                                                </div>
                                                <span
                                                    class="input-group-text">
                                                    <svg class="bi bi-arrow-left-right"
                                                        id="oneway-swap"
                                                        width="1.2em"
                                                        height="1.2em"
                                                        viewBox="0 0 16 16"
                                                        fill="currentColor"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M10.146 7.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L12.793 11l-2.647-2.646a.5.5 0 0 1 0-.708z">
                                                        </path>
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M2 11a.5.5 0 0 1 .5-.5H13a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 11zm3.854-9.354a.5.5 0 0 1 0 .708L3.207 5l2.647 2.646a.5.5 0 1 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0z">
                                                        </path>
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M2.5 5a.5.5 0 0 1 .5-.5h10.5a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z">
                                                        </path>
                                                    </svg>
                                                </span>
                                                <div
                                                    class="form-floating flight-to">
                                                    <label
                                                        for="floatingInput">To</label>

                                                    <select
                                                        class="one-way-to-option form-control border-bottom-0 border-right select2"
                                                        id="one-way-to-option"
                                                        name="to_airport_name"
                                                        tabindex="-1"
                                                        aria-hidden="true">
                                                        <option
                                                            value="DAC">
                                                            Dhaka,
                                                            Bangladesh
                                                            (DAC)
                                                        </option>
                                                        <option
                                                            value="YSO">
                                                            Postville,
                                                            Canada (YSO)
                                                        </option>
                                                        <option
                                                            value="NYC">
                                                            New York,
                                                            United
                                                            States (JFK)
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="col-lg-3 px-0 position-static">
                                            <div data-t-start="2024-03-11"
                                                data-t-end
                                                class="oneWay-datepicker t-datepicker t-datepicker-modal-oneway d-flex w-100 border-0 h-100"
                                                id="oneWayDatePicker">
                                                <div
                                                    class="t-check-in">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 px-0">
                                            <div class="dropdown travellers-dropdown"
                                                id="dropdown-oneway">
                                                <div class="form-floating"
                                                    id="dropdownMenuButton"
                                                    data-bs-toggle="dropdown"
                                                    aria-haspopup="true">
                                                    <input
                                                        type="text"
                                                        class="form-control dropdown-toggle"
                                                        id="passengers-oneway"
                                                        value="1 Travelers, Economy"
                                                        readonly>
                                                    <label
                                                        for="passengers">Traveler(s)
                                                        cabin</label>
                                                </div>
                                                <div class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="dropdownMenuButton">
                                                    <div
                                                        class="tab-container">
                                                        <div
                                                            class="triangle abs">
                                                        </div>
                                                        <ul
                                                            class="m-0 p-0">
                                                            <li
                                                                class="noOf d-flex justify-content-between">
                                                                <span>
                                                                    <input
                                                                        type="text"
                                                                        id="oneway-adult-input"
                                                                        class="all-input"
                                                                        readonly
                                                                        value="1">
                                                                    <span
                                                                        class="fs-16 font-weight-500">Adult<span>s</span></span>
                                                                </span>
                                                                <div
                                                                    class="spinner d-flex">
                                                                    <span
                                                                        id="oneway-adult-minus"
                                                                        class="minus">-</span>
                                                                    <span
                                                                        id="oneway-adult-plus"
                                                                        class="plus">+</span>
                                                                </div>
                                                                <input
                                                                    hidden
                                                                    name="adult_members"
                                                                    id="adult_input_one"
                                                                    value="1">
                                                            </li>
                                                            <li
                                                                class="noOf d-flex justify-content-between">
                                                                <span>
                                                                    <input
                                                                        type="text"
                                                                        id="oneway-child-input"
                                                                        class="all-input"
                                                                        readonly
                                                                        value="0">
                                                                    <span
                                                                        class="fs-16 font-weight-500">Child</span>
                                                                    <span
                                                                        class="cat-info fs-13">
                                                                        2
                                                                        11
                                                                        years</span>
                                                                </span>
                                                                <input
                                                                    hidden
                                                                    name="child_members"
                                                                    id="child_input_one"
                                                                    value="0">
                                                                <div
                                                                    class="spinner d-flex">
                                                                    <span
                                                                        id="oneway-child-minus"
                                                                        class="minus"
                                                                        onclick="oneWayChildDec()">-</span>
                                                                    <span
                                                                        id="oneway-child-plus"
                                                                        class="plus"
                                                                        onclick="oneWayChildInc()">+</span>
                                                                </div>
                                                            </li>
                                                            <li
                                                                class="noOf d-flex justify-content-between">
                                                                <div data-child-total="0"
                                                                    class="_child_age_"
                                                                    id="_child_age_">
                                                                </div>
                                                            </li>
                                                            <li
                                                                class="noOf d-flex justify-content-between">
                                                                <span>
                                                                    <input
                                                                        type="text"
                                                                        id="oneway-infant-input"
                                                                        class="all-input"
                                                                        readonly
                                                                        value="0">
                                                                    <span
                                                                        class="fs-16 font-weight-500">Infant</span>
                                                                    <span
                                                                        class="cat-info fs-13">Below
                                                                        2
                                                                        years</span>
                                                                </span>
                                                                <div
                                                                    class="spinner d-flex">
                                                                    <span
                                                                        id="oneway-infant-minus"
                                                                        class="minus">-</span>
                                                                    <span
                                                                        id="oneway-infant-plus"
                                                                        class="plus">+</span>
                                                                </div>
                                                                <input
                                                                    hidden
                                                                    name="infant_members"
                                                                    id="infant_input_one"
                                                                    value="0">
                                                            </li>
                                                        </ul>
                                                        <div
                                                            class="class-type mt-2">
                                                            <div
                                                                class="custom-control custom-radio pl-0">
                                                                <input
                                                                    type="radio"
                                                                    id="economy1"
                                                                    name="classType1"
                                                                    class="custom-control-input economy1"
                                                                    checked>
                                                                <label
                                                                    class="custom-control-label fs-16 font-weight-500"
                                                                    for="economy1">Economy</label>
                                                            </div>
                                                            <div
                                                                class="custom-control custom-radio pl-0">
                                                                <input
                                                                    type="radio"
                                                                    id="premiumEconomy1"
                                                                    name="classType1"
                                                                    class="custom-control-input premiumEconomy1">
                                                                <label
                                                                    class="custom-control-label fs-16 font-weight-500"
                                                                    for="premiumEconomy1">Premium
                                                                    economy</label>
                                                            </div>
                                                            <div
                                                                class="custom-control custom-radio pl-0">
                                                                <input
                                                                    type="radio"
                                                                    id="first1"
                                                                    name="classType1"
                                                                    class="custom-control-input first1">
                                                                <label
                                                                    class="custom-control-label fs-16 font-weight-500"
                                                                    for="first1">First</label>
                                                            </div>
                                                            <div
                                                                class="custom-control custom-radio pl-0">
                                                                <input
                                                                    type="radio"
                                                                    id="business1"
                                                                    name="classType1"
                                                                    class="custom-control-input business1">
                                                                <label
                                                                    class="custom-control-label fs-16 font-weight-500"
                                                                    for="business1">Business</label>
                                                            </div>
                                                        </div>
                                                        <input hidden
                                                            name="classType"
                                                            id="class_type_one"
                                                            value="Y">
                                                        <div
                                                            class="cat-sel mt-3 text-right">
                                                            <input
                                                                type="button"
                                                                class="btn btn-danger w-100"
                                                                onclick="oneWayTotalPassenger()"
                                                                value="Confirm">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="btn-hub-oneway">
                                        <button type="submit"
                                            id="btn-search-oneway"
                                            class="btn btn-primary btn-search">Search
                                            flights <i
                                                class="fas fa-plane-departure"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
