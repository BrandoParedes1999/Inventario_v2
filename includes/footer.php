</div><!-- /#content -->

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; CARDUMEN <?= date('Y') ?></span>
                </div>
            </div>
        </footer>

    </div><!-- /#content-wrapper -->

</div><!-- /#wrapper -->

<!-- Scroll to top -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_ASSETS ?>vendor/jquery/jquery.min.js"></script>
<script src="<?= BASE_ASSETS ?>vendor/jquery-easing/jquery.easing.min.js"></script>
<!-- CORRECCIÓN: se eliminó el espacio antes de "js/" que impedía cargar el script -->
<script src="<?= BASE_ASSETS ?>js/sb-admin-2.min.js"></script>

<script>
window.onpageshow = function (event) {
    if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
        window.location.reload();
    }
};
</script>

<?php if (!empty($_SESSION['acceso_denegado'])): unset($_SESSION['acceso_denegado']); ?>
<div class="modal fade" id="modalAccesoDenegado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h5 class="modal-title text-danger w-100">Acceso Denegado</h5>
            </div>
            <div class="modal-body">
                <p>No tienes permisos para acceder a esa sección.</p>
            </div>
            <div class="modal-footer">
                <a href="<?= BASE_URL ?>dashboard.php" class="btn btn-primary w-100">
                    Regresar al Inventario
                </a>
            </div>
        </div>
    </div>
</div>
<script>new bootstrap.Modal(document.getElementById('modalAccesoDenegado')).show();</script>
<?php endif; ?>

</body>
</html>