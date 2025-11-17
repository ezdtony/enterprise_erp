/************************************************************
 *  INICIALIZACIONES
 ************************************************************/
$(document).ready(function () {
  // Select2
  $("#expense_category, #expense_project").select2({
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
  flatpickr("#expense_date", { dateFormat: "Y-m-d", maxDate: "today" });

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
      $("#invoice_pdf, #invoice_xml").val("");
      $("#cfdi_code").val("").prop("required", false);
    }
  });

  // Auto-fill project when request changes
  $("#expense_request").on("change", function () {
    let project_id = $(this).find(":selected").data("project-id");
    $("#expense_project").val(project_id).trigger("change");
  });

  // Switch: incluye factura
  $("#has_invoice").on("change", function () {
    if ($(this).is(":checked")) {
      $("#invoice_file_section").slideDown(200);
      $("#cfdi_code").prop("required", true); // CFDI obligatorio
      $("#invoice_pdf").prop("required", true);
    } else {
      $("#invoice_file_section").slideUp(200);
      $("#invoice_pdf, #invoice_xml").val("");
      $("#cfdi_code").val("").prop("required", false);
    }
  });

  /************************************************************
   * LISTENERS PARA INPUTS DE FACTURA
   ************************************************************/
  $("#invoice_xml").on("change", function () {
    const file = this.files[0];
    if (!file) return;

    const ext = file.name.split(".").pop().toLowerCase();
    if (ext === "xml") processXmlInvoice(file);
    else {
      Swal.fire({
        icon: "error",
        text: "Solo se permite XML para este campo.",
      });
      $(this).val("");
    }
  });

  $("#invoice_pdf").on("change", function () {
    const file = this.files[0];
    if (!file) return;

    const ext = file.name.split(".").pop().toLowerCase();
    if (ext === "pdf") processPdfInvoice(file);
    else {
      Swal.fire({
        icon: "error",
        text: "Solo se permite PDF para este campo.",
      });
      $(this).val("");
    }
  });

  /************************************************************
   * FUNCIONES PARA PROCESAR FACTURA
   ************************************************************/
  function processXmlInvoice(file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const xmlText = e.target.result;
      const parser = new DOMParser();
      const xmlDoc = parser.parseFromString(xmlText, "text/xml");

      const tfd = xmlDoc.getElementsByTagName("tfd:TimbreFiscalDigital")[0];
      const cfdiUUID = tfd ? tfd.getAttribute("UUID") : "";

      const comp = xmlDoc.getElementsByTagName("cfdi:Comprobante")[0];
      const total = comp ? comp.getAttribute("Total") : "";

      const concepto = xmlDoc.getElementsByTagName("cfdi:Concepto")[0];
      const descripcion = concepto ? concepto.getAttribute("Descripcion") : "";

      // Asignar valores
      $("#cfdi_code").val(cfdiUUID);
      if (descripcion) $("#expense_description").val(descripcion);
      if (total) {
        const anElement = AutoNumeric.getAutoNumericElement("#expense_amount");
        if (anElement) anElement.set(total); // clave para AutoNumeric
      }
    };
    reader.readAsText(file);
  }

  async function processPdfInvoice(file) {
    // Por ahora solo se indica que el PDF fue cargado
   /*  Swal.fire({
      icon: "info",
      text: "PDF cargado correctamente. CFDI solo se extrae de XML.",
    }); */
  }
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
  // Validación campos obligatorios
  if (
    !$("#expense_category").val() ||
    !$("#expense_project").val() ||
    !$("#expense_amount").val() ||
    !$("#expense_date").val() ||
    !$("#expense_request").val() ||
    !$("#expense_description").val()
  ) {
    Swal.fire({
      icon: "warning",
      text: "Todos los campos son obligatorios.",
    });
    return;
  }

  // Validación fotografía obligatoria
  if ($("#expense_photo")[0].files.length === 0) {
    Swal.fire({
      icon: "warning",
      text: "Debes subir al menos una fotografía del gasto.",
    });
    return;
  }

  // Si tiene factura
  if ($("#has_invoice").is(":checked")) {
    if ($("#invoice_pdf")[0].files.length === 0) {
      Swal.fire({ icon: "warning", text: "Debes subir el PDF de la factura." });
      return;
    }
    if ($("#cfdi_code").val().trim() === "") {
      Swal.fire({
        icon: "warning",
        text: "Debes ingresar el CFDI de la factura.",
      });
      return;
    }

    // Validación de tipo de archivo factura
    const pdfFile = $("#invoice_pdf")[0].files[0];
    const xmlFile = $("#invoice_xml")[0]?.files[0];

    if (pdfFile && pdfFile.name.split(".").pop().toLowerCase() !== "pdf") {
      Swal.fire({ icon: "warning", text: "El archivo PDF no es válido." });
      return;
    }
    if (xmlFile && xmlFile.name.split(".").pop().toLowerCase() !== "xml") {
      Swal.fire({ icon: "warning", text: "El archivo XML no es válido." });
      return;
    }
  }

  // Validación de fotos
  for (let i = 0; i < $("#expense_photo")[0].files.length; i++) {
    const file = $("#expense_photo")[0].files[i];
    const ext = file.name.split(".").pop().toLowerCase();
    if (!["jpg", "jpeg", "png"].includes(ext)) {
      Swal.fire({ icon: "warning", text: "Solo se permiten fotos JPG o PNG." });
      return;
    }
  }

  loading();

  let formData = new FormData();
  formData.append("action", "saveExpense");
  formData.append("request_id", $("#expense_request").val());
  formData.append("category_id", $("#expense_category").val());
  formData.append("project_id", $("#expense_project").val());

  let amount = $("#expense_amount").val().replace(/[$,]/g, "");
  formData.append("amount", amount);

  formData.append("expense_date", $("#expense_date").val());
  formData.append("description", $("#expense_description").val());
  formData.append("is_deductible", $("#is_deductible").is(":checked") ? 1 : 0);
  formData.append("has_invoice", $("#has_invoice").is(":checked") ? 1 : 0);
  formData.append("cfdi_code", $("#cfdi_code").val().trim());

  // Fotos
  for (let i = 0; i < $("#expense_photo")[0].files.length; i++) {
    formData.append("photos[]", $("#expense_photo")[0].files[i]);
  }

  // Facturas
  if ($("#has_invoice").is(":checked")) {
    formData.append("invoice_pdf", $("#invoice_pdf")[0].files[0]);
    if ($("#invoice_xml")[0]?.files[0]) {
      formData.append("invoice_xml", $("#invoice_xml")[0].files[0]);
    }
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
      }).then(function () {
        if (data.response) {
          $("#modalRegisterExpense").modal("hide");
          location.reload();
        }
      });
    },
    error: function () {
      Swal.close();
      Swal.fire({ icon: "error", text: "Error en el servidor." });
    },
  });
}
