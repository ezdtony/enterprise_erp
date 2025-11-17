<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container-fluid">
    <div class="container mt-4">

        <!-- TÍTULO PRINCIPAL -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="fw-bold text-primary mb-0">Viáticos</h1>
                <small class="text-muted fs-5">Comprobación de Gastos Registrados</small>
            </div>

            <div class="col-auto d-flex align-items-end">
                <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalRegisterExpense">
                    <i class="ti ti-plus"></i> Registrar Gasto
                </button>
            </div>
        </div>

        <!-- TABLA DE GASTOS REGISTRADOS -->
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <h4 class="mb-3 fw-semibold">
                    <i class="ti ti-wallet"></i> Gastos Registrados
                </h4>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Categoría</th>
                                <th>Empleado</th>
                                <th>Proyecto</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($expenses)): ?>
                                <?php foreach ($expenses as $e): ?>
                                    <tr>
                                        <td><?= $e->id ?></td>
                                        <td><?= $e->category_name ?></td>
                                        <td><?= $e->employee_name ?></td>
                                        <td><?= $e->project_name ?></td>

                                        <td class="fw-bold text-success">
                                            $<?= number_format($e->amount, 2) ?>
                                        </td>

                                        <td><?= $e->formatted_date ?></td>

                                        <td class="text-center">

                                            <button class="btn btn-sm btn-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalExpenseViewer"
                                                onclick="loadExpenseView(<?= $e->id ?>)">
                                                <i class="ti ti-eye"></i>
                                            </button>

                                            <button class="btn btn-sm btn-warning"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalExpenseEdit"
                                                onclick="loadExpenseEdit(<?= $e->id ?>)">
                                                <i class="ti ti-edit"></i>
                                            </button>

                                            <button class="btn btn-sm btn-danger"
                                                onclick="deleteExpense(<?= $e->id ?>)">
                                                <i class="ti ti-trash"></i>
                                            </button>

                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted">
                                        No hay gastos registrados.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    </div>
</div>


<!-- JS global -->
<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<!-- Modales del módulo de comprobación -->
<?php require_once 'modals/modalRegisterExpense.php'; ?>
<!--  -->
<script src="<?= BASE_URL ?>assets/js/modules/travelExpenses.js"></script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>