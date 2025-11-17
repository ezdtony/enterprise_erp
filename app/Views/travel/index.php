<?php require_once __DIR__ . '/../partials/header.php'; ?>
<div class="container-fluid">

    <div class="container mt-4">

        <h1 class="fw-bold text-primary mb-0">Viáticos</h1>
        <h2 class="mb-4">Solicitudes de Viáticos</h2>

        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#newTravelRequest">
            Nueva Solicitud
        </button>

        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Empleado</th>
                    <th>Proyecto</th>
                    <th>Monto</th>
                    <th>Fechas</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($requests as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= $r['employee_name'] ?></td>
                        <td><?= $r['project_name'] ?></td>
                        <td>$<?= number_format($r['amount_requested'], 2) ?></td>
                        <td><?= $r['max_pay_date'] ?></td>
                        <td>
                            <span class="badge bg-<?= $r['status'] === 'approved' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($r['status']) ?>
                            </span>
                        </td>

                        <td>
                            <button class="btn btn-sm btn-info"
                                data-bs-toggle="modal"
                                data-bs-target="#travelModal"
                                onclick="loadShowForm(<?= $r['id'] ?>)">
                                Ver
                            </button>

                            <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#travelModal"
                                onclick="loadExpensesForm(<?= $r['id'] ?>)">
                                Gastos
                            </button>

                            <button class="btn btn-sm btn-success"
                                data-bs-toggle="modal"
                                data-bs-target="#travelModal"
                                onclick="loadApprovalForm(<?= $r['id'] ?>)">
                                Aprobar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<?php require_once 'modals/newRequest.php'; ?>
<script src="<?= BASE_URL ?>assets/js/modules/travel.js"></script>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>