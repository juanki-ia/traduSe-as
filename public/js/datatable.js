// Verifica si DataTable ya está inicializado y destrúyelo
if ($.fn.DataTable.isDataTable('#datatable')) {
    $('#datatable').DataTable().destroy();
}

// Espera a que el documento esté listo antes de inicializar DataTable
$(document).ready(function() {
    // Inicializa el DataTable
    $('#datatable').DataTable({
        responsive: true,
        autoWidth: false,
        "language": {
            "lengthMenu":   "Mostrar " +
                `<select class="custom-select custom-select-sm form-control form-control-sm">
                    <option value="10" >10</option>
                    <option value="25" >25</option>
                    <option value="50" >50</option>
                    <option value="100" >100</option>
                    <option value="-1" >All</option>
                    </select>` + 
                    " registros por página",
            "zeroRecords": "No se encontró ningun resultado",
            "info": "Mostrando la página _PAGE_ de _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(Filtrado de _MAX_ total registros totales)",
            "search" : "Buscar",
            "paginate" : {
                "next" : "Siguiente",
                "previous" : "Anterior"
            }
        }
    });
});