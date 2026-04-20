<div class="modal fade" id="modalDeudas" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg"
             style="border-radius:25px; overflow:hidden;">

            <!-- HEADER -->
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="fw-bold">
                    <i class="bi bi-wallet2 me-2"></i>
                    Cuentas por Pagar
                </h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-4">

                <!-- FILTROS -->
                <div class="row g-3 mb-3">

                    <div class="col-md-4">
                        <input type="text" id="buscarDeuda"
                               class="form-control"
                               placeholder="Buscar beneficiario o nota...">
                    </div>

                    <div class="col-md-3">
                        <select id="filtroFecha" class="form-select">
                            <option value="hoy">Hoy</option>
                            <option value="ayer">Ayer</option>
                            <option value="semana">Esta semana</option>
                            <option value="mes">Este mes</option>
                            <option value="custom">Personalizado</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <input type="date" id="fecha_inicio" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <input type="date" id="fecha_fin" class="form-control">
                    </div>

                    <div class="col-md-1">
                        <button class="btn btn-primary w-100"
                                onclick="cargarCuentas()">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                </div>

                <!-- TABLA -->
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Beneficiario</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tablaDeudas"></tbody>
                    </table>
                </div>

                <!-- PAGINACION -->
                <div class="d-flex justify-content-center mt-3" id="paginacion"></div>

            </div>
        </div>
    </div>
</div>
<script>

/* =========================
   CONFIG GLOBAL
========================= */
let paginaActual = 1;
const limit = 10;
let debounceTimer = null;

/* =========================
   FECHAS
========================= */
function obtenerFechas(tipo) {

    const hoy = new Date();
    let inicio = '', fin = '';

    if (tipo === 'hoy') {
        inicio = fin = hoy.toISOString().split('T')[0];
    }

    if (tipo === 'ayer') {
        const ayer = new Date();
        ayer.setDate(hoy.getDate() - 1);
        inicio = fin = ayer.toISOString().split('T')[0];
    }

    if (tipo === 'semana') {
        const first = new Date();
        first.setDate(hoy.getDate() - hoy.getDay());
        inicio = first.toISOString().split('T')[0];
        fin = hoy.toISOString().split('T')[0];
    }

    if (tipo === 'mes') {
        const first = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        inicio = first.toISOString().split('T')[0];
        fin = hoy.toISOString().split('T')[0];
    }

    if (tipo === 'custom') {
        inicio = document.getElementById('fecha_inicio').value;
        fin    = document.getElementById('fecha_fin').value;
    }

    return {inicio, fin};
}

/* =========================
   LOADER
========================= */
function mostrarLoader() {
    document.getElementById('tablaDeudas').innerHTML = `
        <tr>
            <td colspan="5" class="text-center text-muted py-4">
                <div class="spinner-border spinner-border-sm me-2"></div>
                Cargando...
            </td>
        </tr>
    `;
}

/* =========================
   CARGAR CUENTAS
========================= */
function cargarCuentas() {

    const busqueda = document.getElementById('buscarDeuda').value;
    const tipoFecha = document.getElementById('filtroFecha').value;

    const fechas = obtenerFechas(tipoFecha);
    const offset = (paginaActual - 1) * limit;

    mostrarLoader();

    fetch(`/cfsistem/app/controllers/egresosController.php?action=listarCuentasPorPagar&busqueda=${encodeURIComponent(busqueda)}&fecha_inicio=${fechas.inicio}&fecha_fin=${fechas.fin}&limit=${limit}&offset=${offset}`)
    .then(res => res.json())
    .then(res => {

        if (!res.success) {
            console.error(res);
            return;
        }

        renderTabla(res.data);
        renderPaginacion(res.total);

    })
    .catch(err => {
        console.error('ERROR:', err);
    });
}

/* =========================
   TABLA
========================= */
function renderTabla(data) {

    const tbody = document.getElementById('tablaDeudas');
    tbody.innerHTML = '';

    if (!data || data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    Sin resultados
                </td>
            </tr>
        `;
        return;
    }

    data.forEach(d => {

        tbody.innerHTML += `
            <tr class="align-middle">
                <td>${d.id}</td>
                <td>${d.beneficiario}</td>
                <td class="fw-bold text-primary">$${parseFloat(d.monto_total).toFixed(2)}</td>
                <td>${d.fecha_registro}</td>
                <td>
                    <button class="btn btn-success btn-sm px-3"
                        onclick="saldarDeuda(${d.id})">
                        Saldar
                    </button>
                </td>
            </tr>
        `;
    });
}

/* =========================
   PAGINACIÓN
========================= */
function renderPaginacion(total) {

    const totalPaginas = Math.ceil(total / limit);
    const cont = document.getElementById('paginacion');

    cont.innerHTML = '';

    if (totalPaginas <= 1) return;

    for (let i = 1; i <= totalPaginas; i++) {

        cont.innerHTML += `
            <button class="btn btn-sm ${i === paginaActual ? 'btn-primary' : 'btn-light'} me-1"
                onclick="paginaActual=${i}; cargarCuentas();">
                ${i}
            </button>
        `;
    }
}

/* =========================
   SALDAR DEUDA
========================= */
async function saldarDeuda(id) {

    await ensureSwal();

    Swal.fire({
        title: '¿Saldar deuda?',
        text: 'Esta acción marcará la deuda como pagada',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, saldar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {

        if (!result.isConfirmed) return;

        fetch(`/cfsistem/app/controllers/egresosController.php?action=saldarDeuda&id=${id}`)
        .then(res => res.json())
        .then(res => {

            if (res.success) {

                Swal.fire({
                    icon: 'success',
                    title: 'Deuda saldada',
                    timer: 1200,
                    showConfirmButton: false
                });

                cargarCuentas();

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'No se pudo saldar'
                });
            }

        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Error de conexión', 'error');
        });

    });
}

/* =========================
   BUSCADOR CON DEBOUNCE
========================= */
document.getElementById('buscarDeuda').addEventListener('input', () => {

    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        paginaActual = 1;
        cargarCuentas();
    }, 400);
});

/* =========================
   CAMBIO DE FECHA
========================= */
document.getElementById('filtroFecha').addEventListener('change', () => {
    paginaActual = 1;
    cargarCuentas();
});

/* =========================
   FECHAS PERSONALIZADAS
========================= */
document.getElementById('fecha_inicio').addEventListener('change', () => {
    if (document.getElementById('filtroFecha').value === 'custom') {
        cargarCuentas();
    }
});

document.getElementById('fecha_fin').addEventListener('change', () => {
    if (document.getElementById('filtroFecha').value === 'custom') {
        cargarCuentas();
    }
});

/* =========================
   ABRIR MODAL
========================= */
function abrirModalDeudas() {
    paginaActual = 1;
    cargarCuentas();
    new bootstrap.Modal(document.getElementById('modalDeudas')).show();
}

</script>