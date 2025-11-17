<div class="container-fluid">
    <!-- Page Title -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold text-primary mb-0">Dashboard</h1>
            <p class="text-muted">Resumen general de tu sistema ERP</p>
        </div>
    </div>

    <!-- Cards -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-muted">Empleados</h5>
                    <h2 class="fw-bold text-dark">24</h2>
                    <p class="text-success mb-0">+2 nuevos este mes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-muted">Pagos procesados</h5>
                    <h2 class="fw-bold text-dark">$58,400</h2>
                    <p class="text-danger mb-0">-3% respecto al mes pasado</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-muted">Proyectos activos</h5>
                    <h2 class="fw-bold text-dark">8</h2>
                    <p class="text-success mb-0">+1 nuevo proyecto</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-muted">Reportes generados</h5>
                    <h2 class="fw-bold text-dark">14</h2>
                    <p class="text-primary mb-0">+4 este mes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Placeholder -->
    <div class="row mt-5">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Actividad de pagos</h5>
                    <div id="chart"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Avisos recientes</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">✔ Nómina procesada correctamente.</li>
                        <li class="list-group-item">⚠️ Nuevo empleado pendiente de aprobación.</li>
                        <li class="list-group-item">💼 Reporte de gastos disponible.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script de ejemplo para ApexCharts -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var options = {
            chart: {
                type: "area",
                height: 200,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: "Pagos",
                data: [12000, 15000, 13000, 17000, 14000, 18000, 16000]
            }],
            xaxis: {
                categories: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul"]
            },
            colors: ["#5D87FF"]
        };
        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    });
</script>