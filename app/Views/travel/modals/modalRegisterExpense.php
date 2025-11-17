<div class="modal fade" id="modalRegisterExpense" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="ti ti-receipt"></i> Registrar Gasto de Viáticos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <form id="formRegisterExpense" enctype="multipart/form-data">

                    <!-- REQUEST ID -->
                    <input type="hidden" id="expense_request_id" name="request_id">

                    <div class="row g-3">
                        <!-- Solicitud -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Solicitud</label>
                            <select id="expense_request" name="request_id" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($requests as $request): ?>
                                    <option value="<?= $request->id ?>">#SDV-<?= $request->id ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Categoría -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoría</label>
                            <select id="expense_category" name="category_id" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= $c->id ?>"><?= $c->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Empleado -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Empleado</label>
                            <select id="expense_employee" name="employee_id" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($employees as $e): ?>
                                    <option value="<?= $e->id ?>">
                                        <?= $e->first_name . " " . $e->last_name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Proyecto -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Proyecto</label>
                            <select id="expense_project" name="project_id" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($projects as $p): ?>
                                    <option value="<?= $p->id ?>"><?= $p->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Monto -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Monto</label>
                            <input type="text" id="expense_amount" name="amount"
                                class="form-control autonumeric-money" required>
                        </div>

                        <!-- Fecha -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha del gasto</label>
                            <input type="text" id="expense_date" name="expense_date"
                                class="form-control flatpickr" required>
                        </div>

                        <!-- Descripción -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción</label>
                            <textarea id="expense_description" name="description"
                                class="form-control" rows="3"
                                placeholder="Describe brevemente el gasto..." required></textarea>
                        </div>

                        <!-- Switch: ¿Es deducible? -->
                        <div class="col-md-6 mt-3">
                            <label class="form-label fw-semibold">¿Es deducible?</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_deductible">
                                <label class="form-check-label" for="is_deductible">Sí, es deducible</label>
                            </div>
                        </div>

                        <!-- Switch: ¿incluye factura? -->
                        <div class="col-md-6 mt-3" id="invoice_switch_section" style="display:none;">
                            <label class="form-label fw-semibold">¿Incluye factura?</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="has_invoice">
                                <label class="form-check-label" for="has_invoice">Sí, subir factura</label>
                            </div>
                        </div>

                        <!-- Input factura -->
                        <div class="col-12 mt-2" id="invoice_file_section" style="display:none;">
                            <label class="form-label fw-semibold">Subir factura (PDF / XML / Imagen)</label>
                            <input type="file" id="invoice_file" name="invoice_file"
                                class="form-control"
                                accept=".pdf,.xml,image/*">
                        </div>

                        <!-- Fotografías -->
                        <div class="col-12 mt-3">
                            <label class="form-label fw-semibold">Fotografías del ticket o evidencia</label>
                            <input type="file" id="expense_photo" name="photos[]" class="form-control" accept="image/*" multiple>
                        </div>

                    </div>

                </form>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                <button class="btn btn-primary" onclick="saveExpense()">
                    <i class="ti ti-device-floppy"></i> Guardar Gasto
                </button>
            </div>

        </div>
    </div>
</div>