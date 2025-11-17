/************************************************************
 *  INICIALIZACIONES
 ************************************************************/
$(document).ready(function () {
  // Select2
  $("#expense_category, #expense_employee, #expense_project").select2({
    theme: "bootstrap-5",
    width: "100%",
    placeholder: "Seleccionar...",
  });

  // Money formatting
  new AutoNumeric(".autonumeric-money", {
    currencySymbol: "$",
    digitGroupSeparator: ",",
    decimalCharacter: ".",
    decimalPlaces: 2,
  });

  // Date
  flatpickr("#expense_date", { dateFormat: "Y-m-d" });

  // Reset modal
  $("#modalRegisterExpense").on("shown.bs.modal", function () {
    $("#formRegisterExpense")[0].reset();
    $("#invoice_switch_section, #invoice_file_section").hide();
    $("#is_deductible, #has_invoice").prop("checked", false);
  });

  // Switch: deducible
  $("#is_deductible").on("change", function () {
    if ($(this).is(":checked")) {
      $("#invoice_switch_section").slideDown(200);
    } else {
      $("#invoice_switch_section").slideUp(200);
      $("#invoice_file_section").slideUp(200);
      $("#has_invoice").prop("checked", false);
      $("#invoice_file").val("");
    }
  });

  // Switch: incluye factura
  $("#has_invoice").on("change", function () {
    if ($(this).is(":checked")) {
      $("#invoice_file_section").slideDown(200);
    } else {
      $("#invoice_file_section").slideUp(200);
      $("#invoice_file").val("");
    }
  });
});

/************************************************************
 * SWAL LOADING
 ************************************************************/
function loading() {
  Swal.fire({
    text: "Cargando...",
    html: '<img src="/antonio_proyects/enterprise-erp/public/assets/images/loading-blue.gif" width="200">',
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
  });
}

/************************************************************
 * GUARDAR GASTO
 ************************************************************/
function saveExpense() {
  // Validación
  if (
    !$("#expense_category").val() ||
    !$("#expense_employee").val() ||
    !$("#expense_project").val() ||
    !$("#expense_amount").val() ||
    !$("#expense_date").val() ||
    !$("#expense_description").val()
  ) {
    Swal.fire({
      icon: "warning",
      text: "Completa todos los campos obligatorios.",
    });
    return;
  }

  if ($("#has_invoice").is(":checked") && $("#invoice_file").val() == "") {
    Swal.fire({ icon: "warning", text: "Debes subir la factura." });
    return;
  }

  loading();

  let formData = new FormData();

  formData.append("action", "saveExpense");
  formData.append("request_id", $("#expense_request_id").val());
  formData.append("category_id", $("#expense_category").val());
  formData.append("employee_id", $("#expense_employee").val());
  formData.append("project_id", $("#expense_project").val());

  let amount = $("#expense_amount").val().replace(/[$,]/g, "");
  formData.append("amount", amount);

  formData.append("expense_date", $("#expense_date").val());
  formData.append("description", $("#expense_description").val());
  formData.append("is_deductible", $("#is_deductible").is(":checked") ? 1 : 0);
  formData.append("has_invoice", $("#has_invoice").is(":checked") ? 1 : 0);

  // Photos
  if ($("#expense_photo")[0].files.length > 0) {
    for (let i = 0; i < $("#expense_photo")[0].files.length; i++) {
      formData.append("photos[]", $("#expense_photo")[0].files[i]);
    }
  }

  // Invoice
  if ($("#invoice_file")[0].files.length > 0) {
    formData.append("invoice_file", $("#invoice_file")[0].files[0]);
  }

  $.ajax({
    url: "/antonio_proyects/enterprise-erp/public/travel-request-action",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (resp) {
      Swal.close();

      let data = JSON.parse(resp);

      Swal.fire({
        icon: data.response ? "success" : "error",
        text: data.message,
      });

      if (data.response) {
        $("#modalRegisterExpense").modal("hide");
        setTimeout(() => location.reload(), 600);
      }
    },

    error: function () {
      Swal.close();
      Swal.fire({ icon: "error", text: "Error en el servidor." });
    },
  });
}
