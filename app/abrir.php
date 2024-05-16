<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abrir Arquivo</title>
</head>

<body>
    <script>
        async function abrir() {
            let sAbrir = await window.arquivo.abrir();
            document.write(sAbrir.toString());
        }
        abrir();
    </script>
</body>

</html>