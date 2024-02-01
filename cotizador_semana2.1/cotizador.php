<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock</title>
    <link rel="stylesheet" href="estilo2.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.all.js"></script>
<style>
    #calcular-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #ff69b4; /* Cambiar a tu color rosa preferido */
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    #calcular-container button {
        background-color: #ff2e63; /* Cambiar a tu color rosa preferido */
        color: #fff;
        border: none;
        padding: 8px 15px;
        border-radius: 3px;
        cursor: pointer;
    }
</style>


</head>

<body>
    <header>
        <div class="logo">
            <img src="Logo.jpg" alt="logo de la compania">
            <h1 class="nombre de la empresa">Liverpool</h1>
            <a href="desarrolladores.php"><button>Desarrolladores</button></a>
            <a href="cotizador.php"><button>Version 1</button></a>
        </div>
    </header>

    <section id="stock">
        <div>
            <form action='' method='post' onsubmit="return validarFormulario();">
                <p>
                    Encuentra lo que buscas:
                    <input type='text' name='busquedacodigo' pattern='[A-Za-z0-9\s]{1,20}' title='Un código válido consiste en una cadena con 1 a 20 caracteres, cada uno de los cuales es una letra o un dígito'>

                    <!-- Lista desplegable para filtrar tiendas -->
                    <select name="tienda_filtro">
                        <option value="">Todas las tiendas</option>
                        <?php
                        // Obtener la lista de tiendas únicas del archivo CSV
                        $tiendas = array();
                        $file = fopen('productos_liverpool.csv', 'r');
                        while (($data = fgetcsv($file, 1000, ',')) !== false) {
                            $tiendas[] = $data[4];
                        }
                        fclose($file);

                        // Eliminar duplicados
                        $tiendas = array_unique($tiendas);

                        // Imprimir las opciones de la lista desplegable
                        foreach ($tiendas as $tienda) {
                            echo "<option value=\"$tienda\">$tienda</option>";
                        }
                        ?>
                    </select>

                    <input type='submit' value='Buscar'>
                    <button type="submit" name="ofertas">Ofertas</button>
                    <button type="submit" name="limpiar">Limpiar</button>
                </p>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Imagen</th>
                            <th>Stock</th>
                            <th>Tienda</th>
                            <th>Empresa</th>
                            <th>Promocion</th>
                            <th>Descuento</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ruta del archivo CSV
                        $csvFile = 'productos_liverpool.csv';

                        // Inicializar la variable $codigo_producto
                        $codigo_producto = "";

                        $tienda_filtro = "";

                        // Variable para verificar si se presionó el botón "Ofertas"
                        $buscar_ofertas = false;

                        // Variable para verificar si se presionó el botón "Limpiar"
                        $limpiar = false;

                        // Verificar si se ha enviado un formulario
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            // Obtener el valor del input y la tienda seleccionada
                            $codigo_producto = isset($_POST["busquedacodigo"]) ? $_POST["busquedacodigo"] : "";

                            // Inicializa la variable $tienda_filtro
                            $tienda_filtro = "";

                            if (isset($_POST["tienda_filtro"])) {
                                $tienda_filtro = $_POST["tienda_filtro"];
                            }

                            // Verificar si se presionó el botón "Ofertas"
                            if (isset($_POST['ofertas'])) {
                                $buscar_ofertas = true;
                            }

                            // Verificar si se presionó el botón "Limpiar"
                            if (isset($_POST['limpiar'])) {
                                $limpiar = true;
                            }
                        }

                        // Abre el archivo CSV en modo lectura
                        $file = fopen($csvFile, 'r');

                        // Verifica si se pudo abrir el archivo
                        if ($file !== false) {
                            // Inicializar variables para la suma
                            $sumaCasillas = 0;
                            $sumaDescuento = 0;
                            $sumaPrecios = 0;

                            // Lee cada línea del archivo CSV
                            while (($data = fgetcsv($file, 1000, ',')) !== false) {
                                // Verifica si coincide el código de producto y la tienda seleccionada
                                if (
                                    (!$codigo_producto || $codigo_producto == $data[0] || strpos($data[1], $codigo_producto) !== false) &&
                                    (!$tienda_filtro || $tienda_filtro == $data[4])
                                ) {
                                    // Verifica si se deben mostrar solo las ofertas
                                    if (!$buscar_ofertas || (floatval($data[7]) > 0)) {
                                        $cantidad = 0;
                                        if (isset($_POST["cantidad_{$data[0]}"])) {
                                            $cantidad = intval($_POST["cantidad_{$data[0]}"]);
                                        }
                                        $subtotal = $cantidad * floatval($data[2]);

                                        echo "<tr>
                                                <td>{$data[0]}</td>
                                                <td>{$data[1]}</td>
                                                <td>{$data[2]}</td>
                                                <td><img src='Imagenes/{$data[0]}.jpeg' alt='Imagen del producto' height='200px'></td>
                                                <td>{$data[3]}</td>
                                                <td>{$data[4]}</td>
                                                <td>{$data[5]}</td>
                                                <td>{$data[6]}</td>
                                                <td>{$data[7]}</td>
                                                <td>
                                                    <div class='cantidad-container'>
                                                        <button type='button' class='cantidad-btn' onclick='ajustarCantidad({$data[0]}, -1)'>-</button>
                                                        <input type='number' name='cantidad_{$data[0]}' value='{$cantidad}' min='0' max='{$data[3]}' readonly>
                                                        <button type='button' class='cantidad-btn' onclick='ajustarCantidad({$data[0]}, 1)'>+</button>
                                                    </div>
                                                </td>
                                            </tr>";

                                        // Sumar a la cantidad total solo si la cantidad es mayor que 0
                                        if ($cantidad > 0) {
                                            $sumaCasillas += $cantidad;
                                            $sumaDescuento += floatval($data[8]) * $cantidad;
                                            $sumaPrecios += $subtotal;
                                        }
                                    }
                                }
                            }

                            // Cierra el archivo CSV
                            fclose($file);
                        } else {
                            echo "Error al abrir el archivo CSV.";
                        }

                        if ($limpiar) {
                            $file = fopen($csvFile, 'r');
                            while (($data = fgetcsv($file, 1000, ',')) !== false) {
                                echo "<tr>
                                        <td>{$data[0]}</td>
                                        <td>{$data[1]}</td>
                                        <td>{$data[2]}</td>
                                        <td><img src='Imagenes/{$data[0]}.jpeg' alt='Imagen del producto' height='200px'></td>
                                        <td>{$data[3]}</td>
                                        <td>{$data[4]}</td>
                                        <td>{$data[5]}</td>
                                        <td>{$data[6]}</td>
                                        <td>{$data[7]}</td>
                                        <td>
                                            <div class='cantidad-container'>
                                                <button type='button' class='cantidad-btn' onclick='ajustarCantidad({$data[0]}, -1)'>-</button>
                                                <input type='number' name='cantidad_{$data[0]}' value='0' min='0' max='{$data[3]}' readonly>
                                                <button type='button' class='cantidad-btn' onclick='ajustarCantidad({$data[0]}, 1)'>+</button>
                                            </div>
                                        </td>
                                    </tr>";
                            }
                            fclose($file);
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Botón de calcular dentro del formulario principal -->
                <input type='hidden' name='sumaCasillas' value='<?php echo $sumaCasillas; ?>'>
                <input type='hidden' name='sumaDescuento' value='<?php echo $sumaDescuento; ?>'>
                <input type='hidden' name='sumaPrecios' value='<?php echo $sumaPrecios; ?>'>
                <br><br>
                <div id="calcular-container">
                    <button type='submit' name='calcular'>Calcular</button>
                </div>
            </form>

            <?php
            // Verificar si se ha enviado el formulario y se presionó el botón "Calcular"
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calcular'])) {
                // Mostrar resultados
                echo "<p>Articulos en Carrito: $sumaCasillas</p>";
                echo "<p>Descuento: $sumaDescuento</p>";
                echo "<p>Total: $sumaPrecios</p>";

                echo "<script>
                    Swal.fire({
                        title: '¡Listo!',
                        icon: 'success',
                        text: ['Articulos en Carrito: $sumaCasillas,            Descuento: $sumaDescuento,             Total: $sumaPrecios'],
                    });
                 </script>";
            }

            // Verificar si se ha enviado el formulario y se presionó el botón "Limpiar"
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['limpiar'])) {
                // Reiniciar contadores
                $sumaCasillas = 0;
                $sumaDescuento = 0;
                $sumaPrecios = 0;
            }
            ?>
        </div>
    </section>

    <script>
        function ajustarCantidad(id, cantidad) {
            var inputCantidad = document.querySelector(`input[name='cantidad_${id}']`);
            var cantidadActual = parseInt(inputCantidad.value);

            if (cantidadActual + cantidad >= 0 && cantidadActual + cantidad <= parseInt(inputCantidad.max)) {
                inputCantidad.value = cantidadActual + cantidad;
            }
        }

        function validarFormulario() {
            return true; 
        }
    </script>

</body>

</html>
