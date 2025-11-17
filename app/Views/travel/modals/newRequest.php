<!-- MODAL -->
<div class="modal fade" id="newTravelRequest" tabindex="-1" aria-labelledby="newTravelRequestLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="newTravelRequestLabel">Registrar Viático</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Empleado</label>
                    <select class="form-select" id="slct-employee">
                        <option selected disabled value="">Seleccionar empleado</option>
                        <?php foreach ($getEmployees as $employee): ?>
                            <option value="<?= $employee->id ?>"><?= $employee->full_name ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Proyecto</label>
                    <select class="form-select" id="slct-project">
                        <option selected disabled value="">Seleccionar proyecto</option>
                        <?php foreach ($getProjects as $project): ?>
                            <option value="<?= $project->id ?>"><?= $project->name ?> | <?= $project->name ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Propósito</label>
                    <textarea class="form-control" id="purpose" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Monto solicitado</label>
                    <input type="text" id="amount-requested" class="form-control" placeholder="$0.00">
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha máxima de depósito</label>
                    <input type="input" id="payDate" class="form-control">
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" id="btnSaveTravelRequest">Guardar</button>
            </div>

        </div>
    </div>
</div>