$(document).ready(function () {
  flatpickr("#payDate", {
    dateFormat: "Y-m-d", // Formato compatible con MySQL
    altInput: true, // Muestra un formato bonito
    altFormat: "d/m/Y", // Formato que ve el usuario
    allowInput: true,
    locale: "es",
  });

  const amountMask = new AutoNumeric("#amount-requested", {
    currencySymbol: "$",
    decimalCharacter: ".",
    digitGroupSeparator: ",",
    decimalPlaces: 2,
  });

  $("#slct-employee").select2({
    theme: "bootstrap-5",
    placeholder: "Buscar empleado...",
    allowClear: true,
    width: "100%",
  });

  $("#slct-project").select2({
    theme: "bootstrap-5",
    placeholder: "Buscar empleado...",
    allowClear: true,
    width: "100%",
  });

  $("#btnSaveTravelRequest").on("click", function () {
    let employee_id = $("#slct-employee").val();
    let project_id = $("#slct-project").val();
    let purpose = $("#purpose").val();
    let amount_requested = $("#amount-requested").val();
    let payDate = $("#payDate").val();

    console.log(amount_requested);
    // Validación de campos obligatorios
    if (
      !employee_id ||
      !project_id ||
      !purpose ||
      !amount_requested ||
      !payDate
    ) {
      Swal.fire({
        title: "Atención!",
        text: "Todos los campos son obligatorios",
        icon: "warning",
      });
      return; // detener ejecución si falta algún campo
    }
    loading(); // tu función de carga

    $.ajax({
      url: "/antonio_proyects/enterprise-erp/public/travel-request-action",
      type: "POST",
      data: {
        action: "saveTravelRequest",
        employee_id: employee_id,
        project_id: project_id,
        purpose: purpose,
        amount_requested: amount_requested,
        payDate: payDate,
      },
      success: function (response) {
        var data = JSON.parse(response);
        //console.log(data);

        if (data.response) {
          Swal.fire({
            title: "Hecho!",
            text: data.message,
            icon: "success",
          });
        } else {
          Swal.fire({
            title: "Atención!",
            text: data.message,
            icon: "error",
          });
        }
      },
      error: function (xhr, status, error) {
        Swal.fire("Error", "No se pudo cargar el modal: " + error, "error");
      },
    });
  });
});

function loading() {
  Swal.fire({
    text: "Cargando...",
    html: '<img src="public/assets/images/loading-blue.gif" width="300" height="300">',
    allowOutsideClick: false,
    allowEscapeKey: false,
    showCloseButton: false,
    showCancelButton: false,
    showConfirmButton: false,
  });
}
