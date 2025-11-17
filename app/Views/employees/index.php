<?php require_once __DIR__ . '/../partials/header.php'; ?>
<div class="container-fluid">
      <!-- Page Title -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold text-primary mb-0">Colaboradores</h1>
            <p class="text-muted">Gestión de Colaboradores</p>
        </div>
    </div>

    <div class="container mt-4">
        <h2 class="mb-4">Lista de Empleados</h2>
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre completo</th>
                    <th>Puesto</th>
                    <th>Departamento</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($employees)): ?>
                    <?php foreach ($employees as $emp): ?>
                        <tr>
                            <td><?= htmlspecialchars($emp['id']) ?></td>
                            <td><?= htmlspecialchars($emp['full_name']) ?></td>
                            <td><?= htmlspecialchars($emp['position']) ?></td>
                            <td><?= htmlspecialchars($emp['department']) ?></td>
                            <td>
                                <span class="badge bg-<?= $emp['status'] === 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($emp['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay empleados registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>