function disableButtonOneWay() {
  let btn = document.getElementById("btn-search-oneway");
  let btnHub = document.getElementById("btn-hub-oneway");
  btn.remove();
  btnHub.innerHTML = `<button class="btn-search btn btn-primary" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Searching...
                        </button>`;
}

function disableButtonRound() {
  let btn = document.getElementById("btn-search-round");
  let btnHub = document.getElementById("btn-hub-round");
  btn.remove();
  btnHub.innerHTML = `<button class="btn-search btn btn-primary" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Searching...
                </button>`;
}

let airports;

const oneWayFr = document.getElementById("one-way-from-option");
const oneWayTo = document.getElementById("one-way-to-option");
const roundTripFr = document.getElementById("round-trip-from-option");
const roundTripTo = document.getElementById("round-trip-to-option");

const airportMatcher = (params, data) => {
  data.priority = -1;

  // Show every item if query isn't given yet
  if (
    params.term === undefined ||
    data.id === undefined ||
    data.text === undefined ||
    data.text === ""
  ) {
    return data;
  }

  let query = params.term;

  if (data.id === query) {
    data.priority = 1;
  } else if (data.id.toLowerCase() === query.toLowerCase()) {
    data.priority = 2;
  } else if (data.id.indexOf(query) === 0) {
    data.priority = 3;
  } else if (data.id.toLowerCase().indexOf(query) === 0) {
    data.priority = 4;
  } else if (data.id.toLowerCase().indexOf(query.toLowerCase()) === 0) {
    data.priority = 5;
  } else if (data.id.toLowerCase().indexOf(query.toLowerCase()) !== -1) {
    data.priority = 6;
  } else if (data.text.indexOf(query) === 0) {
    data.priority = 7;
  } else if (data.text.toLowerCase().indexOf(query) === 0) {
    data.priority = 8;
  } else if (data.text.toLowerCase().indexOf(query.toLowerCase()) === 0) {
    data.priority = 9;
  } else if (data.text.toLowerCase().indexOf(query.toLowerCase()) !== -1) {
    data.priority = 10;
  }

  if (data.priority > 0) {
    return data;
  }
  return null;
};

const airportSorter = (data) => {
  return data.sort((a, b) => a.priority - b.priority);
};

const config = {
  from: null,
  to: null,
};

window.searchConfig = config;

$(document).on("select2:open", () => {
  const selector = document.querySelector(".select2-search__field");
  selector.focus();
});

$(document).ready(function () {
  "use strict";

  let airportUrl = $("[data-airport-url]").data("airport-url");
  $("#one-way-from-option, #round-trip-from-option").change(function (e) {
    config.from = e.target.value;
  });
  $("#one-way-to-option, #round-trip-to-option").change(function (e) {
    config.to = e.target.value;
  });

  $('input[name="tabsA"]').change(function (e) {
    if (config.from) {
      $("#one-way-from-option, #round-trip-from-option")
        .val(config.from)
        .trigger("change");
    }
    if (config.to) {
      $("#one-way-to-option, #round-trip-to-option")
        .val(config.to)
        .trigger("change");
    }
  });

  $(
    ".one-way-from-option, .one-way-to-option, .round-trip-from-option, .round-trip-to-option"
  ).select2({
    placeholder: "Select a Country",
    matcher: airportMatcher,
    sorter: airportSorter,
  });

  $("#oneway-swap").on("click", function () {
    var selectedValue1 = $("#one-way-from-option").val();
    var selectedValue2 = $("#one-way-to-option").val();
    // Swap selected values
    $("#one-way-from-option").val(selectedValue2).trigger("change");
    $("#one-way-to-option").val(selectedValue1).trigger("change");
  });

  $("#round-swap").on("click", function () {
    var selectedValue1 = $("#round-trip-from-option").val();
    var selectedValue2 = $("#round-trip-to-option").val();
    // Swap selected values
    $("#round-trip-from-option").val(selectedValue2).trigger("change");
    $("#round-trip-to-option").val(selectedValue1).trigger("change");
  });
});

const oneWayDatePicker = document.getElementById("oneWayDatePicker");
oneWayDatePicker.addEventListener("focusin", function (eve) {
  oneWayDatePicker.click();
});

$(".t-datepicker").tDatePicker({
  autoClose: true,
  durationArrowTop: 200,
  formatDate: "dd-mm-yyyy",
  dateCheckIn: new Date(
    `${new Date().getFullYear()}, ${
      new Date().getMonth() + 1
    }, ${new Date().getDate()}`
  ),
  dateCheckOut: new Date(
    `${new Date().getFullYear()}, ${
      new Date().getMonth() + 1
    }, ${new Date().getDate()}`
  ),
  iconDate: "",
  titleCheckIn: $("[data-departure]").data("departure"),
  titleCheckOut: $("[data-return]").data("return"),
  limitDateRanges: 360,
  limitNextMonth: 12,
});

function oneWayTotalPassenger() {
  const totalPass = document.getElementById("passengers-oneway");
  let onewayqty = document.querySelector("#oneway-adult-input");
  let childqty = document.querySelector("#oneway-child-input");
  let infanqty = document.querySelector("#oneway-infant-input");
  let total =
    parseInt(onewayqty.value) +
    parseInt(childqty.value) +
    parseInt(infanqty.value);
  let patType = "Economy";
  let class_type = document.getElementById("class_type_one");

  let adult_input_val = document.getElementById("adult_input_one");
  adult_input_val.value = onewayqty.value;

  let infant_input_val = document.getElementById("infant_input_one");
  infant_input_val.value = infanqty.value;

  let child_input_val = document.getElementById("child_input_one");
  child_input_val.value = childqty.value;

  if (document.getElementById("economy1").checked) {
    patType = "Economy";
    class_type.value = "Y";
  } else if (document.getElementById("premiumEconomy1").checked) {
    patType = "Premium Economy";
    class_type.value = "S";
  } else if (document.getElementById("first1").checked) {
    patType = "First";
    class_type.value = "F";
  } else {
    patType = "Business";
    class_type.value = "C";
  }

  totalPass.value = `${total} Travellers, ${patType}`;

  const mainDropDown = document.querySelectorAll(".dropdown-menu");
  mainDropDown[0].classList.remove("show");
  mainDropDown[1].classList.remove("show");
  mainDropDown[2].classList.remove("show");
}

const roundDatePicker = document.getElementById("roundDatePicker");
roundDatePicker.addEventListener("focusin", function (eve) {
  roundDatePicker.click();
});

function roundTripTotalPassenger() {
  const totalPass = document.getElementById("passengers-roundTrip");

  let onewayqty = document.querySelector("#round-adult-input");
  let childqty = document.querySelector("#round-child-input");
  let infanqty = document.querySelector("#round-infant-input");
  let total =
    parseInt(onewayqty.value) +
    parseInt(childqty.value) +
    parseInt(infanqty.value);
  let patType = "Economy";

  let class_type = document.getElementById("class_type");

  let adult_input_val = document.getElementById("adult_input");
  adult_input_val.value = onewayqty.value;

  let infant_input_val = document.getElementById("infant_input");
  infant_input_val.value = infanqty.value;

  let child_input_val = document.getElementById("child_input");
  child_input_val.value = childqty.value;

  if (document.getElementById("economy2").checked) {
    patType = "Economy";
    class_type.value = "Y";
  } else if (document.getElementById("premiumEconomy2").checked) {
    patType = "Premium Economy";
    class_type.value = "S";
  } else if (document.getElementById("first2").checked) {
    patType = "First";
    class_type.value = "F";
  } else {
    patType = "Business";
    class_type.value = "C";
  }

  totalPass.value = `${total} Travellers, ${patType}`;

  const mainDropDown = document.querySelector(".drop-down-round");
  mainDropDown.classList.remove("show");
}

let r_child = 0;

function roundChildDec() {
  if (r_child > 0) {
    const dCl = document.getElementsByClassName(`ch-${r_child}`);
    // Convert HTMLCollection to array for easy iteration
    const elementsArray = Array.from(dCl);
    // Iterate over each element and remove it
    elementsArray.forEach(function (element) {
      element.remove();
    });
    r_child--;
    o_child--;
  }
}

function roundChildInc() {
  if (totalRound < 9) {
    r_child++;
    o_child++;

    let elements = document.getElementsByClassName("_child_age_");

    for (var i = 0; i < elements.length; i++) {
      let txtNewInputBox = document.createElement("div");
      txtNewInputBox.innerHTML = `<input type="text" name="chd_age[]" class="form-control bg-light border mr-3 ch-${r_child}" id="ch-${r_child}" placeholder="age">`;

      elements[i].append(txtNewInputBox);
    }
  }
}

let o_child = 0;

function oneWayChildInc() {
  if (totalOneWay < 9) {
    o_child++;
    r_child++;
    let elements = document.getElementsByClassName("_child_age_");

    for (var i = 0; i < elements.length; i++) {
      let txtNewInputBox = document.createElement("div");
      txtNewInputBox.innerHTML = `<input type="text" name="chd_age[]" class="form-control bg-light border mr-3 ch-${o_child}" id="ch-${o_child}" placeholder="age">`;

      elements[i].append(txtNewInputBox);
    }
  }
}

function oneWayChildDec() {
  if (o_child > 0) {
    const dCl = document.getElementsByClassName(`ch-${o_child}`);
    // Convert HTMLCollection to array for easy iteration
    const elementsArray = Array.from(dCl);
    // Iterate over each element and remove it
    elementsArray.forEach(function (element) {
      element.remove();
    });
    o_child--;
    r_child--;
  }
}

// script for round trip adult person

let addBtn = document.querySelector("#round-adult-plus");
let subBtn = document.querySelector("#round-adult-minus");
let qty = document.querySelector("#round-adult-input");

let roundaddBtn = document.querySelector("#round-child-plus");
let roundsubBtn = document.querySelector("#round-child-minus");
let roundqty = document.querySelector("#round-child-input");

let infantaddBtn = document.querySelector("#round-infant-plus");
let infantsubBtn = document.querySelector("#round-infant-minus");
let infantqty = document.querySelector("#round-infant-input");

var totalRound = 1;

addBtn.addEventListener("click", () => {
  if (totalRound < 9) {
    qty.value = parseInt(qty.value) + 1;
    totalRound++;
  }
});

subBtn.addEventListener("click", () => {
  if (qty.value <= 0) {
    qty.value = 0;
  } else {
    qty.value = parseInt(qty.value) - 1;
    totalRound--;
  }
});

// script for round trip child

roundaddBtn.addEventListener("click", () => {
  if (totalRound < 9) {
    roundqty.value = parseInt(roundqty.value) + 1;
    totalRound++;
  }
});

roundsubBtn.addEventListener("click", () => {
  if (roundqty.value <= 0) {
    roundqty.value = 0;
  } else {
    roundqty.value = parseInt(roundqty.value) - 1;
    totalRound--;
  }
});

// script for round trip infant

infantaddBtn.addEventListener("click", () => {
  if (totalRound < 9) {
    infantqty.value = parseInt(infantqty.value) + 1;
    totalRound++;
  }
});

infantsubBtn.addEventListener("click", () => {
  if (infantqty.value <= 0) {
    infantqty.value = 0;
  } else {
    infantqty.value = parseInt(infantqty.value) - 1;
    totalRound--;
  }
});

// script for one way trip adult person

let onewayaddBtn = document.querySelector("#oneway-adult-plus");
let onewaysubBtn = document.querySelector("#oneway-adult-minus");
let onewayqty = document.querySelector("#oneway-adult-input");

let childaddBtn = document.querySelector("#oneway-child-plus");
let childsubBtn = document.querySelector("#oneway-child-minus");
let childqty = document.querySelector("#oneway-child-input");

let plusBtn = document.querySelector("#oneway-infant-plus");
let minusBtn = document.querySelector("#oneway-infant-minus");
let infanqty = document.querySelector("#oneway-infant-input");

let totalOneWay = 1;

onewayaddBtn.addEventListener("click", () => {
  if (totalOneWay < 9) {
    totalOneWay++;
    onewayqty.value = parseInt(onewayqty.value) + 1;
  }
});

onewaysubBtn.addEventListener("click", () => {
  if (onewayqty.value <= 1) {
    onewayqty.value = 1;
  } else {
    onewayqty.value = parseInt(onewayqty.value) - 1;
    totalOneWay--;
  }
});

// script for one way trip child

childaddBtn.addEventListener("click", () => {
  if (totalOneWay < 9) {
    totalOneWay++;
    childqty.value = parseInt(childqty.value) + 1;
  }
});

childsubBtn.addEventListener("click", () => {
  if (childqty.value <= 0) {
    childqty.value = 0;
  } else {
    childqty.value = parseInt(childqty.value) - 1;
    totalOneWay--;
  }
});

// script for one way trip infant

plusBtn.addEventListener("click", () => {
  if (totalOneWay < 9) {
    totalOneWay++;
    infanqty.value = parseInt(infanqty.value) + 1;
  }
});

minusBtn.addEventListener("click", () => {
  if (infanqty.value <= 0) {
    infanqty.value = 0;
  } else {
    infanqty.value = parseInt(infanqty.value) - 1;
    totalOneWay--;
  }
});

$(document).ready(function () {
  "use strict";

  let childTotal = $("[data-child-total]").data("child-total");
  o_child += childTotal;
  r_child += childTotal;

  var maxChildCount = 5;
  var childAgeGroups = $(".child-age-group");

  $("#child-minus").click(function () {
    var childCount = parseInt($("#child-count").val());
    if (childCount > 0) {
      childCount--;
      $("#child-count").val(childCount);
      if (childCount == 0) {
        childAgeGroups.hide();
      } else {
        childAgeGroups.filter(":visible").last().hide();
      }
    }
  });

  $("#child-plus").click(function () {
    var childCount = parseInt($("#child-count").val());
    if (childCount < maxChildCount) {
      childCount++;
      $("#child-count").val(childCount);
      childAgeGroups.slice(0, childCount).show();
    }
  });

  //Tooltip
  $('[data-bs-toggle="tooltip"]').tooltip();

  $(".multicity-datepicker1").tDatePicker({
    autoClose: true,
    iconDate: '<i class="far fa-calendar-alt"></i>',
  });

  $(".multicity-datepicker2").tDatePicker({
    autoClose: true,
    iconDate: '<i class="far fa-calendar-alt"></i>',
  });
  $(".flight-status").tDatePicker({
    autoClose: true,
    limitNextMonth: 3,
    numCalendar: 1,
    dateRangesHover: false,
    iconDate: '<i class="far fa-calendar-alt"></i>',
  });

  for (
    var e = document.querySelectorAll(".disable-autohide .custom-select"),
      t = 0;
    t < e.length;
    t++
  )
    e[t].addEventListener("click", function (e) {
      e.stopPropagation();
    });

  for (
    var e = document.querySelectorAll(".travellers-dropdown .dropdown-menu"),
      t = 0;
    t < e.length;
    t++
  )
    e[t].addEventListener("click", function (e) {
      e.stopPropagation();
    });

  //modify search for modal date picker

  $("#checkLFD").on("shown.bs.modal", function (e) {
    let firstElement = $(".t-datepicker-modal-oneway").first();

    // Get the value of the 'data-t-start' attribute using jQuery's data() method
    let tStartValue = new Date(firstElement.data("t-start"));

    $(".t-datepicker-modal-oneway").tDatePicker({
      durationArrowTop: 200,
      formatDate: "dd-mm-yyyy",
      dateCheckIn: tStartValue,
      dateCheckOut: tStartValue,
      iconDate: "",
      titleCheckIn: $("[data-departure]").data("departure"),
      titleCheckOut: $("[data-return]").data("return"),
      limitDateRanges: 360,
      limitNextMonth: 12,
    });

    // for round trip

    let roundElement = $(".t-datepicker-modal-round").first();
    let roundStart = roundElement.data("t-start");
    let roundEnd = roundElement.data("t-end");

    $(".t-datepicker-modal-round").tDatePicker({
      durationArrowTop: 200,
      formatDate: "dd-mm-yyyy",
      dateCheckIn: new Date(roundStart),
      dateCheckOut: new Date(roundEnd),
      iconDate: "",
      titleCheckIn: $("[data-departure]").data("departure"),
      titleCheckOut: $("[data-return]").data("return"),
      limitDateRanges: 360,
      limitNextMonth: 12,
    });
  });
});

function close_the_menu() {
  document.getElementById("take_it").classList.remove("show");
}
