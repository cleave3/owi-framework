const BASE_URL = "http://localhost:8080/api";

async function postRequest(url, data, headers = new Headers()) {
  try {
    const res = await fetch(`${BASE_URL}/${url}`, { method: "POST", headers, body: data });
    const result = await res.json();
    return result;
  } catch ({ message: error }) {
    console.trace(error);
    toastr.error("Oops something went wrong");
  }
}

async function getRequest(url, headers = new Headers()) {
  try {
    const res = await fetch(`${BASE_URL}/${url}`, { method: "GET", headers });
    const result = await res.json();
    return result;
  } catch ({ message: error }) {
    console.trace(error);
    toastr.error("Oops something went wrong");
  }
}

function numberFormat(value) {
  const result = new Intl.NumberFormat().format(value);
  return result;
}

function formatCurrencyInput(inputs) {
  inputs.forEach(input => {
    let format = new Cleave(input, {
      numeral: true,
      numeralThousandsGroupStyle: "thousand",
    });
  });
}

const showLoader = () => document.getElementById("loader").classList.remove("d-none");
const hideLoader = () => document.getElementById("loader").classList.add("d-none");
