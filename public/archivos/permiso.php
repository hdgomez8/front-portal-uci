<?php
ob_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Tus encabezados y estilos aquí -->
</head>

<body>
    <!-- Contenido HTML de tu informe aquí -->
</body>

</html>
<?php
// Obtener el contenido HTML almacenado en el búfer y limpiar el búfer
$html = ob_get_clean();

// Mostrar el contenido HTML en el navegador
echo $html;
