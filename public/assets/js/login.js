$(document).ready(function () {
  $("#loginForm").on("submit", function (e) {
    e.preventDefault();
    loading();
    let user = $("#user").val();
    let password = $("#password").val();

    $.ajax({
      url: BASE_URL + "/login",
      method: "POST",
      data: {
        user: user,
        password: password,
      },
      success: function (response) {
        try {
          let res = JSON.parse(response);
          if (res.success) {
            window.location.href = BASE_URL + "/home";
          } else {
            Swal.fire({
              title: "Error",
              text: res.message,
              icon: "error",
            });
          }
        } catch (e) {
          $("#loginError").text("Error inesperado").show();
        }
      },
      error: function () {
        $("#loginError")
          .text("Error en el servidor. Intenta más tarde.")
          .show();
      },
    });
  });
});

function loading() {
  Swal.fire({
    text: "Cargando...",
    html: '<img src="assets/images/loading-blue.gif" width="200">',
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
  });
}
