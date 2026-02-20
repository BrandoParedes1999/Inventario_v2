<?php
include 'conexion.php';

// Obtener usuarios activos
$sql = "SELECT * FROM usuarios WHERE estatus = 0";
$result = $conexion->query($sql);
?>

<?php
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['nombre_completo']}</td>
            <td>{$row['usuario']}</td>
            <td>{$row['correo']}</td>
            <td>{$row['rol']}</td>
            <td>
              <div style='display: flex; gap: 15px; justify-content: center;'>
                <a class='icofont-ui-add text-info asignar-btn' style='cursor:pointer;' 
                   data-bs-toggle='modal' data-bs-target='#modalAsignarEquipo' 
                   data-usuario-id='{$row['id']}' 
                   data-usuario-nombre='{$row['nombre_completo']}' 
                   title='Asignar Equipos'></a>

                <a class='icofont-ui-edit text-info' data-bs-toggle='modal' data-bs-target='#editarUsuario{$row['id']}' title='Editar Usuario'></a>
                
                <a class='icofont-ui-delete text-danger' data-bs-toggle='modal' data-bs-target='#eliminarUsuario{$row['id']}' title='Eliminar Usuario'></a>
              </div>
            </td>
          </tr>";
          // Modal Editar Usuario
          echo "
          <div class='modal fade' id='editarUsuario{$row['id']}' tabindex='-1' aria-hidden='true'>
          <div class='modal-dialog modal-dialog-centered'>
          <form method='POST' action='editar_usuario.php'>
          <div class='modal-content'>
          <div class='modal-header'>
          <h5 class='modal-title'>Editar Usuario</h5>
          <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
          </div>
          <div class='modal-body'>
          <input type='hidden' name='id' value='{$row['id']}'>
          <div class='mb-3'>
          <label>Nombre Completo</label>
          <input type='text' name='nombre_completo' class='form-control' value='{$row['nombre_completo']}' required>
          </div>
          <div class='mb-3'>
          <label>Usuario</label>
          <input type='text' name='usuario' class='form-control' value='{$row['usuario']}' required>
          </div>
          <div class='mb-3'>
          <label>Correo</label>
          <input type='email' name='correo' class='form-control' value='{$row['correo']}' required>
          </div>
          <div class='mb-3'>
          <label>Rol</label>
          <select name='rol' class='form-control'>
          <option value='Admin' " . ($row['rol'] == 'Admin' ? 'selected' : '') . ">Admin</option>
          <option value='Usuario' " . ($row['rol'] == 'Usuario' ? 'selected' : '') . ">Usuario</option>
          </select>
          </div>
          <div class='mb-3'>
          <label>Estatus</label>
          <select name='estatus' class='form-control'>
          <option value='0' " . ($row['estatus'] == 0 ? 'selected' : '') . ">Activo</option>
          <option value='1' " . ($row['estatus'] == 1 ? 'selected' : '') . ">Inactivo</option>
          </select>
          </div>
          </div>
          <div class='modal-footer'>
          <button type='submit' class='btn btn-primary'>Guardar Cambios</button>
          </div>
          </div>
          </form>
          </div>
          </div>";
          // Modal Eliminar Usuario
          echo "
          <div class='modal fade' id='eliminarUsuario{$row['id']}' tabindex='-1' aria-hidden='true'>
          <div class='modal-dialog modal-dialog-centered'>
          <form method='POST' action='eliminar_usuario.php'>
          <div class='modal-content'>
          <div class='modal-header bg-danger text-white'>
          <h5 class='modal-title'>Eliminar Usuario</h5>
          <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
          </div>
          <div class='modal-body'>
          <p>¿Estás seguro de que deseas eliminar al usuario <strong>{$row['nombre_completo']}</strong>?</p>
          <input type='hidden' name='id' value='{$row['id']}'>
          </div>
          <div class='modal-footer'>
          <button type='submit' class='btn btn-danger'>Eliminar</button>
          </div>
          </div>
          </form>
          </div>
          </div>";
        }
      }
      ?>
      </tbody>
    </table>
    <!-- Modal Asignar Equipo completo -->
     <div class="modal fade" id="modalAsignarEquipo" tabindex="-1" aria-labelledby="modalAsignarEquipoLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <form method="POST" action="crear_pdf.php" enctype="multipart/form-data" id="formAsignarEquipo">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title">Asignar Equipos a <span id="nombreUsuarioModal"></span></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
              <input type="hidden" name="usuario_id" id="usuarioIdModal">
              <!-- Aquí se agregarán inputs ocultos dinamicamente con articulo_id[] -->
               <!-- Datos generales -->
                <div class="row mb-4">
                  <div class="col-md-4">
                    <label>Área</label>
                    <input type="text" name="area" id="areaInput" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label>Puesto</label>
                    <input type="text" name="puesto" id="puestoInput" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label>Fecha</label>
                    <input type="date" name="fecha" id="fechaInput" value="<?= date('Y-m-d') ?>" class="form-control" required>
                  </div>
                </div>
                
                <div class="container mt-4">
                  <!-- Computadora -->
                   <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="switchPc" name="activar_pc">
                    <label class="form-check-label" for="switchPc">¿Agregar equipo de cómputo?</label>
                  </div>
                  <div class="collapse mb-4" id="collapsePc">
                    <div class="card card-body p-3">
                      <label>Buscar N° Serie (Computadora)</label>
                      <input list="listaPc" id="buscarSeriePc" class="form-control mb-3" autocomplete="off">
                      <datalist id="listaPc">
                        <?php
                      $pcs = $conexion->query("SELECT * FROM articulo WHERE estatus = 0 AND articulo IN ('Computadora', 'Laptop') AND categoria = 'Electrónico' ");
                      while ($pc = $pcs->fetch_assoc()) {
                        echo "<option value='{$pc['numero_serie']}' data-id='{$pc['id']}' data-marca='{$pc['marca']}' data-modelo='{$pc['modelo']}'>
                        {$pc['numero_serie']} - {$pc['marca']} {$pc['modelo']}
                        </option>";
                      }
                      ?>
                      </datalist>
                      <div class="row mb-3">
                        <div class="col-md-3">
                          <label>Marca</label>
                          <input type="text" name="pc_marca" id="pcMarca" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                          <label>Modelo</label>
                          <input type="text" name="pc_modelo" id="pcModelo" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                          <label>Sistema Operativo</label>
                          <input type="text" name="pc_so" class="form-control">
                        </div>
                        <div class="col-md-3">
                          <label>N° Serie</label>
                          <input type="text" name="pc_serie" id="pcSerie" class="form-control" readonly>
                        </div>
                      </div>
                      <label>Observaciones</label>
                      <textarea name="pc_obs" class="form-control mb-3"></textarea>
                      <label>Evidencia Fotográfica</label>
                      <input type="file" name="pc_evidencia[]" class="form-control mb-4" multiple accept="image/*">
                      <hr>
                      <h6>Cargador (vinculado al equipo)</h6>
                      <div class="row mb-3">
                        <div class="col-md-4">
                          <label>Marca</label>
                          <input type="text" name="cargador_marca" id="pcCargadorMarca" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                          <label>Modelo</label>
                          <input type="text" name="cargador_modelo" id="pcCargadorModelo" class="form-control" readonly>
                        </div>
                      </div>
                      <label>Observaciones del cargador</label>
                      <textarea name="cargador_obs" class="form-control mb-3"></textarea>
                      <label>Evidencia fotográfica del cargador</label>
                      <input type="file" name="cargador_evidencia[]" class="form-control mb-3" multiple accept="image/*">
                    </div>
                  </div>
                </div>
                <div class="container mt-4">
                  <!-- Monitor -->
                   <div class="form-check form-switch mt-3 mb-3">
                    <input class="form-check-input" type="checkbox" id="switchMonitor" name="activar_monitor">
                    <label class="form-check-label" for="switchMonitor">¿Agregar monitor?</label>
                  </div>
                  <div class="collapse mb-4" id="collapseMonitor">
                    <div class="card card-body p-3">
                      <label>Buscar N° Serie (Monitor)</label>
                      <input list="listaMonitor" id="buscarSerieMonitor" class="form-control mb-3" autocomplete="off">
                      <datalist id="listaMonitor">
                        <?php
                        $mons = $conexion->query("SELECT * FROM articulo WHERE estatus=0 AND articulo='Monitor'");
                        while ($m = $mons->fetch_assoc()) {
                          echo "<option value='{$m['numero_serie']}' data-id='{$m['id']}' data-marca='{$m['marca']}' data-modelo='{$m['modelo']}'>";
                        }
                        ?>
                        </datalist>
                        <div class="row mb-3">
                          <div class="col-md-4">
                            <label>Marca</label>
                            <input type="text" name="monitor_marca" id="monitorMarca" class="form-control" readonly>
                          </div>
                          <div class="col-md-4">
                            <label>Modelo</label>
                            <input type="text" name="monitor_modelo" id="monitorModelo" class="form-control" readonly>
                          </div>
                          <div class="col-md-4">
                            <label>N° Serie</label>
                            <input type="text" name="monitor_serie" id="monitorSerie" class="form-control" readonly>
                          </div>
                        </div>
                        <label>Observaciones</label>
                        <textarea name="monitor_obs" class="form-control mb-3"></textarea>
                        <label>Evidencia Fotográfica</label>
                        <input type="file" name="monitor_evidencia[]" class="form-control mb-3" multiple accept="image/*">
                      </div>
                    </div>
                  </div>
                  <div class="container mt-4">
                    
                  <!-- Celular -->
                   <div class="form-check form-switch mt-3 mb-3">
                    <input class="form-check-input" type="checkbox" id="switchCel" name="activar_celular">
                    <label class="form-check-label" for="switchCel">¿Agregar celular?</label>
                  </div>
                  <div class="collapse mb-4" id="collapseCel">
                    <div class="card card-body p-3">
                      <label>Buscar N° Serie (Celular)</label>
                      <input list="listaCel" id="buscarSerieCel" class="form-control mb-3" autocomplete="off">
                      <datalist id="listaCel">
                        <?php
                        $cels = $conexion->query("SELECT * FROM articulo WHERE estatus=0 AND articulo='Celular'");
                        while ($c = $cels->fetch_assoc()) {
                          echo "<option value='{$c['numero_serie']}' data-id='{$c['id']}' data-marca='{$c['marca']}' data-modelo='{$c['modelo']}'>";
                        }
                        ?>
                        </datalist>
                        <div class="row mb-3">
                          <div class="col-md-3">
                            <label>Marca</label>
                            <input type="text" name="cel_marca" id="celMarca" class="form-control" readonly>
                          </div>
                          <div class="col-md-3">
                            <label>Modelo</label>
                            <input type="text" name="cel_modelo" id="celModelo" class="form-control" readonly>
                          </div>
                          <div class="col-md-3">
                            <label>N° Modelo</label>
                            <input type="text" name="cel_num_mod" class="form-control">
                          </div>
                          <div class="col-md-3">
                            <label>N° Serie</label>
                            <input type="text" name="cel_serie" id="celSerie" class="form-control" readonly>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <label>EMEI</label>
                            <input type="text" name="cel_emei" class="form-control">
                          </div>
                          <div class="col-md-6">
                            <label>Cargador</label>
                            <input type="text" name="cel_carga" class="form-control">
                          </div>
                        </div>
                        <label>Observaciones</label>
                        <textarea name="cel_obs" class="form-control mb-3"></textarea>
                        <label>Evidencia Fotográfica</label>
                        <input type="file" name="cel_evidencia[]" class="form-control mb-3" multiple accept="image/*">
                      </div>
                    </div>
                  </div>
                  <!-- Tabla de artículos asignados -->
                   <div id="tablaAsignados" class="mt-4"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Generar Responsiva</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          
          <script>
          document.addEventListener("DOMContentLoaded", function () {
            const switches = [
              { sw: 'switchPc', collapse: 'collapsePc' },
              { sw: 'switchMonitor', collapse: 'collapseMonitor' },
              { sw: 'switchCel', collapse: 'collapseCel' }
            ];
            switches.forEach(({ sw, collapse }) => {
              const cb = document.getElementById(sw);
              const section = new bootstrap.Collapse(document.getElementById(collapse), { toggle: false });
              cb.addEventListener('change', () => cb.checked ? section.show() : section.hide());
            });
            // Función para agregar inputs ocultos dinamicamente para articulo_id[]
          function agregarArticuloInput(id) {
            if (!id) return;
            const form = document.getElementById('formAsignarEquipo');
            if (form.querySelector(`input[name="articulo_id[]"][value="${id}"]`)) return; // evita duplicados
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'articulo_id[]';
            input.value = id;
            form.appendChild(input);
           }
           function quitarArticuloInput(id) {
            const form = document.getElementById('formAsignarEquipo');
            const input = form.querySelector(`input[name="articulo_id[]"][value="${id}"]`);
            if (input) input.remove();
           }
           
           // Setup búsqueda para cada equipo
           function setupBusqueda(inputId, listId, map, extraIdsToClear=[]) {
            const input = document.getElementById(inputId);
            input.addEventListener("input", function () {
              const val = this.value;
              let found = false;
              document.querySelectorAll(`#${listId} option`).forEach(opt => {
                if (opt.value === val) {
                  Object.entries(map).forEach(([campo, attr]) => {
                    const el = document.getElementById(campo);
                    if (el) el.value = attr === 'value' ? opt.value : opt.dataset[attr] || '';
                  });
                  
                  // Agregar input oculto con articulo_id[]
                agregarArticuloInput(opt.dataset.id);
                
                // Guardar id seleccionado para controlar borrado luego 
                this.dataset.selectedId = opt.dataset.id;
                found = true;
              }
            });
            if (!found) {
              
              // Limpiar campos
            Object.keys(map).forEach(id => {
              const el = document.getElementById(id);
              if (el) el.value = '';
            });
            
            // Quitar input oculto anterior
            if (this.dataset.selectedId) {
              quitarArticuloInput(this.dataset.selectedId);
              this.dataset.selectedId = '';
            }
            // Limpiar extras si los hay
            extraIdsToClear.forEach(id => {
              const el = document.getElementById(id);
              if (el) el.value = '';
            });
          }
        });
      }

      setupBusqueda("buscarSeriePc", "listaPc", {
        pcMarca: 'marca', pcModelo: 'modelo', pcSerie: 'value', pcCargadorMarca: 'marca', pcCargadorModelo: 'modelo'
      }, ['pcCargadorMarca', 'pcCargadorModelo']);

      setupBusqueda("buscarSerieMonitor", "listaMonitor", {
        monitorMarca: 'marca', monitorModelo: 'modelo', monitorSerie: 'value'
      });

      setupBusqueda("buscarSerieCel", "listaCel", {
        celMarca: 'marca', celModelo: 'modelo', celSerie: 'value'
      });

      function limpiar() {
        ["buscarSeriePc", "buscarSerieMonitor", "buscarSerieCel"].forEach(id => {
          const input = document.getElementById(id);
          if (input) {
            if (input.dataset.selectedId) {
              quitarArticuloInput(input.dataset.selectedId);
              input.dataset.selectedId = '';
            }
            input.value = '';
          }
        });

        // Limpiar tabla asignados
        const tabla = document.getElementById("tablaAsignados");
        if (tabla) tabla.innerHTML = "";

        // Limpiar switches y colapsables
        switches.forEach(({ sw, collapse }) => {
          const cb = document.getElementById(sw);
          const section = new bootstrap.Collapse(document.getElementById(collapse), { toggle: false });
          if (cb) cb.checked = false;
          if (section) section.hide();
        });
        // Limpiar inputs relacionados (opcional)
        const campos = ['pcMarca', 'pcModelo', 'pcSerie', 'pcCargadorMarca', 'pcCargadorModelo',
        'monitorMarca', 'monitorModelo', 'monitorSerie',
        'celMarca', 'celModelo', 'celSerie'];
        campos.forEach(id => {
          const el = document.getElementById(id);
          if (el) el.value = '';
        });
      }
      document.querySelectorAll(".asignar-btn").forEach(btn => {
        btn.addEventListener("click", () => {
          document.getElementById("usuarioIdModal").value = btn.dataset.usuarioId;
          document.getElementById("nombreUsuarioModal").textContent = btn.dataset.usuarioNombre;
          fetch(`get_articulos_asignados.php?usuario_id=${btn.dataset.usuarioId}`)
          .then(res => res.text())
          .then(html => document.getElementById("tablaAsignados").innerHTML = html);
          limpiar();
        });
      });
      document.getElementById("modalAsignarEquipo").addEventListener("hidden.bs.modal", limpiar);
      document.getElementById("modalAsignarEquipo").addEventListener("show.bs.modal", limpiar);
      });
      </script>